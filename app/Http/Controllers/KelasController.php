<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KelasController extends Controller
{
    /**
     * Display a listing of kelas.
     */
    public function index(): JsonResponse
    {
        try {
            $kelas = Kelas::with(['waliKelas', 'siswas'])->get();

            return response()->json([
                'success' => true,
                'message' => 'Data kelas berhasil diambil',
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
     * Display the specified kelas.
     */
    public function show($id): JsonResponse
    {
        try {
            $kelas = Kelas::with([
                'waliKelas',
                'siswas',
                'jadwals.mataPelajaran',
                'jadwals.guru'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail kelas berhasil diambil',
                'data' => $kelas
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Store a newly created kelas.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:50',
                'tingkat' => 'required|integer|min:10|max:12',
                'jurusan' => 'required|string|max:50',
                'wali_kelas_id' => 'nullable|exists:gurus,id',
                'kapasitas' => 'required|integer|min:1|max:50',
                'jumlah_siswa' => 'nullable|integer|min:0',
                'ruangan' => 'nullable|string|max:20',
                'status' => 'required|in:aktif,nonaktif,arsip',
            ]);

            $kelas = Kelas::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil ditambahkan',
                'data' => $kelas
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kelas',
            ], 500);
        }
    }

    /**
     * Update the specified kelas.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $kelas = Kelas::findOrFail($id);

            $validated = $request->validate([
                'nama' => 'sometimes|string|max:50',
                'tingkat' => 'sometimes|integer|min:10|max:12',
                'jurusan' => 'sometimes|string|max:50',
                'wali_kelas_id' => 'nullable|exists:gurus,id',
                'kapasitas' => 'sometimes|integer|min:1|max:50',
                'jumlah_siswa' => 'nullable|integer|min:0',
                'ruangan' => 'nullable|string|max:20',
                'status' => 'sometimes|in:aktif,nonaktif,arsip',
            ]);

            $kelas->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil diupdate',
                'data' => $kelas
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kelas',
            ], 500);
        }
    }

    /**
     * Remove the specified kelas.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $kelas = Kelas::findOrFail($id);
            $kelas->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kelas',
            ], 500);
        }
    }
}
