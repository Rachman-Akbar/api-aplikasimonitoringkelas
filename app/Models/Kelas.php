<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'tingkat',
        'jurusan',
        'wali_kelas_id',
        'kapasitas',
        'jumlah_siswa',
        'ruangan',
        'status',
    ];

    protected $casts = [
        'tingkat' => 'integer',
        'kapasitas' => 'integer',
        'jumlah_siswa' => 'integer',
        'status' => 'string', // Adding cast for status field
    ];

    protected $attributes = [
        'status' => 'aktif', // Default status
    ];

    // Relasi: Wali Kelas
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    // Relasi: Siswa dalam kelas
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    // Relasi: Jadwal kelas
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
