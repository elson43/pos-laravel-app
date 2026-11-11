<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function isKasir(): bool
    {
        return $this->role === 'UKasir';
    }

    public function isGudang(): bool
    {
        return $this->role === 'UGudang';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'UAdmin';
    }

    public function getDashboardUrl(): string
    {
        return match ($this->role) {
            'UKasir' => route('kasir.pos'),
            'UGudang' => route('gudang.index'),
            'UAdmin' => route('admin.dashboard'),
            default => route('login'),
        };
    }
}
