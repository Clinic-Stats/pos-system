<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
    ];

    // پشکنین: ئایا ئەدمینە یان نا
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ئایا دەتوانێت پارە لە کارمەندان وەربگرێت (تەسلیمات)؟ ئەدمین، کاشیر، یان کەسێک کە دەسەڵاتی receive_cash ی هەبێت
    public function canReceiveCash(): bool
    {
        return $this->isAdmin()
            || $this->role === 'cashier'
            || in_array('receive_cash', $this->permissions ?? []);
    }

    // پشکنینی دەسەڵاتێکی دیاریکراو
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return in_array($permission, $this->permissions ?? []);
    }
}