<?php

namespace App\Http\Controllers;

use App\Models\IzinGuru;
use App\Models\Guru;
use App\Http\Resources\IzinGuruResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class IzinGuruController extends Controller
{
    /**
     * Display a listing of izin guru with optional filters.
     * Endpoint: GET /api/izin-guru?tanggal=&guru_id=&hari=&kelas_id=
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Select only necessary fields and eager-load minimal relation data
            $query = IzinGuru::select(['id', 'guru_id', 'tanggal_mulai', 'tanggal_selesai', 'durasi_hari', 'jenis_izin', 'keterangan', 'status_approval', 'disetujui_oleh'])
                ->with(['guru:id,nama', 'disetujuiOleh:id,name']);

            // Filter by tanggal (izin yang aktif pada tanggal tertentu)
            if ($request->filled('tanggal')) {
                $query->where(function ($q) use ($request) {
                    $q->where('tanggal_mulai', '<=', $request->tanggal)
                        ->where('tanggal_selesai', '>=', $request->tanggal);
                });
            }

            // Filter by guru_id
            if ($request->filled('guru_id')) {
                $query->where('guru_id', $request->guru_id);
            }

            // Filter by hari (mencari izin guru yang mengajar di hari tertentu)
            if ($request->filled('hari')) {
                $query->whereHas('guru.jadwals', function ($q) use ($request) {
                    $q->where('hari', $request->hari);
                });
            }

            // Filter by kelas_id (mencari izin guru yang mengajar di kelas tertentu)
            if ($request->filled('kelas_id')) {
                $query->whereHas('guru.jadwals', function ($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id);
                });
            }

            // Filter by status_approval
            if ($request->filled('status_approval')) {
                $query->where('status_approval', $request->status_approval);
            }

            // Filter by jenis_izin
            if ($request->filled('jenis_izin')) {
                $query->where('jenis_izin', $request->jenis_izin);
            }

            // Default to smaller pages to keep payloads small
            $perPage = (int) $request->get('per_page', 20);
            $perPage = max(5, min($perPage, 100));

            // If no filter is applied, cache the paginated result briefly to reduce DB load
            if (!$request->filled('tanggal') && !$request->filled('guru_id') && !$request->filled('hari') && !$request->filled('kelas_id') && !$request->filled('status_approval') && !$request->filled('jenis_izin')) {
                $page = (int) $request->get('page', 1);
                $cacheKey = "izin_guru_all_page_{$page}_per_{$perPage}";
                $paginated = Cache::remember($cacheKey, 30, function () use ($query, $perPage) {
                    return $query->orderBy('tanggal_mulai', 'desc')->paginate($perPage);
                });
            } else {
                $paginated = $query->orderBy('tanggal_mulai', 'desc')->paginate($perPage);
            }

            $izinGurus = $paginated;

            return response()->json([
                'success' => true,
                'message' => 'Data izin guru berhasil diambil',
                'data' => IzinGuruResource::collection($izinGurus->items()),
                'meta' => [
                    'current_page' => $izinGurus->currentPage(),
                    'per_page' => $izinGurus->perPage(),
                    'total' => $izinGurus->total(),
                    'last_page' => $izinGurus->lastPage()
                ]
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data izin guru',
            ], 500);
        }
    }

    /**
     * Display a listing of izin guru with day filter.
     * Endpoint: POST /api/izin-guru/filter-by-day
     */
    public function filterByDay(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
            ]);

            $izinGurus = IzinGuru::with(['guru', 'disetujuiOleh'])
                ->where(function ($query) use ($validated) {
                    $query->where('tanggal_mulai', '<=', $validated['tanggal'])
                        ->where('tanggal_selesai', '>=', $validated['tanggal']);
                })
                ->orderBy('tanggal_mulai', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data izin guru berhasil difilter berdasarkan tanggal',
                'data' => IzinGuruResource::collection($izinGurus)
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data izin guru',
            ], 500);
        }
    }

    /**
     * Display a listing of izin guru by guru_id.
     * Endpoint: GET /api/izin-guru/guru/{guruId}
     */
    public function getByGuru($guruId): JsonResponse
    {
        try {
            $izinGurus = IzinGuru::with(['guru', 'disetujuiOleh'])
                ->where('guru_id', $guruId)
                ->orderBy('tanggal_mulai', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data izin guru berhasil diambil berdasarkan guru',
                'data' => IzinGuruResource::collection($izinGurus)
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data izin guru',
            ], 500);
        }
    }

    /**
     * Store a newly created izin guru.
     * Endpoint: POST /api/izin-guru
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Log incoming request for debugging
            \Log::info('IzinGuru Store Request:', $request->all());

            $validated = $request->validate([
                'guru_id' => 'required|exists:gurus,id',
                'tanggal_mulai' => 'required|date_format:Y-m-d',
                'tanggal_selesai' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
                'jenis_izin' => 'required|in:sakit,izin,cuti,dinas_luar,lainnya',
                'keterangan' => 'nullable|string',
                'file_surat' => 'nullable|string',
                'status_approval' => 'nullable|in:pending,disetujui,ditolak',
                'tanggal_approval' => 'nullable|date',
                'catatan_approval' => 'nullable|string',
            ]);

            // Set default status_approval if not provided
            if ($request->user()->role === 'guru') {
                if (!$request->user()->guru_id || (int) $validated['guru_id'] !== (int) $request->user()->guru_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Guru hanya dapat mengajukan izin untuk dirinya sendiri',
                    ], 403);
                }
                $validated['status_approval'] = 'pending';
                unset($validated['tanggal_approval']);
            } elseif (!isset($validated['status_approval'])) {
                $validated['status_approval'] = 'pending';
            }

            if (in_array($validated['status_approval'], ['disetujui', 'ditolak'], true)) {
                $validated['disetujui_oleh'] = $request->user()->id;
                $validated['tanggal_approval'] = now();
            }

            // Calculate duration in days
            $tanggalMulai = \Carbon\Carbon::parse($validated['tanggal_mulai']);
            $tanggalSelesai = \Carbon\Carbon::parse($validated['tanggal_selesai']);
            $validated['durasi_hari'] = abs($tanggalSelesai->diffInDays($tanggalMulai)) + 1;

            \Log::info('IzinGuru Validated Data:', $validated);

            $izinGuru = IzinGuru::create($validated);

            // Load relationships for consistent response
            $izinGuru->load(['guru', 'disetujuiOleh']);

            return response()->json([
                'success' => true,
                'message' => 'Izin guru berhasil ditambahkan',
                'data' => new IzinGuruResource($izinGuru)
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('IzinGuru Validation Error:', [
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
            \Log::error('IzinGuru Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan izin guru',
            ], 500);
        }
    }

    /**
     * Display the specified izin guru.
     * Endpoint: GET /api/izin-guru/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $izinGuru = IzinGuru::with(['guru', 'disetujuiOleh'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail izin guru berhasil diambil',
                'data' => new IzinGuruResource($izinGuru)
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Izin guru tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Update the specified izin guru.
     * Endpoint: PUT /api/izin-guru/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $izinGuru = IzinGuru::findOrFail($id);

            $validated = $request->validate([
                'guru_id' => 'sometimes|exists:gurus,id',
                'tanggal_mulai' => 'sometimes|date',
                'tanggal_selesai' => 'sometimes|date|after_or_equal:tanggal_mulai',
                'jenis_izin' => 'sometimes|in:sakit,izin,cuti,dinas_luar,lainnya',
                'keterangan' => 'nullable|string',
                'file_surat' => 'nullable|string',
                'status_approval' => 'sometimes|in:pending,disetujui,ditolak',
                'tanggal_approval' => 'nullable|date',
                'catatan_approval' => 'nullable|string',
            ]);

            if (isset($validated['status_approval']) && in_array($validated['status_approval'], ['disetujui', 'ditolak'], true)) {
                $validated['disetujui_oleh'] = $request->user()->id;
                $validated['tanggal_approval'] = now();
            } elseif (($validated['status_approval'] ?? null) === 'pending') {
                $validated['disetujui_oleh'] = null;
                $validated['tanggal_approval'] = null;
            }

            if (isset($validated['tanggal_mulai']) && isset($validated['tanggal_selesai'])) {
                // Calculate duration in days
                $tanggalMulai = \Carbon\Carbon::parse($validated['tanggal_mulai']);
                $tanggalSelesai = \Carbon\Carbon::parse($validated['tanggal_selesai']);
                $validated['durasi_hari'] = abs($tanggalSelesai->diffInDays($tanggalMulai)) + 1;
            }

            $izinGuru->update($validated);

            // Reload with relationships
            $izinGuru->load(['guru', 'disetujuiOleh']);

            return response()->json([
                'success' => true,
                'message' => 'Izin guru berhasil diupdate',
                'data' => new IzinGuruResource($izinGuru)
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate izin guru',
            ], 500);
        }
    }

    /**
     * Remove the specified izin guru.
     * Endpoint: DELETE /api/izin-guru/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $izinGuru = IzinGuru::findOrFail($id);
            $izinGuru->delete();

            return response()->json([
                'success' => true,
                'message' => 'Izin guru berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus izin guru',
            ], 500);
        }
    }
}
