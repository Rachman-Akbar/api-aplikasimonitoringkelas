<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTextAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes, NormalizesTextAttributes;

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
        'status' => 'string',
    ];

    protected $attributes = [
        'status' => 'aktif',
    ];

    protected function lowercaseAttributes(): array
    {
        return ['status'];
    }

    protected function trimmedAttributes(): array
    {
        return ['nis', 'nisn', 'nama', 'email', 'no_telp', 'alamat', 'nama_orang_tua', 'no_telp_orang_tua'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class);
    }
}
