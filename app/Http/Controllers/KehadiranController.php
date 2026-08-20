<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KehadiranController extends Controller
{
    /**
     * Display a listing of kehadiran with optional filters.
     * Endpoint: GET /api/kehadiran?tanggal=&kelas_id=&hari=&status=
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Select only required columns and eager-load minimal columns from relations
            // NOTE: `waktu_datang` and `durasi_keterlambatan` do not exist on the `kehadirans` table
            // selecting non-existent columns causes SQL errors (500). Use columns defined in the
            // migration/schema and add extras only after verifying the database contains them.
            $query = Kehadiran::select(['id', 'jadwal_id', 'siswa_id', 'tanggal', 'status', 'keterangan'])
                ->with([
                    'siswa:id,nama,nis,kelas_id',
                    'jadwal:id,mata_pelajaran_id,guru_id,kelas_id,jam_mulai,jam_selesai',
                    'jadwal.mataPelajaran:id,nama',
                    'jadwal.guru:id,nama'
                ]);

            // Filter by tanggal
            if ($request->filled('tanggal')) {
                $query->where('tanggal', $request->tanggal);
            }

            // Filter by kelas_id (melalui siswa)
            if ($request->filled('kelas_id')) {
                $query->whereHas('siswa', function ($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id);
                });
            }

            // Filter by hari (melalui jadwal)
            if ($request->filled('hari')) {
                $query->whereHas('jadwal', function ($q) use ($request) {
                    $q->where('hari', $request->hari);
                });
            }

            // Filter by status kehadiran
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Use pagination to limit payload size
            // Default to smaller pages to reduce payload and perceived latency
            $perPage = (int) $request->get('per_page', 20);
            $perPage = max(5, min($perPage, 100));

            $kehadirans = $query->orderBy('tanggal', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data kehadiran berhasil diambil',
                'data' => $kehadirans->items(),
                'meta' => [
                    'current_page' => $kehadirans->currentPage(),
                    'per_page' => $kehadirans->perPage(),
                    'total' => $kehadirans->total(),
                    'last_page' => $kehadirans->lastPage()
                ]
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kehadiran',
            ], 500);
        }
    }

    /**
     * Display the specified kehadiran.
     */
    public function show($id): JsonResponse
    {
        try {
            $kehadiran = Kehadiran::with(['siswa.kelas', 'jadwal.mataPelajaran', 'jadwal.guru'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail kehadiran berhasil diambil',
                'data' => $kehadiran
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Kehadiran tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Store a newly created kehadiran.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'siswa_id' => 'required|exists:siswas,id',
                'jadwal_id' => 'required|exists:jadwals,id',
                'tanggal' => 'required|date',
                'status' => 'required|in:hadir,tidak_hadir,izin,sakit',
                'keterangan' => 'nullable|string',
            ]);

            // Check if kehadiran already exists
            $exists = Kehadiran::where('siswa_id', $validated['siswa_id'])
                ->where('jadwal_id', $validated['jadwal_id'])
                ->where('tanggal', $validated['tanggal'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehadiran untuk siswa ini pada jadwal dan tanggal yang sama sudah ada'
                ], 422);
            }

            $validated['diinput_oleh'] = $request->user()->id;
            $kehadiran = Kehadiran::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran berhasil ditambahkan',
                'data' => $kehadiran
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kehadiran',
            ], 500);
        }
    }

    /**
     * Update the specified kehadiran.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $kehadiran = Kehadiran::findOrFail($id);

            $validated = $request->validate([
                'siswa_id' => 'sometimes|exists:siswas,id',
                'jadwal_id' => 'sometimes|exists:jadwals,id',
                'tanggal' => 'sometimes|date',
                'status' => 'sometimes|in:hadir,tidak_hadir,izin,sakit',
                'keterangan' => 'nullable|string',
            ]);

            $validated['diinput_oleh'] = $request->user()->id;
            $kehadiran->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran berhasil diupdate',
                'data' => $kehadiran
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kehadiran',
            ], 500);
        }
    }

    /**
     * Remove the specified kehadiran.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $kehadiran = Kehadiran::findOrFail($id);
            $kehadiran->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kehadiran',
            ], 500);
        }
    }

    /**
     * Get kehadiran by siswa.
     */
    public function bySiswa($siswaId): JsonResponse
    {
        try {
            $kehadirans = Kehadiran::where('siswa_id', $siswaId)
                ->with(['jadwal.mataPelajaran', 'jadwal.guru'])
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran siswa berhasil diambil',
                'data' => $kehadirans
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kehadiran siswa',
            ], 500);
        }
    }

    /**
     * Get kehadiran by jadwal and tanggal.
     */
    public function byJadwalTanggal($jadwalId, $tanggal): JsonResponse
    {
        try {
            $kehadirans = Kehadiran::where('jadwal_id', $jadwalId)
                ->where('tanggal', $tanggal)
                ->with(['siswa.kelas'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran berhasil diambil',
                'data' => $kehadirans
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kehadiran',
            ], 500);
        }
    }

    /**
     * Get rekap kehadiran by siswa.
     */
    public function rekapBySiswa($siswaId): JsonResponse
    {
        try {
            $kehadirans = Kehadiran::where('siswa_id', $siswaId)->get();

            $rekap = [
                'total' => $kehadirans->count(),
                'hadir' => $kehadirans->where('status', 'hadir')->count(),
                'tidak_hadir' => $kehadirans->where('status', 'tidak_hadir')->count(),
                'izin' => $kehadirans->where('status', 'izin')->count(),
                'sakit' => $kehadirans->where('status', 'sakit')->count(),
            ];

            $rekap['persentase_hadir'] = $rekap['total'] > 0
                ? round(($rekap['hadir'] / $rekap['total']) * 100, 2)
                : 0;

            return response()->json([
                'success' => true,
                'message' => 'Rekap kehadiran siswa berhasil diambil',
                'data' => $rekap
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil rekap kehadiran',
            ], 500);
        }
    }

    /**
     * Get student attendance records by class_id and date
     * Endpoint: POST /api/kehadiran/by-class-date
     */
    public function byClassDate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'tanggal' => 'required|date',
            ]);

            // Get all students in the class
            $siswaIds = \App\Models\Siswa::where('kelas_id', $validated['kelas_id'])->pluck('id');

            // Get attendance records for those students on the given date
            $kehadirans = Kehadiran::whereIn('siswa_id', $siswaIds)
                ->where('tanggal', $validated['tanggal'])
                ->with(['siswa.kelas'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran siswa berhasil diambil',
                'data' => $kehadirans
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kehadiran siswa',
            ], 500);
        }
    }

    /**
     * Get students by current user's class to allow tracking attendance
     * Endpoint: GET /api/kehadiran/siswa-by-user-class
     */
    public function getSiswaByUserClass(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->kelas_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terkait dengan kelas'
                ], 400);
            }

            // Get all students in the same class as the current user
            $siswas = \App\Models\Siswa::where('kelas_id', $user->kelas_id)
                ->with(['kelas'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar siswa berhasil diambil',
                'data' => $siswas
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar siswa',
            ], 500);
        }
    }

    /**
     * Create general student attendance record without jadwal (for student-to-student tracking)
     * Endpoint: POST /api/kehadiran/general
     */
    public function createGeneralKehadiran(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'siswa_id' => 'required|exists:siswas,id',
                'tanggal' => 'required|date',
                'status' => 'required|in:hadir,tidak_hadir,izin,sakit',
                'keterangan' => 'nullable|string',
            ]);

            // Check if kehadiran already exists
            $exists = Kehadiran::where('siswa_id', $validated['siswa_id'])
                ->where('tanggal', $validated['tanggal'])
                ->whereNull('jadwal_id') // Only for general attendance records (no specific schedule)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehadiran umum untuk siswa ini pada tanggal yang sama sudah ada'
                ], 422);
            }

            // Create the attendance record without jadwal_id
            $validated['jadwal_id'] = null; // Explicitly set to null for general attendance
            $kehadiran = Kehadiran::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran umum berhasil ditambahkan',
                'data' => $kehadiran
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kehadiran umum',
            ], 500);
        }
    }
}
