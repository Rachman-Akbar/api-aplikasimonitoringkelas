<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuruPengganti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guru_pengganties';

    protected $fillable = [
        'jadwal_id',
        'tanggal',
        'guru_asli_id',
        'guru_pengganti_id',
        'status_penggantian',
        'keterangan',
        'catatan_approval',
        'disetujui_oleh',
    ];

    protected $casts = [];

    // Relasi: Jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    // Relasi: Guru Asli
    public function guruAsli()
    {
        return $this->belongsTo(Guru::class, 'guru_asli_id');
    }

    // Relasi: Guru Pengganti
    public function guruPengganti()
    {
        return $this->belongsTo(Guru::class, 'guru_pengganti_id');
    }

    // Relasi: User yang menyetujui (Kurikulum)
    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Accessor untuk label status
    public function getStatusPenggantianLabelAttribute()
    {
        return match ($this->status_penggantian) {
            'dijadwalkan' => 'Dijadwalkan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => $this->status_penggantian,
        };
    }
}
