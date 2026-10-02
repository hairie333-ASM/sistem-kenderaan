<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'department',
        'position',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUpf(): bool
    {
        return in_array($this->role, ['upf', 'admin']);
    }

    public function isApplicant(): bool
    {
        return $this->role === 'pemohon';
    }

    public function isDriver(): bool
    {
        return $this->role === 'pemandu';
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Pentadbir Sistem (Admin)',
            'upf' => 'Pegawai UPF',
            'pemohon' => 'Pemohon (Staf/Pegawai)',
            'pemandu' => 'Pemandu',
            default => ucfirst($this->role),
        };
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(VehicleRequest::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }
}
