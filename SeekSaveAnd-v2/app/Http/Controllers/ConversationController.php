<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Item;
use App\Notifications\ActivityNotification;
use App\Services\SupabaseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $uid = $user->id;

        $conversations = Conversation::with(['item.user', 'user', 'latestMessage'])
            ->withCount(['messages as unread_count' => fn ($m) => $m->whereNull('read_at')->where('sender_id', '!=', $uid)])
            ->when($user->isAdmin(), function ($q) use ($uid) {
                // Admins see guided chats + their own chats
                $q->where(function ($w) use ($uid) {
                    $w->where('guided', true)
                        ->orWhere('user_id', $uid)
                        ->orWhereHas('item', fn ($i) => $i->where('user_id', $uid));
                });
            }, function ($q) use ($uid) {
                $q->where(function ($w) use ($uid) {
                    $w->where('user_id', $uid)
                        ->orWhereHas('item', fn ($i) => $i->where('user_id', $uid));
                });
            })
            ->orderByDesc('updated_at')
            ->get();

        return view('messages.index', compact('conversations'));
    }

    /**
     * Direct open is blocked for non-owners.
     * Claimers must submit the proof form first (ClaimController::store).
     * Owners/admins can still open existing threads.
     */
    public function open(Request $request, Item $item): RedirectResponse
    {
        $user = $request->user();

        if ($item->user_id === $user->id) {
            return back()->withErrors(['chat' => 'You cannot message yourself about your own post.']);
        }

        $existing = Conversation::where('item_id', $item->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return redirect()->route('messages.show', $existing);
        }

        // Force claimer through the proof form
        return redirect()
            ->route('items.show', $item)
            ->withErrors(['proof' => 'Please fill in the contact form with proof details before messaging.']);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $conversation->load(['item.user', 'user']);
        abort_unless($conversation->includes($request->user()), 403);

        $this->markRead($conversation, $request->user()->id);

        $messages = $conversation->messages()->orderBy('id')->get();
        $other = $conversation->otherParty($request->user());

        // Claim linked to this conversation (for showing proof to poster/admin)
        $claim = \App\Models\Claim::where('item_id', $conversation->item_id)
            ->where('user_id', $conversation->user_id)
            ->first();

        return view('messages.show', compact('conversation', 'messages', 'other', 'claim'));
    }

    public function send(Request $request, Conversation $conversation)
    {
        $conversation->load(['item.user', 'user']);
        $user = $request->user();
        abort_unless($conversation->includes($user), 403);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ], [
            'photo.image' => 'Only image files are allowed.',
            'photo.max' => 'Image must be 8 MB or smaller.',
        ]);

        $body = trim($data['body'] ?? '');
        $photoPath = null;

        if ($request->hasFile('photo')) {
            try {
                $photoPath = SupabaseStorage::upload($request->file('photo'), 'messages');
            } catch (\Throwable $e) {
                report($e);

                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Photo upload failed.'], 422);
                }

                return back()->withErrors(['photo' => 'The photo could not be uploaded. Please try again.']);
            }
        }

        if ($body === '' && ! $photoPath) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Message or photo required.'], 422);
            }

            return back()->withErrors(['body' => 'Please type a message or attach a photo.']);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $body !== '' ? $body : '📷 Photo',
            'photo_path' => $photoPath,
        ]);
        $conversation->touch();

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
            return response()->json([
                'id' => $message->id,
                'body' => $message->body,
                'photo_url' => $message->photoUrl(),
                'mine' => true,
                'time' => $message->created_at->timezone(config('app.timezone'))->format('g:i A'),
            ]);
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
                'photo_url' => $m->photoUrl(),
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
