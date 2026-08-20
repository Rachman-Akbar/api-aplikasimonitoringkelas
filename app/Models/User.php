<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'guru_id',
        'kelas_id',
        'foto',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'role' => 'string',
        ];
    }

    protected $attributes = [
        'role' => 'siswa',
    ];

    // Relasi: Guru (jika role = guru)
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    // Relasi: Kelas (jika role = siswa)
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi: Data Siswa lengkap
    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKepsek(): bool
    {
        return $this->role === 'kepsek';
    }

    public function isKurikulum(): bool
    {
        return $this->hasRole('kurikulum');
    }

    public function isGuru(): bool
    {
        return $this->hasRole('guru');
    }

    public function isSiswa(): bool
    {
        return $this->hasRole('siswa');
    }

    /**
     * Determine if the user can access the admin panel (Filament).
     * 
     * PENTING: Hanya role 'admin' yang boleh akses web panel.
     * Role lain (guru, siswa, kepsek, kurikulum) HANYA bisa via aplikasi mobile.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Strict: Only admin role can access web panel
        if ($this->role !== 'admin') {
            Log::warning('Blocked non-admin panel access attempt', [
                'user_id' => $this->id,
                'email' => $this->email,
                'role' => $this->role
            ]);
            return false;
        }

        return true;
    }
}
