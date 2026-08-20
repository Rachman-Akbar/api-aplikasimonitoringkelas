<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuruPenggantiResource extends JsonResource
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
            'jadwal_id' => $this->jadwal_id,
            'guru_asli_id' => $this->guru_asli_id,
            'guru_pengganti_id' => $this->guru_pengganti_id,
            'tanggal' => $this->tanggal,
            'status_penggantian' => $this->status_penggantian,
            'keterangan' => $this->keterangan,
            'catatan_approval' => $this->catatan_approval,
            'dibuat_oleh' => $this->dibuat_oleh,
            'disetujui_oleh' => $this->disetujui_oleh,
            'guru_asli' => $this->whenLoaded('guruAsli', function () {
                return $this->guruAsli ? [
                    'id' => $this->guruAsli->id,
                    'nama' => $this->guruAsli->nama,
                    'nip' => $this->guruAsli->nip,
                    'status' => $this->guruAsli->status,
                ] : null;
            }),
            'guru_pengganti' => $this->whenLoaded('guruPengganti', function () {
                return $this->guruPengganti ? [
                    'id' => $this->guruPengganti->id,
                    'nama' => $this->guruPengganti->nama,
                    'nip' => $this->guruPengganti->nip,
                    'status' => $this->guruPengganti->status,
                ] : null;
            }),
            'jadwal' => $this->whenLoaded('jadwal', function () {
                return $this->jadwal ? [
                    'id' => $this->jadwal->id,
                    'hari' => $this->jadwal->hari,
                    'jam_ke' => $this->jadwal->jam_ke,
                    'jam_mulai' => $this->jadwal->jam_mulai,
                    'jam_selesai' => $this->jadwal->jam_selesai,
                    'kelas' => $this->jadwal->kelas ? [
                        'id' => $this->jadwal->kelas->id,
                        'nama' => $this->jadwal->kelas->nama,
                    ] : null,
                    'mata_pelajaran' => $this->jadwal->mataPelajaran ? [
                        'id' => $this->jadwal->mataPelajaran->id,
                        'nama' => $this->jadwal->mataPelajaran->nama,
                        'kode' => $this->jadwal->mataPelajaran->kode,
                    ] : null,
                ] : null;
            }),
            'dibuat_oleh_user' => $this->whenLoaded('dibuatOleh', function () {
                return $this->dibuatOleh ? [
                    'id' => $this->dibuatOleh->id,
                    'name' => $this->dibuatOleh->name,
                    'email' => $this->dibuatOleh->email,
                    'role' => $this->dibuatOleh->role,
                ] : null;
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
