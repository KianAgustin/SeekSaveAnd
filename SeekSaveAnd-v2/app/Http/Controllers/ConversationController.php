<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Item;
use App\Notifications\ActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $uid = $request->user()->id;

        $conversations = Conversation::with(['item.user', 'user', 'latestMessage'])
            ->withCount(['messages as unread_count' => fn ($m) => $m->whereNull('read_at')->where('sender_id', '!=', $uid)])
            ->where(function ($q) use ($uid) {
                $q->where('user_id', $uid)
                    ->orWhereHas('item', fn ($i) => $i->where('user_id', $uid));
            })
            ->orderByDesc('updated_at')
            ->get();

        return view('messages.index', compact('conversations'));
    }

    public function open(Request $request, Item $item): RedirectResponse
    {
        if ($item->user_id === $request->user()->id) {
            return back()->withErrors(['chat' => 'You cannot message yourself about your own post.']);
        }

        $conversation = Conversation::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $conversation->load(['item.user', 'user']);
        abort_unless($conversation->includes($request->user()), 403);

        $this->markRead($conversation, $request->user()->id);

        $messages = $conversation->messages()->orderBy('id')->get();
        $other = $conversation->otherParty($request->user());

        return view('messages.show', compact('conversation', 'messages', 'other'));
    }

    public function send(Request $request, Conversation $conversation)
    {
        $conversation->load(['item.user', 'user']);
        $user = $request->user();
        abort_unless($conversation->includes($user), 403);

        $data = $request->validate(['body' => ['required', 'string', 'max:1000']]);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => trim($data['body']),
        ]);
        $conversation->touch();

        // Email only for the first message of a chat (avoids spamming school mailboxes).
        if ($conversation->messages()->count() === 1) {
            try {
                $conversation->otherParty($user)->notify(new ActivityNotification(
                    'New message about "' . $conversation->item->title . '"',
                    $user->name . ' sent you a message on SeekSaveAnd.',
                    route('messages.show', $conversation)
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['id' => $message->id]);
        }

        return redirect()->route('messages.show', $conversation);
    }

    public function poll(Request $request, Conversation $conversation): JsonResponse
    {
        $conversation->load('item');
        $uid = $request->user()->id;
        abort_unless($conversation->includes($request->user()), 403);

        $this->markRead($conversation, $uid);

        $messages = $conversation->messages()
            ->where('id', '>', (int) $request->query('after', 0))
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'mine' => $m->sender_id === $uid,
                'time' => $m->created_at->timezone(config('app.timezone'))->format('g:i A'),
            ]);

        return response()->json(['messages' => $messages]);
    }

    private function markRead(Conversation $conversation, int $userId): void
    {
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->update(['read_at' => now()]);
    }
}
