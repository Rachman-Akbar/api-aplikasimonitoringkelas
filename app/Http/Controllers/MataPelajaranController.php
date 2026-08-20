<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MataPelajaranController extends Controller
{
    /**
     * Display a listing of mata pelajaran.
     */
    public function index(): JsonResponse
    {
        try {
            $mataPelajarans = Cache::remember('mata_pelajaran_minimal', 300, function () {
                return MataPelajaran::select(['id', 'nama', 'kode', 'status'])->get();
            });

            return response()->json([
                'success' => true,
                'message' => 'Data mata pelajaran berhasil diambil',
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
     * Display the specified mata pelajaran.
     */
    public function show($id): JsonResponse
    {
        try {
            $mataPelajaran = MataPelajaran::with(['jadwals.kelas', 'jadwals.guru'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail mata pelajaran berhasil diambil',
                'data' => $mataPelajaran
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Mata pelajaran tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Store a newly created mata pelajaran.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kode' => 'required|string|max:10|unique:mata_pelajarans,kode',
                'nama' => 'required|string|max:100',
                'deskripsi' => 'nullable|string',
                'sks' => 'required|integer|min:1|max:10',
                'kategori' => 'required|in:wajib,pilihan,muatan_lokal',
                'status' => 'required|in:aktif,nonaktif',
            ]);

            $mataPelajaran = MataPelajaran::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil ditambahkan',
                'data' => $mataPelajaran
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mata pelajaran',
            ], 500);
        }
    }

    /**
     * Update the specified mata pelajaran.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $mataPelajaran = MataPelajaran::findOrFail($id);

            $validated = $request->validate([
                'kode' => 'sometimes|string|max:10|unique:mata_pelajarans,kode,' . $id,
                'nama' => 'sometimes|string|max:100',
                'deskripsi' => 'nullable|string',
                'sks' => 'sometimes|integer|min:1|max:10',
                'kategori' => 'sometimes|in:wajib,pilihan,muatan_lokal',
                'status' => 'sometimes|in:aktif,nonaktif',
            ]);

            $mataPelajaran->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil diupdate',
                'data' => $mataPelajaran
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate mata pelajaran',
            ], 500);
        }
    }

    /**
     * Remove the specified mata pelajaran.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $mataPelajaran = MataPelajaran::findOrFail($id);
            $mataPelajaran->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus mata pelajaran',
            ], 500);
        }
    }
}
