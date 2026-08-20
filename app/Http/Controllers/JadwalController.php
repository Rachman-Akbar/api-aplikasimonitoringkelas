<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JadwalController extends Controller
{
    /**
     * Display a listing of jadwal.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Jadwal::with(['kelas', 'mataPelajaran', 'guru']);

            if ($request->filled('kelas_id')) {
                $query->where('kelas_id', $request->integer('kelas_id'));
            }

            if ($request->filled('guru_id')) {
                $query->where('guru_id', $request->integer('guru_id'));
            }

            if ($request->filled('hari')) {
                $query->where('hari', $request->string('hari'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->string('status'));
            }

            $jadwals = $query->orderBy('hari')->orderBy('jam_ke')->orderBy('jam_mulai')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal berhasil diambil',
                'data' => $jadwals
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal',
            ], 500);
        }
    }

    /**
     * Display the specified jadwal.
     */
    public function show($id): JsonResponse
    {
        try {
            $jadwal = Jadwal::with(['kelas.siswas', 'mataPelajaran', 'guru'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail jadwal berhasil diambil',
                'data' => $jadwal
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Store a newly created jadwal.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
                'guru_id' => 'required|exists:gurus,id',
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'jam_ke' => 'required|integer|min:1',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
                'ruangan' => 'nullable|string|max:20',
                'tahun_ajaran' => 'nullable|string|max:20',
                'status' => 'required|in:aktif,nonaktif,batal',
            ]);

            $jadwal = Jadwal::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil ditambahkan',
                'data' => $jadwal
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan jadwal',
            ], 500);
        }
    }

    /**
     * Update the specified jadwal.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $jadwal = Jadwal::findOrFail($id);

            $validated = $request->validate([
                'kelas_id' => 'sometimes|exists:kelas,id',
                'mata_pelajaran_id' => 'sometimes|exists:mata_pelajarans,id',
                'guru_id' => 'sometimes|exists:gurus,id',
                'hari' => 'sometimes|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'jam_ke' => 'sometimes|integer|min:1',
                'jam_mulai' => 'sometimes|date_format:H:i',
                'jam_selesai' => 'sometimes|date_format:H:i|after:jam_mulai',
                'ruangan' => 'nullable|string|max:20',
                'tahun_ajaran' => 'nullable|string|max:20',
                'status' => 'sometimes|in:aktif,nonaktif,batal',
            ]);

            $jadwal->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diupdate',
                'data' => $jadwal
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate jadwal',
            ], 500);
        }
    }

    /**
     * Remove the specified jadwal.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $jadwal = Jadwal::findOrFail($id);
            $jadwal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal',
            ], 500);
        }
    }

    /**
     * Get jadwal by kelas.
     */
    public function byKelas($kelasId): JsonResponse
    {
        try {
            $jadwals = Jadwal::where('kelas_id', $kelasId)
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal kelas berhasil diambil',
                'data' => $jadwals
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal kelas',
            ], 500);
        }
    }

    /**
     * Get jadwal by guru with optional filters.
     * Endpoint: GET /api/jadwal/guru/{guruId}?hari=&kelas_id=
     */
    public function byGuru($guruId, Request $request = null): JsonResponse
    {
        try {
            $query = Jadwal::where('guru_id', $guruId)
                ->with(['kelas', 'mataPelajaran']);

            // Apply optional filters
            if ($request && $request->filled('hari')) {
                $query->where('hari', $request->hari);
            }

            if ($request && $request->filled('kelas_id')) {
                $query->where('kelas_id', $request->kelas_id);
            }

            $jadwals = $query->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal guru berhasil diambil',
                'data' => $jadwals
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal guru',
            ], 500);
        }
    }

    /**
     * Get jadwal by hari.
     */
    public function byHari($hari): JsonResponse
    {
        try {
            $jadwals = Jadwal::where('hari', $hari)
                ->with(['kelas', 'mataPelajaran', 'guru'])
                ->orderBy('jam_mulai')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal hari ' . $hari . ' berhasil diambil',
                'data' => $jadwals
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal',
            ], 500);
        }
    }

    /**
     * Get jadwal detail by kelas dan hari (jam ke, mata pelajaran, kode guru, nama guru).
     * Endpoint: GET /api/jadwal/kelas/{kelasId}/hari/{hari}
     */
    public function getJadwalByKelasHari($kelasId, $hari): JsonResponse
    {
        try {
            $jadwals = Jadwal::where('kelas_id', $kelasId)
                ->where('hari', $hari)
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('jam_ke')
                ->orderBy('jam_mulai')
                ->get()
                ->filter(function ($jadwal) {
                    // Filter out records with null relations
                    return $jadwal->mataPelajaran && $jadwal->guru;
                });

            // Format response sesuai kebutuhan
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
                    'tahun_ajaran' => $jadwal->tahun_ajaran,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal',
            ], 500);
        }
    }

    /**
     * Get jadwal dengan info guru, mapel, tahun ajaran, jam ke by hari dan kelas.
     * Endpoint: GET /api/jadwal/detail/kelas/{kelasId}/hari/{hari}
     */
    public function getJadwalDetailByKelasHari($kelasId, $hari): JsonResponse
    {
        try {
            $jadwals = Jadwal::where('kelas_id', $kelasId)
                ->where('hari', $hari)
                ->with(['mataPelajaran', 'guru', 'kelas'])
                ->orderBy('jam_ke')
                ->orderBy('jam_mulai')
                ->get()
                ->filter(function ($jadwal) {
                    // Filter out records with null relations
                    return $jadwal->mataPelajaran && $jadwal->guru && $jadwal->kelas;
                });

            // Format response
            $result = $jadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'nama_guru' => $jadwal->guru->nama,
                    'nip_guru' => $jadwal->guru->nip,
                    'mata_pelajaran' => $jadwal->mataPelajaran->nama,
                    'kode_mapel' => $jadwal->mataPelajaran->kode,
                    'tahun_ajaran' => $jadwal->tahun_ajaran,
                    'jam_ke' => $jadwal->jam_ke,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'hari' => $jadwal->hari,
                    'kelas' => $jadwal->kelas->nama,
                    'ruangan' => $jadwal->ruangan,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Detail jadwal berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail jadwal',
            ], 500);
        }
    }

    /**
     * Filter kelas by hari - untuk Kurikulum Activity
     * Endpoint: POST /api/jadwal/filter/kelas-by-hari
     */
    public function filterKelasByHari(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            ]);

            // Ambil kelas yang memiliki jadwal di hari tersebut
            $kelasIds = Jadwal::where('hari', $validated['hari'])
                ->distinct()
                ->pluck('kelas_id');

            $kelas = \App\Models\Kelas::whereIn('id', $kelasIds)->get();

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil diambil',
                'data' => $kelas
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kelas',
            ], 500);
        }
    }

    /**
     * Filter guru by hari dan kelas - untuk Kurikulum Activity
     * Endpoint: POST /api/jadwal/filter/guru-by-hari-kelas
     */
    public function filterGuruByHariKelas(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'kelas_id' => 'required|exists:kelas,id',
            ]);

            // Ambil guru yang mengajar di hari dan kelas tersebut
            $guruIds = Jadwal::where('hari', $validated['hari'])
                ->where('kelas_id', $validated['kelas_id'])
                ->distinct()
                ->pluck('guru_id');

            $gurus = \App\Models\Guru::whereIn('id', $guruIds)->get();

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil diambil',
                'data' => $gurus
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data guru',
            ], 500);
        }
    }

    /**
     * Filter mata pelajaran by hari, kelas, dan guru - untuk Kurikulum Activity
     * Endpoint: POST /api/jadwal/filter/mapel-by-hari-kelas-guru
     */
    public function filterMapelByHariKelasGuru(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'kelas_id' => 'required|exists:kelas,id',
                'guru_id' => 'required|exists:gurus,id',
            ]);

            // Ambil mata pelajaran yang diajarkan guru di hari dan kelas tersebut
            $mapelIds = Jadwal::where('hari', $validated['hari'])
                ->where('kelas_id', $validated['kelas_id'])
                ->where('guru_id', $validated['guru_id'])
                ->distinct()
                ->pluck('mata_pelajaran_id');

            $mataPelajarans = \App\Models\MataPelajaran::whereIn('id', $mapelIds)->get();

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil diambil',
                'data' => $mataPelajarans
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mata pelajaran',
            ], 500);
        }
    }

    /**
     * Get jam_ke otomatis berdasarkan guru, mapel, dan kelas
     * Endpoint: POST /api/jadwal/get-jam-ke
     */
    public function getJamKe(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'kelas_id' => 'required|exists:kelas,id',
                'guru_id' => 'required|exists:gurus,id',
                'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            ]);

            $jadwal = Jadwal::where('hari', $validated['hari'])
                ->where('kelas_id', $validated['kelas_id'])
                ->where('guru_id', $validated['guru_id'])
                ->where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
                ->first();

            if ($jadwal) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jam ke berhasil diambil',
                    'data' => [
                        'jam_ke' => $jadwal->jam_ke,
                        'jam_mulai' => $jadwal->jam_mulai,
                        'jam_selesai' => $jadwal->jam_selesai,
                        'jadwal_id' => $jadwal->id,
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak ditemukan',
                ], 404);
            }
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jam ke',
            ], 500);
        }
    }

    /**
     * Filter jadwal by hari dan kelas untuk SiswaActivity
     * Return: jam_ke, mata_pelajaran, kode_guru (NIP), nama_guru
     * Endpoint: POST /api/jadwal/by-hari-kelas
     */
    public function filterJadwalByHariKelas(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'kelas_id' => 'required|exists:kelas,id',
            ]);

            $jadwals = Jadwal::where('hari', $validated['hari'])
                ->where('kelas_id', $validated['kelas_id'])
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('jam_ke')
                ->orderBy('jam_mulai')
                ->get()
                ->filter(function ($jadwal) {
                    // Filter out records with null relations
                    return $jadwal->mataPelajaran && $jadwal->guru;
                });

            // Format response: jam_ke, mata_pelajaran, kode_guru (NIP), nama_guru
            $result = $jadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'jam_ke' => $jadwal->jam_ke,
                    'mata_pelajaran' => $jadwal->mataPelajaran->nama,
                    'kode_guru' => $jadwal->guru->nip,
                    'nama_guru' => $jadwal->guru->nama,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal',
            ], 500);
        }
    }

    /**
     * Filter jadwal dengan fleksibilitas (hari dan/atau kelas bisa null untuk menampilkan semua)
     * Endpoint: POST /api/jadwal/filter-flexible
     */
    public function filterJadwalFlexible(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hari' => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'kelas_id' => 'nullable|exists:kelas,id',
            ]);

            $query = Jadwal::with(['mataPelajaran', 'guru', 'kelas']);

            // Apply filter hanya jika parameter tersedia
            if (!empty($validated['hari'])) {
                $query->where('hari', $validated['hari']);
            }

            if (!empty($validated['kelas_id'])) {
                $query->where('kelas_id', $validated['kelas_id']);
            }

            $jadwals = $query->orderBy('hari')
                ->orderBy('jam_ke')
                ->orderBy('jam_mulai')
                ->get()
                ->filter(function ($jadwal) {
                    // Filter out records with null relations
                    return $jadwal->mataPelajaran && $jadwal->guru && $jadwal->kelas;
                });

            // Format response
            $result = $jadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'jam_ke' => $jadwal->jam_ke,
                    'mata_pelajaran' => $jadwal->mataPelajaran->nama,
                    'kode_guru' => $jadwal->guru->nip,
                    'nama_guru' => $jadwal->guru->nama,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'hari' => $jadwal->hari,
                    'kelas' => $jadwal->kelas->nama,
                    'ruangan' => $jadwal->ruangan,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal',
            ], 500);
        }
    }

    /**
     * Filter jadwal detail dengan fleksibilitas (untuk Kepsek dan Admin)
     * Endpoint: POST /api/jadwal/filter-flexible-detail
     */
    public function filterJadwalFlexibleDetail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'hari' => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'kelas_id' => 'nullable|exists:kelas,id',
            ]);

            $query = Jadwal::with(['mataPelajaran', 'guru', 'kelas']);

            // Apply filter hanya jika parameter tersedia
            if (!empty($validated['hari'])) {
                $query->where('hari', $validated['hari']);
            }

            if (!empty($validated['kelas_id'])) {
                $query->where('kelas_id', $validated['kelas_id']);
            }

            $jadwals = $query->orderBy('hari')
                ->orderBy('jam_ke')
                ->orderBy('jam_mulai')
                ->get()
                ->filter(function ($jadwal) {
                    // Filter out records with null relations
                    return $jadwal->mataPelajaran && $jadwal->guru && $jadwal->kelas;
                });

            // Format response dengan detail lengkap
            $result = $jadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'nama_guru' => $jadwal->guru->nama,
                    'nip_guru' => $jadwal->guru->nip,
                    'mata_pelajaran' => $jadwal->mataPelajaran->nama,
                    'kode_mapel' => $jadwal->mataPelajaran->kode,
                    'tahun_ajaran' => $jadwal->tahun_ajaran,
                    'jam_ke' => $jadwal->jam_ke,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'hari' => $jadwal->hari,
                    'kelas' => $jadwal->kelas->nama,
                    'ruangan' => $jadwal->ruangan,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Detail jadwal berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail jadwal',
            ], 500);
        }
    }

    /**
     * Display jadwal for the authenticated user's guru_id with class/day filters
     * Endpoint: GET /api/jadwal/my-schedule
     */
    public function getMySchedule(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->guru_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terkait dengan guru'
                ], 400);
            }

            $query = Jadwal::with(['guru', 'mataPelajaran', 'kelas'])
                ->where('guru_id', $user->guru_id);

            // Apply filters if provided
            if ($request->filled('hari')) {
                $query->where('hari', $request->hari);
            }

            if ($request->filled('kelas_id')) {
                $query->where('kelas_id', $request->kelas_id);
            }

            $jadwals = $query->orderBy('hari')
                ->orderBy('jam_ke')
                ->orderBy('jam_mulai')
                ->get();

            // Format response for the app
            $result = $jadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'jam_ke' => $jadwal->jam_ke,
                    'mata_pelajaran' => $jadwal->mataPelajaran->nama,
                    'kode_guru' => $jadwal->guru->nip,
                    'nama_guru' => $jadwal->guru->nama,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'hari' => $jadwal->hari,
                    'kelas' => $jadwal->kelas->nama,
                    'ruangan' => $jadwal->ruangan,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal saya berhasil diambil',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal saya',
            ], 500);
        }
    }
}
