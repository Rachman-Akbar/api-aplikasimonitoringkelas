<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwals';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'hari',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'tahun_ajaran',
        'ruangan',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'jam_mulai' => 'string',
        'jam_selesai' => 'string',
        'status' => 'string',
        'jam_ke' => 'integer',
    ];

    protected $attributes = [
        'status' => 'aktif', // Default status
    ];

    // Relasi: Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi: Mata Pelajaran
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    // Relasi: Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    // Relasi: Kehadiran
    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class);
    }
}
