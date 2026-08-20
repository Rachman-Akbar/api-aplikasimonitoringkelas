<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTextAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use HasFactory, SoftDeletes, NormalizesTextAttributes;

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
        'status' => 'string',
    ];

    protected $attributes = [
        'status' => 'aktif',
    ];

    protected function lowercaseAttributes(): array
    {
        return ['nama', 'jurusan', 'ruangan', 'status'];
    }

    protected function caseInsensitiveUniqueAttributes(): array
    {
        return ['nama'];
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
