<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Item;
use App\Notifications\ActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    /** A student claims a FOUND item by describing proof of ownership. */
    public function store(Request $request, Item $item): RedirectResponse
    {
        $user = $request->user();

        if ($item->user_id === $user->id) {
            return back()->withErrors(['proof' => 'You cannot claim your own post.']);
        }
        if (! $item->isFound()) {
            return back()->withErrors(['proof' => 'Claims are for found items. Message the poster if you found a lost item.']);
        }
        if ($item->resolved) {
            return back()->withErrors(['proof' => 'This item is already resolved.']);
        }

        $data = $request->validate(
            ['proof' => ['required', 'string', 'min:8', 'max:1000']],
            ['proof.min' => 'Please describe proof of ownership (at least 8 characters).',
             'proof.required' => 'Please describe proof of ownership.']
        );

        $claim = Claim::updateOrCreate(
            ['item_id' => $item->id, 'user_id' => $user->id],
            ['proof' => trim($data['proof']), 'status' => 'pending']
        );

        if ($claim->wasRecentlyCreated) {
            $this->notify($item->user, 'New claim on "' . $item->title . '"',
                $user->name . ' submitted a claim for your post.', route('items.show', $item));
        }

        return back()->with('status', 'Claim submitted. The poster will review it.');
    }

    public function approve(Request $request, Claim $claim): RedirectResponse
    {
        $item = $claim->item;
        abort_unless($item->canBeManagedBy($request->user()), 403);

        $claim->update(['status' => 'approved']);
        $item->update(['resolved' => true, 'resolved_at' => now()]);

        $this->notify($claim->user, 'Your claim was approved',
            'Your claim for "' . $item->title . '" was approved. Message the poster to arrange the return.',
            route('items.show', $item));

        return back()->with('status', 'Claim approved and item marked as resolved.');
    }

    public function reject(Request $request, Claim $claim): RedirectResponse
    {
        abort_unless($claim->item->canBeManagedBy($request->user()), 403);

        $claim->update(['status' => 'rejected']);

        $this->notify($claim->user, 'Your claim was not approved',
            'Your claim for "' . $claim->item->title . '" was not approved.', route('items.show', $claim->item));

        return back()->with('status', 'Claim rejected.');
    }

    private function notify($user, string $subject, string $line, string $url): void
    {
        try {
            $user->notify(new ActivityNotification($subject, $line, $url));
        } catch (\Throwable $e) {
            report($e); // a mail problem must never break the website
        }
    }
}
