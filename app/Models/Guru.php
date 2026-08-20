<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTextAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use HasFactory, SoftDeletes, NormalizesTextAttributes;

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
        return ['nip', 'nama', 'email', 'no_telp', 'alamat'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
