<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'item_id', 'user_id', 'guided',
        'meetup_at', 'meetup_location', 'meetup_notes', 'meetup_status',
    ];

    protected function casts(): array
    {
        return [
            'guided' => 'boolean',
            'meetup_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** The person who contacted the poster. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function includes(User $user): bool
    {
        // Poster, claimer, or any admin (for guided chats) can access
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $this->user_id || $user->id === $this->item->user_id;
    }

    public function otherParty(User $me): User
    {
        if ($me->isAdmin()) {
            // Prefer showing the claimer when admin views
            return $this->user;
        }

        return $me->id === $this->user_id ? $this->item->user : $this->user;
    }

    public function hasMeetup(): bool
    {
        return filled($this->meetup_at) || filled($this->meetup_location);
    }
}
