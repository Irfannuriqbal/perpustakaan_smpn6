<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    public const TYPE_STUDENT = 'Siswa/i';
    public const TYPE_STAFF   = 'Guru/Staf';

    protected $fillable = [
        'no',
        'member_type',
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

    /**
     * Laravel's member guard uses this column as the "username" credential.
     */
    public function getAuthIdentifierName(): string
    {
        return 'no';
    }

    public function isStaff(): bool
    {
        return $this->member_type === self::TYPE_STAFF;
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
