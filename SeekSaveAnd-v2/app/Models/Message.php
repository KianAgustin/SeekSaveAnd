<?php

namespace App\Models;

use App\Services\SupabaseStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['conversation_id', 'sender_id', 'body', 'photo_path', 'read_at'];

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

    public function photoUrl(): ?string
    {
        return $this->photo_path ? SupabaseStorage::url($this->photo_path) : null;
    }

    /** Number of messages sent to this user that they haven't opened yet. */
    public static function unreadCountFor(User $user): int
    {
        return static::whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->whereHas('conversation', function ($q) use ($user) {
                if ($user->isAdmin()) {
                    // Admins see unread in guided chats
                    $q->where('guided', true);
                    return;
                }
                $q->where('user_id', $user->id)
                    ->orWhereHas('item', fn ($i) => $i->where('user_id', $user->id));
            })
            ->count();
    }
}
