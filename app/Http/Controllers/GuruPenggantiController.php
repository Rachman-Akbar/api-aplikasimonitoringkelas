<?php

namespace App\Http\Controllers;

use App\Models\GuruPengganti;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\User;
use App\Http\Resources\GuruPenggantiResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GuruPenggantiController extends Controller
{
    /**
     * Display a listing of guru pengganti with optional filters.
     * Endpoint: GET /api/guru-pengganti?tanggal=&guru_asli_id=&guru_pengganti_id=&kelas_id=&hari=
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = GuruPengganti::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'guruAsli', 'guruPengganti', 'disetujuiOleh']);

            // Filter by tanggal
            if ($request->filled('tanggal')) {
                $query->where('tanggal', $request->tanggal);
            }

            // Filter by guru_asli_id (untuk melihat pengganti dari guru tertentu)
            if ($request->filled('guru_asli_id')) {
                $query->where('guru_asli_id', $request->guru_asli_id);
            }

            // Filter by guru_pengganti_id (untuk melihat jadwal guru pengganti tertentu)
            if ($request->filled('guru_pengganti_id')) {
                $query->where('guru_pengganti_id', $request->guru_pengganti_id);
            }

            // Filter by kelas_id (melalui jadwal)
            if ($request->filled('kelas_id')) {
                $query->whereHas('jadwal', function ($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id);
                });
            }

            // Filter by hari (melalui jadwal)
            if ($request->filled('hari')) {
                $query->whereHas('jadwal', function ($q) use ($request) {
                    $q->where('hari', $request->hari);
                });
            }

            // Filter by status_penggantian
            if ($request->filled('status_penggantian')) {
                $query->where('status_penggantian', $request->status_penggantian);
            }

            $guruPenggantis = $query->orderBy('tanggal', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data guru pengganti berhasil diambil',
                'data' => GuruPenggantiResource::collection($guruPenggantis)
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data guru pengganti',
            ], 500);
        }
    }

    /**
     * Store a newly created guru pengganti.
     * Endpoint: POST /api/guru-pengganti
     */
    public function store(Request $request): JsonResponse
    {
        try {
            \Log::info('GuruPengganti Store Request:', $request->all());

            $validated = $request->validate([
                'jadwal_id' => 'required|exists:jadwals,id',
                'tanggal' => 'required|date_format:Y-m-d',
                'guru_asli_id' => 'required|exists:gurus,id',
                'guru_pengganti_id' => 'required|exists:gurus,id',
                'status_penggantian' => 'nullable|in:pending,disetujui,ditolak,dijadwalkan,selesai,dibatalkan,tidak_hadir',
                'keterangan' => 'nullable|string',
                'catatan_approval' => 'nullable|string',
            ]);

            // Set default status_penggantian if not provided
            if (!isset($validated['status_penggantian'])) {
                $validated['status_penggantian'] = 'pending';
            }

            \Log::info('GuruPengganti Validated Data:', $validated);

            $guruPengganti = GuruPengganti::create($validated);

            // Load relationships for consistent response (removed dibuatOleh)
            $guruPengganti->load(['jadwal.kelas', 'jadwal.mataPelajaran', 'guruAsli', 'guruPengganti', 'disetujuiOleh']);

            return response()->json([
                'success' => true,
                'message' => 'Guru pengganti berhasil ditambahkan',
                'data' => new GuruPenggantiResource($guruPengganti)
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('GuruPengganti Validation Error:', [
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            \Log::error('GuruPengganti Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan guru pengganti',
            ], 500);
        }
    }

    /**
     * Display the specified guru pengganti.
     * Endpoint: GET /api/guru-pengganti/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $guruPengganti = GuruPengganti::with(['jadwal', 'guruAsli', 'guruPengganti', 'disetujuiOleh'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail guru pengganti berhasil diambil',
                'data' => new GuruPenggantiResource($guruPengganti)
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Guru pengganti tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Filter substitute teachers by a guru id (either as guru_asli or guru_pengganti).
     * Endpoint: GET /api/guru-pengganti/filter-by-guru/{guruId}?tanggal=&role=asli|pengganti|both&per_page=
     */
    public function filterByGuru(Request $request, $guruId): JsonResponse
    {
        try {
            $query = GuruPengganti::with(['jadwal.kelas', 'jadwal.mataPelajaran', 'guruAsli', 'guruPengganti', 'disetujuiOleh']);

            // Role filtering: apakah ingin hanya guru_asli, hanya guru_pengganti, atau keduanya
            $role = $request->query('role', 'both');
            if ($role === 'asli') {
                $query->where('guru_asli_id', $guruId);
            } elseif ($role === 'pengganti') {
                $query->where('guru_pengganti_id', $guruId);
            } else {
                // both
                $query->where(function ($q) use ($guruId) {
                    $q->where('guru_asli_id', $guruId)
                        ->orWhere('guru_pengganti_id', $guruId);
                });
            }

            // Optional tanggal filter
            if ($request->filled('tanggal')) {
                $query->where('tanggal', $request->tanggal);
            }

            // Optional kelas filter (via jadwal relation)
            if ($request->filled('kelas_id')) {
                $query->whereHas('jadwal', function ($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id);
                });
            }

            // Order
            $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

            // Pagination if requested
            if ($request->filled('per_page')) {
                $perPage = (int) $request->per_page;
                $results = $query->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'message' => 'Data guru pengganti terfilter berhasil diambil',
                    'data' => GuruPenggantiResource::collection($results->items()),
                    'meta' => [
                        'total' => $results->total(),
                        'per_page' => $results->perPage(),
                        'current_page' => $results->currentPage(),
                        'last_page' => $results->lastPage(),
                    ]
                ], 200);
            }

            $guruPenggantis = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Data guru pengganti terfilter berhasil diambil',
                'data' => GuruPenggantiResource::collection($guruPenggantis)
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data guru pengganti terfilter',
            ], 500);
        }
    }

    /**
     * Update the specified guru pengganti.
     * Endpoint: PUT /api/guru-pengganti/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $guruPengganti = GuruPengganti::findOrFail($id);

            $validated = $request->validate([
                'jadwal_id' => 'sometimes|exists:jadwals,id',
                'tanggal' => 'sometimes|date',
                'guru_asli_id' => 'sometimes|exists:gurus,id',
                'guru_pengganti_id' => 'sometimes|exists:gurus,id',
                'status_penggantian' => 'sometimes|in:pending,disetujui,ditolak,dijadwalkan,selesai,dibatalkan,tidak_hadir',
                'keterangan' => 'sometimes|string|nullable',
                'catatan_approval' => 'sometimes|string|nullable',
            ]);

            if (isset($validated['status_penggantian']) && in_array($validated['status_penggantian'], ['disetujui', 'ditolak', 'dijadwalkan', 'selesai', 'tidak_hadir'], true)) {
                $validated['disetujui_oleh'] = $request->user()->id;
            } elseif (($validated['status_penggantian'] ?? null) === 'pending') {
                $validated['disetujui_oleh'] = null;
            }

            $guruPengganti->update($validated);

            // Reload with relationships
            $guruPengganti->load(['jadwal.kelas', 'jadwal.mataPelajaran', 'guruAsli', 'guruPengganti', 'disetujuiOleh']);

            return response()->json([
                'success' => true,
                'message' => 'Guru pengganti berhasil diupdate',
                'data' => new GuruPenggantiResource($guruPengganti)
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate guru pengganti',
            ], 500);
        }
    }

    /**
     * Remove the specified guru pengganti.
     * Endpoint: DELETE /api/guru-pengganti/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $guruPengganti = GuruPengganti::findOrFail($id);
            $guruPengganti->delete();

            return response()->json([
                'success' => true,
                'message' => 'Guru pengganti berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus guru pengganti',
            ], 500);
        }
    }
}
