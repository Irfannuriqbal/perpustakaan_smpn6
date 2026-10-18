<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nis',
        'member_code',
        'name',
        'gender',
        'class',
        'phone',
        'address',
        'status',
        'password',
        'photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getAuthIdentifierName(): string
    {
        return 'nis';
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
