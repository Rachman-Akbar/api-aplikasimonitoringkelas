<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTextAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kehadiran extends Model
{
    use HasFactory, SoftDeletes, NormalizesTextAttributes;

    protected $table = 'kehadirans';

    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'tanggal',
        'status',
        'keterangan',
        'diinput_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected function lowercaseAttributes(): array
    {
        return ['status'];
    }

    protected function trimmedAttributes(): array
    {
        return ['keterangan'];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function diinputOleh()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}
