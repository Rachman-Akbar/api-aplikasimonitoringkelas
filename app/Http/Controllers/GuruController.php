<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class GuruController extends Controller
{
    /**
     * Display a listing of gurus.
     */
    public function index(): JsonResponse
    {
        try {
            // Cache the guru list for short time because this data rarely changes
            $gurus = Cache::remember('guru_list_minimal', 300, function () {
                return Guru::select(['id', 'nama', 'nip', 'status'])->get();
            });

            return response()->json([
                'success' => true,
                'message' => 'Data guru berhasil diambil',
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
     * Display the specified guru.
     */
    public function show($id): JsonResponse
    {
        try {
            $guru = Guru::with(['kelasWali.siswas', 'jadwals.mataPelajaran', 'jadwals.kelas'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail guru berhasil diambil',
                'data' => $guru
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Store a newly created guru.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nip' => 'required|string|max:18|unique:gurus,nip',
                'nama' => 'required|string|max:100',
                'email' => 'nullable|email|max:100|unique:gurus,email',
                'no_telp' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'jenis_kelamin' => 'required|in:L,P',
                'tanggal_lahir' => 'nullable|date',
                'status' => 'required|in:aktif,nonaktif,pensiun',
            ]);

            $guru = Guru::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil ditambahkan',
                'data' => $guru
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan guru',
            ], 500);
        }
    }

    /**
     * Update the specified guru.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $guru = Guru::findOrFail($id);

            $validated = $request->validate([
                'nip' => 'sometimes|string|max:18|unique:gurus,nip,' . $id,
                'nama' => 'sometimes|string|max:100',
                'email' => 'nullable|email|max:100|unique:gurus,email,' . $id,
                'no_telp' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'jenis_kelamin' => 'sometimes|in:L,P',
                'tanggal_lahir' => 'nullable|date',
                'status' => 'sometimes|in:aktif,nonaktif,pensiun',
            ]);

            $guru->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil diupdate',
                'data' => $guru
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate guru',
            ], 500);
        }
    }

    /**
     * Remove the specified guru.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $guru = Guru::findOrFail($id);
            $guru->delete();

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus guru',
            ], 500);
        }
    }
}
