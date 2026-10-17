<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'siswa';

    protected $fillable = [
        'name',
        'username',
        'nisn',
        'phone',
        'email',
        'password',
        'raw_password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }


    // Role Helper Methods
    public function isMurid(): bool
    {
        return true;
    }

    public function isAdmin(): bool
    {
        return false;
    }

    public function isPanitia(): bool
    {
        return false;
    }

    // Relationships
    public function registration(): HasOne
    {
        return $this->hasOne(Registration::class, 'user_id');
    }
}
