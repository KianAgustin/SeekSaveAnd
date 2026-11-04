<?php

namespace App\Models;

use App\Services\SupabaseStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'description', 'category', 'location',
        'photo_path', 'resolved', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function isLost(): bool
    {
        return $this->type === 'lost';
    }

    public function isFound(): bool
    {
        return $this->type === 'found';
    }

    /** The poster and any admin can manage (edit/delete/resolve) a post. */
    public function canBeManagedBy(User $user): bool
    {
        return $user->isAdmin() || $user->id === $this->user_id;
    }

    public function photoUrl(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        // Demo images that ship with the website (public/images/samples)
        if (str_starts_with($this->photo_path, 'samples/')) {
            return asset('images/' . $this->photo_path);
        }

        return SupabaseStorage::url($this->photo_path);
    }

    /** Emoji used when an item has no photo. */
    public function icon(): string
    {
        $t = strtolower($this->title);

        return match (true) {
            (bool) preg_match('/phone|iphone/', $t) => '📱',
            (bool) preg_match('/watch/', $t) => '⌚',
            (bool) preg_match('/wallet/', $t) => '👛',
            (bool) preg_match('/\bpens?\b/', $t) => '🖊️',
            (bool) preg_match('/laptop/', $t) => '💻',
            (bool) preg_match('/key/', $t) => '🔑',
            (bool) preg_match('/hoodie|jacket|shirt/', $t), $this->category === 'Clothing' => '👕',
            (bool) preg_match('/\bid\b|lace/', $t) => '🪪',
            (bool) preg_match('/calculator/', $t) => '🧮',
            $this->category === 'Pets' => '🐾',
            $this->category === 'Documents' => '📄',
            $this->category === 'Electronics' => '🔌',
            default => '🎒',
        };
    }
}
