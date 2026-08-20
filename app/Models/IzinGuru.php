<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTextAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IzinGuru extends Model
{
    use HasFactory, SoftDeletes, NormalizesTextAttributes;

    protected $table = 'izin_gurus';

    protected $fillable = [
        'guru_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
        'jenis_izin',
        'keterangan',
        'file_surat',
        'status_approval',
        'disetujui_oleh',
        'tanggal_approval',
        'catatan_approval',
    ];

    protected $casts = [
        'tanggal_approval' => 'datetime',
        'durasi_hari' => 'integer',
    ];

    protected function lowercaseAttributes(): array
    {
        return ['jenis_izin', 'status_approval'];
    }

    protected function trimmedAttributes(): array
    {
        return ['keterangan', 'file_surat', 'catatan_approval'];
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function getJenisIzinLabelAttribute()
    {
        return match ($this->jenis_izin) {
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'cuti' => 'Cuti',
            'dinas_luar' => 'Dinas Luar',
            'lainnya' => 'Lainnya',
            default => $this->jenis_izin,
        };
    }

    public function getStatusApprovalLabelAttribute()
    {
        return match ($this->status_approval) {
            'pending' => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => $this->status_approval,
        };
    }
}
