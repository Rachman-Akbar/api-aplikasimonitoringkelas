<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KehadiranGuru extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jadwal_id',
        'guru_id',
        'tanggal',
        'status_kehadiran',
        'waktu_datang',
        'durasi_keterlambatan',
        'keterangan',
        'diinput_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'durasi_keterlambatan' => 'integer',
    ];

    // Relasi: Jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    // Relasi: Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    // Relasi: User yang menginput
    public function diinputOleh()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    // Accessor untuk label status
    public function getStatusKehadiranLabelAttribute()
    {
        return match ($this->status_kehadiran) {
            'hadir' => 'Hadir',
            'telat' => 'Telat',
            'tidak_hadir' => 'Tidak Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            default => $this->status_kehadiran,
        };
    }
}
