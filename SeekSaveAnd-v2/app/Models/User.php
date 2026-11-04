<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'microsoft_id',
        'campus', 'program', 'year', 'section', 'student_number', 'contact',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function profileComplete(): bool
    {
        return filled($this->campus) && filled($this->program) && filled($this->year)
            && filled($this->section) && filled($this->student_number) && filled($this->contact);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function savedItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'saved_items')->withTimestamps();
    }

    /** e.g. "BSIT 3A" */
    public function programLabel(): string
    {
        $yearNumber = $this->year ? substr($this->year, 0, 1) : '';

        return trim(($this->program ?? '') . ' ' . $yearNumber . ($this->section ?? ''));
    }
}
