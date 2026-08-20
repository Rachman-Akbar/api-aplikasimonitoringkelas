<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IzinGuruResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'guru_id' => $this->guru_id,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'durasi_hari' => $this->durasi_hari,
            'jenis_izin' => $this->jenis_izin,
            'keterangan' => $this->keterangan ?? '',
            'file_surat' => $this->file_surat,
            'status_approval' => $this->status_approval,
            'disetujui_oleh' => $this->disetujui_oleh,
            'tanggal_approval' => $this->tanggal_approval?->format('Y-m-d H:i:s'),
            'catatan_approval' => $this->catatan_approval,
            'guru' => $this->whenLoaded('guru', function () {
                return [
                    'id' => $this->guru->id,
                    'nama' => $this->guru->nama,
                    'nip' => $this->guru->nip,
                    'status' => $this->guru->status,
                ];
            }),
            'disetujui_oleh_user' => $this->whenLoaded('disetujuiOleh', function () {
                return $this->disetujuiOleh ? [
                    'id' => $this->disetujuiOleh->id,
                    'name' => $this->disetujuiOleh->name,
                    'email' => $this->disetujuiOleh->email,
                    'role' => $this->disetujuiOleh->role,
                ] : null;
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
