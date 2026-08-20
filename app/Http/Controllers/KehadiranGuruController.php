<?php

namespace App\Http\Controllers;

use App\Models\KehadiranGuru;
use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KehadiranGuruController extends Controller
{
    /**
     * Display a listing of kehadiran guru with optional filters and relationships.
     * Endpoint: GET /api/kehadiran-guru?tanggal=&kelas_id=&guru_id=&status_kehadiran=&per_page=
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Eager-load relationships to get names directly
            $query = KehadiranGuru::with([
                'guru:id,nama,nip',
                'jadwal:id,kelas_id,mata_pelajaran_id,guru_id,hari,jam_ke,jam_mulai,jam_selesai',
                'jadwal.kelas:id,nama',
                'jadwal.mataPelajaran:id,nama,kode'
            ]);

            // Filter by tanggal
            if ($request->filled('tanggal')) {
                $query->where('tanggal', $request->tanggal);
            }

            // Filter by guru_id
            if ($request->filled('guru_id')) {
                $query->where('guru_id', $request->guru_id);
            }

            // Filter by kelas_id (via jadwal relation)
            if ($request->filled('kelas_id')) {
                $query->whereHas('jadwal', function ($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id);
                });
            }

            if ($request->filled('status_kehadiran')) {
                $query->where('status_kehadiran', $request->status_kehadiran);
            }

            // Use pagination to limit payload size
            $perPage = (int) $request->get('per_page', 20);
            $perPage = max(5, min($perPage, 100));

            $kehadirans = $query->orderBy('tanggal', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data kehadiran guru berhasil diambil',
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
                'message' => 'Gagal mengambil data kehadiran guru',
            ], 500);
        }
    }

    /**
     * Display the specified kehadiran guru.
     */
    public function show($id): JsonResponse
    {
        try {
            $kehadiran = KehadiranGuru::with([
                'guru',
                'jadwal.kelas',
                'jadwal.mataPelajaran',
                'diinputOleh'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail kehadiran guru berhasil diambil',
                'data' => $kehadiran
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Kehadiran guru tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Store a newly created kehadiran guru.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'jadwal_id' => 'required|exists:jadwals,id',
                'guru_id' => 'required|exists:gurus,id',
                'tanggal' => 'required|date',
                'status_kehadiran' => 'required|in:hadir,telat,tidak_hadir,izin,sakit',
                'waktu_datang' => 'nullable|date_format:H:i',
                'durasi_keterlambatan' => 'nullable|integer|min:0',
                'keterangan' => 'nullable|string',
            ]);

            // Check if kehadiran already exists
            $exists = KehadiranGuru::where('jadwal_id', $validated['jadwal_id'])
                ->where('guru_id', $validated['guru_id'])
                ->where('tanggal', $validated['tanggal'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehadiran guru untuk jadwal ini pada tanggal yang sama sudah ada'
                ], 422);
            }

            $validated['diinput_oleh'] = $request->user()->id;
            $kehadiran = KehadiranGuru::create($validated);

            // Load relationships for response
            $kehadiran->load(['guru', 'jadwal.kelas', 'jadwal.mataPelajaran']);

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran guru berhasil ditambahkan',
                'data' => $kehadiran
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kehadiran guru',
            ], 500);
        }
    }

    /**
     * Update the specified kehadiran guru.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $kehadiran = KehadiranGuru::findOrFail($id);

            $validated = $request->validate([
                'jadwal_id' => 'sometimes|exists:jadwals,id',
                'guru_id' => 'sometimes|exists:gurus,id',
                'tanggal' => 'sometimes|date',
                'status_kehadiran' => 'sometimes|in:hadir,telat,tidak_hadir,izin,sakit',
                'waktu_datang' => 'nullable|date_format:H:i',
                'durasi_keterlambatan' => 'nullable|integer|min:0',
                'keterangan' => 'nullable|string',
            ]);

            $validated['diinput_oleh'] = $request->user()->id;
            $kehadiran->update($validated);

            // Load relationships for response
            $kehadiran->load(['guru', 'jadwal.kelas', 'jadwal.mataPelajaran']);

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran guru berhasil diupdate',
                'data' => $kehadiran
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kehadiran guru',
            ], 500);
        }
    }

    /**
     * Remove the specified kehadiran guru.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $kehadiran = KehadiranGuru::findOrFail($id);
            $kehadiran->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran guru berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kehadiran guru',
            ], 500);
        }
    }

    /**
     * Get kehadiran guru by guru ID.
     */
    public function byGuru($guruId): JsonResponse
    {
        try {
            $kehadirans = KehadiranGuru::where('guru_id', $guruId)
                ->with([
                    'guru',
                    'jadwal.kelas',
                    'jadwal.mataPelajaran'
                ])
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran guru berhasil diambil',
                'data' => $kehadirans
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kehadiran guru',
            ], 500);
        }
    }

    /**
     * Get rekap kehadiran by guru.
     */
    public function rekapByGuru($guruId): JsonResponse
    {
        try {
            $kehadirans = KehadiranGuru::where('guru_id', $guruId)->get();

            $rekap = [
                'total' => $kehadirans->count(),
                'hadir' => $kehadirans->where('status_kehadiran', 'hadir')->count(),
                'telat' => $kehadirans->where('status_kehadiran', 'telat')->count(),
                'tidak_hadir' => $kehadirans->where('status_kehadiran', 'tidak_hadir')->count(),
                'izin' => $kehadirans->where('status_kehadiran', 'izin')->count(),
                'sakit' => $kehadirans->where('status_kehadiran', 'sakit')->count(),
            ];

            $rekap['persentase_hadir'] = $rekap['total'] > 0
                ? round((($rekap['hadir'] + $rekap['telat']) / $rekap['total']) * 100, 2)
                : 0;

            return response()->json([
                'success' => true,
                'message' => 'Rekap kehadiran guru berhasil diambil',
                'data' => $rekap
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil rekap kehadiran guru',
            ], 500);
        }
    }
}
