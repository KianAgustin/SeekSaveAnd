<?php

namespace App\Models;

use App\Services\SupabaseStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Claim extends Model
{
    protected $fillable = [
        'item_id', 'user_id', 'proof', 'proof_photo_path',
        'claimer_wants_guidance', 'status',
    ];

    protected function casts(): array
    {
        return [
            'claimer_wants_guidance' => 'boolean',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function proofPhotoUrl(): ?string
    {
        return $this->proof_photo_path
            ? SupabaseStorage::url($this->proof_photo_path)
            : null;
    }
}
