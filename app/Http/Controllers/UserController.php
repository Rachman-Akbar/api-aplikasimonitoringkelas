<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): JsonResponse
    {
        try {
            $users = User::with(['guru', 'kelas'])->get();

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
            ], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show($id): JsonResponse
    {
        try {
            $user = User::with(['guru', 'kelas'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail user berhasil diambil',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Get users by role.
     */
    public function byRole($role): JsonResponse
    {
        try {
            $users = User::with(['guru', 'kelas'])->where('role', $role)->get();

            return response()->json([
                'success' => true,
                'message' => 'Data user dengan role ' . $role . ' berhasil diambil',
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
            ], 500);
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,guru,siswa,kepala_sekolah,kurikulum,kepsek',
                'guru_id' => 'nullable|exists:gurus,id',
                'kelas_id' => 'nullable|exists:kelas,id',
                'foto' => 'nullable|string|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
            $user->load(['guru', 'kelas']);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user',
            ], 500);
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'role' => 'sometimes|in:admin,guru,siswa,kepala_sekolah,kurikulum,kepsek',
                'guru_id' => 'nullable|exists:gurus,id',
                'kelas_id' => 'nullable|exists:kelas,id',
                'foto' => 'nullable|string|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);
            $user->load(['guru', 'kelas']);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate user',
            ], 500);
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user',
            ], 500);
        }
    }
}
