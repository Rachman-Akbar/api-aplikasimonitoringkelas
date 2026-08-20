<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siswas';

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'nama',
        'email',
        'no_telp',
        'alamat',
        'jenis_kelamin',
        'tanggal_lahir',
        'foto',
        'kelas_id',
        'nama_orang_tua',
        'no_telp_orang_tua',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status' => 'string', // Adding cast for status field
    ];

    protected $attributes = [
        'status' => 'aktif',
    ];

    // Relasi: User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi: Kehadiran
    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class);
    }
}
