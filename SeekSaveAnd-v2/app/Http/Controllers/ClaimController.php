<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Conversation;
use App\Models\Item;
use App\Notifications\ActivityNotification;
use App\Services\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    /**
     * Claimer fills proof form (text + optional photo + guidance choice)
     * before messaging. Works for both lost and found items.
     */
    public function store(Request $request, Item $item): RedirectResponse
    {
        $user = $request->user();

        if ($item->user_id === $user->id) {
            return back()->withErrors(['proof' => 'You cannot claim or contact your own post.']);
        }
        if ($item->resolved) {
            return back()->withErrors(['proof' => 'This item is already resolved.']);
        }

        $data = $request->validate([
            'proof' => ['required', 'string', 'min:8', 'max:1000'],
            'proof_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'claimer_wants_guidance' => ['nullable', 'boolean'],
        ], [
            'proof.required' => 'Please describe details that prove you know this item.',
            'proof.min' => 'Please write at least 8 characters of proof details.',
            'proof_photo.image' => 'Proof photo must be an image (JPG, PNG or WEBP).',
            'proof_photo.max' => 'Proof photo must be 8 MB or smaller.',
        ]);

        $claimerWantsGuidance = $request->boolean('claimer_wants_guidance');

        $proofPhotoPath = null;
        if ($request->hasFile('proof_photo')) {
            try {
                $proofPhotoPath = SupabaseStorage::upload($request->file('proof_photo'), 'claims');
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->withErrors(['proof_photo' => 'The photo could not be uploaded. Please try again.']);
            }
        }

        // Keep existing proof photo if claimer updates text only
        $existing = Claim::where('item_id', $item->id)->where('user_id', $user->id)->first();
        if (! $proofPhotoPath && $existing) {
            $proofPhotoPath = $existing->proof_photo_path;
        }

        $claim = Claim::updateOrCreate(
            ['item_id' => $item->id, 'user_id' => $user->id],
            [
                'proof' => trim($data['proof']),
                'proof_photo_path' => $proofPhotoPath,
                'claimer_wants_guidance' => $claimerWantsGuidance,
                'status' => 'pending',
            ]
        );

        // Guided if poster OR claimer requested it
        $guided = $item->wants_guidance || $claimerWantsGuidance;

        $conversation = Conversation::firstOrCreate(
            ['item_id' => $item->id, 'user_id' => $user->id],
            ['guided' => $guided]
        );

        // Update guided flag if conversation already existed
        if ($conversation->guided !== $guided) {
            $conversation->update(['guided' => $guided]);
        }

        if ($claim->wasRecentlyCreated) {
            $this->notify(
                $item->user,
                'New contact on "' . $item->title . '"',
                $user->name . ' submitted proof details for your post.',
                route('messages.show', $conversation)
            );

            if ($guided) {
                // Notify all admins
                \App\Models\User::where('role', 'admin')->each(function ($admin) use ($item, $user, $conversation) {
                    if ($admin->id === $item->user_id) {
                        return;
                    }
                    $this->notify(
                        $admin,
                        'Guided case: "' . $item->title . '"',
                        $user->name . ' contacted the poster and admin guidance was requested.',
                        route('messages.show', $conversation)
                    );
                });
            }
        }

        return redirect()
            ->route('messages.show', $conversation)
            ->with('status', $guided
                ? 'Proof submitted. This chat is guided by an admin.'
                : 'Proof submitted. You can now message the poster.');
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
            report($e);
        }
    }
}
