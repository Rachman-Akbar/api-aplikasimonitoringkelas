<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTextAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use HasFactory, SoftDeletes, NormalizesTextAttributes;

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
        'status' => 'aktif',
    ];

    protected function lowercaseAttributes(): array
    {
        return ['ruangan', 'status'];
    }

    protected function trimmedAttributes(): array
    {
        return ['hari', 'tahun_ajaran', 'keterangan'];
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class);
    }
}
