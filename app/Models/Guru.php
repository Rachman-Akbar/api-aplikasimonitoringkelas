<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gurus';

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'email',
        'no_telp',
        'alamat',
        'jenis_kelamin',
        'tanggal_lahir',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status' => 'string', // Adding cast for status field
    ];

    protected $attributes = [
        'status' => 'aktif', // Default status
    ];

    // Relasi: User account
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Guru sebagai Wali Kelas
    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    // Relasi: Jadwal mengajar
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
