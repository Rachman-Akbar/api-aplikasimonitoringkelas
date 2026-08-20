<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GuruMengajarController extends Controller
{

    /**
     * Apply a filter so teachers only see their own jadwals when logged in as 'guru'
     */
    private function applyGuruFilter($query)
    {
        $user = request()->user();

        if ($user && isset($user->role) && $user->role === 'guru') {
            // Prefer explicit guru_id on user, fallback to user's id if needed
            if (!empty($user->guru_id)) {
                $query->where('guru_id', $user->guru_id);
            }
        }

        return $query;
    }

    /**
     * Get guru mengajar by hari and kelas
     * Endpoint: GET /api/guru-mengajar/hari/{hari}/kelas/{kelasId}
     */
    public function getByHariKelas($hari, $kelasId): JsonResponse
    {
        try {
            $query = Jadwal::with(['guru', 'mataPelajaran', 'kelas'])
                ->where('hari', $hari)
                ->where('kelas_id', $kelasId);

            // Apply guru filter if the logged-in user is a guru
            $query = $this->applyGuruFilter($query);

            $jadwals = $query->get();

            $result = $jadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'jam_ke' => $jadwal->jam_ke,
                    'mata_pelajaran' => $jadwal->mataPelajaran->nama,
                    'kode_mata_pelajaran' => $jadwal->mataPelajaran->kode,
                    'kode_guru' => $jadwal->guru->nip,
                    'nama_guru' => $jadwal->guru->nama,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'ruangan' => $jadwal->ruangan,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Guru mengajar berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data guru mengajar',
            ], 500);
        }
    }

    /**
     * Get guru tidak masuk (teachers not present) by hari and kelas with flexible filters
     * Endpoint: GET /api/guru-mengajar/tidak-masuk/hari/{hari}/kelas/{kelasId}
     * Also supports: GET /api/guru-mengajar/tidak-masuk?hari=&kelas_id=&tanggal=&status_kehadiran=
     */
    public function getGuruTidakMasuk($hari = null, $kelasId = null): JsonResponse
    {
        try {
            // Support both path params and query params
            $request = request();
            $hari = $hari ?? $request->query('hari');
            $kelasId = $kelasId ?? $request->query('kelas_id');

            $query = Jadwal::with(['guru', 'mataPelajaran', 'kelas']);

            // Filter by hari
            if ($hari) {
                $query->where('hari', $hari);
            }

            // Filter by kelas_id
            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }

            // Apply guru filter if the logged-in user is a guru
            $query = $this->applyGuruFilter($query);

            $allJadwals = $query->get();

            // For now, return all jadwal records (would need more complex logic to determine who is not present)
            $result = $allJadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'jam_ke' => $jadwal->jam_ke,
                    'mata_pelajaran' => $jadwal->mataPelajaran->nama,
                    'kode_mata_pelajaran' => $jadwal->mataPelajaran->kode,
                    'kode_guru' => $jadwal->guru->nip,
                    'nama_guru' => $jadwal->guru->nama,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'ruangan' => $jadwal->ruangan,
                    'status' => 'aktif' // placeholder status
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Guru tidak masuk berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data guru tidak masuk',
            ], 500);
        }
    }

    /**
     * Create guru mengajar (new schedule assignment)
     * Endpoint: POST /api/guru-mengajar
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
                'guru_id' => 'required|exists:gurus,id',
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
                'jam_ke' => 'required|integer|min:1',
                'ruangan' => 'nullable|string|max:20',
                'tahun_ajaran' => 'required|string|max:20',
                'status' => 'required|in:aktif,nonaktif,batal',
            ]);

            $jadwal = Jadwal::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Guru mengajar berhasil ditambahkan',
                'data' => $jadwal
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan guru mengajar',
            ], 500);
        }
    }

    /**
     * Update guru mengajar
     * Endpoint: PUT /api/guru-mengajar
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:jadwals,id',
                'kelas_id' => 'sometimes|exists:kelas,id',
                'mata_pelajaran_id' => 'sometimes|exists:mata_pelajarans,id',
                'guru_id' => 'sometimes|exists:gurus,id',
                'hari' => 'sometimes|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'jam_mulai' => 'sometimes|date_format:H:i',
                'jam_selesai' => 'sometimes|date_format:H:i|after:jam_mulai',
                'jam_ke' => 'sometimes|integer|min:1',
                'ruangan' => 'nullable|string|max:20',
                'tahun_ajaran' => 'sometimes|string|max:20',
                'status' => 'sometimes|in:aktif,nonaktif,batal',
            ]);

            $jadwalId = $validated['id'];
            unset($validated['id']);

            $jadwal = Jadwal::findOrFail($jadwalId);
            $jadwal->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Guru mengajar berhasil diupdate',
                'data' => $jadwal
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate guru mengajar',
            ], 500);
        }
    }

    /**
     * Get guru mengajar by hari and kelas (POST version)
     * Endpoint: POST /api/guru-mengajar/by-hari-kelas
     */
    public function getByHariKelasPost(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'kelas_id' => 'required|exists:kelas,id',
            ]);

            $query = Jadwal::where('hari', $validated['hari'])
                ->where('kelas_id', $validated['kelas_id'])
                ->with(['guru', 'mataPelajaran', 'kelas'])
                ->orderBy('jam_ke');

            $query = $this->applyGuruFilter($query);

            $jadwals = $query->get();

            $result = $jadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'jam_ke' => $jadwal->jam_ke,
                    'mata_pelajaran' => $jadwal->mataPelajaran->nama,
                    'kode_mata_pelajaran' => $jadwal->mataPelajaran->kode,
                    'kode_guru' => $jadwal->guru->nip,
                    'nama_guru' => $jadwal->guru->nama,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'ruangan' => $jadwal->ruangan,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Guru mengajar berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data guru mengajar',
            ], 500);
        }
    }

    /**
     * Get guru tidak masuk by hari and kelas (POST version)
     * Endpoint: POST /api/guru-mengajar/tidak-masuk
     */
    public function getGuruTidakMasukPost(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'kelas_id' => 'required|exists:kelas,id',
            ]);

            $query = Jadwal::where('hari', $validated['hari'])
                ->where('kelas_id', $validated['kelas_id'])
                ->with(['guru', 'mataPelajaran', 'kelas']);

            $query = $this->applyGuruFilter($query);

            $jadwals = $query->get();

            $result = $jadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'jam_ke' => $jadwal->jam_ke,
                    'mata_pelajaran' => $jadwal->mataPelajaran->nama,
                    'kode_mata_pelajaran' => $jadwal->mataPelajaran->kode,
                    'kode_guru' => $jadwal->guru->nip,
                    'nama_guru' => $jadwal->guru->nama,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'ruangan' => $jadwal->ruangan,
                    'status' => 'aktif' // placeholder status
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Guru tidak masuk berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data guru tidak masuk',
            ], 500);
        }
    }

    /**
     * Update guru mengajar by ID
     * Endpoint: PUT /api/guru-mengajar/{id}
     */
    public function updateById(Request $request, $id): JsonResponse
    {
        try {
            $jadwal = Jadwal::findOrFail($id);

            $validated = $request->validate([
                'kelas_id' => 'sometimes|exists:kelas,id',
                'mata_pelajaran_id' => 'sometimes|exists:mata_pelajarans,id',
                'guru_id' => 'sometimes|exists:gurus,id',
                'hari' => 'sometimes|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'jam_mulai' => 'sometimes|date_format:H:i',
                'jam_selesai' => 'sometimes|date_format:H:i|after:jam_mulai',
                'jam_ke' => 'sometimes|integer|min:1',
                'ruangan' => 'nullable|string|max:20',
                'tahun_ajaran' => 'sometimes|string|max:20',
                'status' => 'sometimes|in:aktif,nonaktif,batal',
            ]);

            $jadwal->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Guru mengajar berhasil diupdate',
                'data' => $jadwal
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate guru mengajar',
            ], 500);
        }
    }
}
