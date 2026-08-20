<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTextAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuruPengganti extends Model
{
    use HasFactory, SoftDeletes, NormalizesTextAttributes;

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

    protected function lowercaseAttributes(): array
    {
        return ['status_penggantian'];
    }

    protected function trimmedAttributes(): array
    {
        return ['keterangan', 'catatan_approval'];
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function guruAsli()
    {
        return $this->belongsTo(Guru::class, 'guru_asli_id');
    }

    public function guruPengganti()
    {
        return $this->belongsTo(Guru::class, 'guru_pengganti_id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function getStatusPenggantianLabelAttribute()
    {
        return match ($this->status_penggantian) {
            'dijadwalkan' => 'Dijadwalkan',
            'selesai' => 'Selesai',
            'tidak_hadir' => 'Tidak Hadir',
            'dibatalkan' => 'Dibatalkan',
            default => $this->status_penggantian,
        };
    }
}
