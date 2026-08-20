<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SiswaController extends Controller
{
    /**
     * Display a listing of siswas.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Siswa::with(['kelas.waliKelas']);

            // Filter by kelas_id if provided
            if ($request->has('kelas_id')) {
                $query->where('kelas_id', $request->kelas_id);
            }

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $siswas = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diambil',
                'data' => $siswas
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data siswa',
            ], 500);
        }
    }

    /**
     * Display the specified siswa.
     */
    public function show($id): JsonResponse
    {
        try {
            $siswa = Siswa::with(['kelas.waliKelas', 'kehadirans.jadwal.mataPelajaran'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail siswa berhasil diambil',
                'data' => $siswa
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Store a newly created siswa.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nis' => 'required|string|max:10|unique:siswas,nis',
                'nisn' => 'required|string|max:15|unique:siswas,nisn',
                'nama' => 'required|string|max:100',
                'email' => 'nullable|email|max:100',
                'no_telp' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'jenis_kelamin' => 'required|in:L,P',
                'tanggal_lahir' => 'nullable|date',
                'foto' => 'nullable|string|max:255',
                'kelas_id' => 'nullable|exists:kelas,id',
                'nama_orang_tua' => 'nullable|string|max:100',
                'no_telp_orang_tua' => 'nullable|string|max:15',
                'status' => 'required|in:aktif,lulus,pindah,keluar,nonaktif',
            ]);

            $siswa = Siswa::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan',
                'data' => $siswa
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan siswa',
            ], 500);
        }
    }

    /**
     * Update the specified siswa.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $siswa = Siswa::findOrFail($id);

            $validated = $request->validate([
                'nis' => 'sometimes|string|max:10|unique:siswas,nis,' . $id,
                'nisn' => 'sometimes|string|max:15|unique:siswas,nisn,' . $id,
                'nama' => 'sometimes|string|max:100',
                'email' => 'nullable|email|max:100',
                'no_telp' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'jenis_kelamin' => 'sometimes|in:L,P',
                'tanggal_lahir' => 'nullable|date',
                'foto' => 'nullable|string|max:255',
                'kelas_id' => 'nullable|exists:kelas,id',
                'nama_orang_tua' => 'nullable|string|max:100',
                'no_telp_orang_tua' => 'nullable|string|max:15',
                'status' => 'sometimes|in:aktif,lulus,pindah,keluar,nonaktif',
            ]);

            $siswa->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil diupdate',
                'data' => $siswa
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate siswa',
            ], 500);
        }
    }

    /**
     * Remove the specified siswa.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $siswa = Siswa::findOrFail($id);
            $siswa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus siswa',
            ], 500);
        }
    }

    /**
     * Get siswa by kelas.
     */
    public function byKelas($kelasId): JsonResponse
    {
        try {
            $siswas = Siswa::where('kelas_id', $kelasId)
                ->with(['kelas.waliKelas'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berdasarkan kelas berhasil diambil',
                'data' => $siswas
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data siswa',
            ], 500);
        }
    }
}
