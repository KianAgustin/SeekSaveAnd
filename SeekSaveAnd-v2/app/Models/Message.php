<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['conversation_id', 'sender_id', 'body', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /** Number of messages sent to this user that they haven't opened yet. */
    public static function unreadCountFor(User $user): int
    {
        return static::whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->whereHas('conversation', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('item', fn ($i) => $i->where('user_id', $user->id));
            })
            ->count();
    }
}
