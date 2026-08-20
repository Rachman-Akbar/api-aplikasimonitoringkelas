<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:guru,kurikulum,siswa,kepsek',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            $token = $user->createToken('api_token')->plainTextToken;

            Log::info('API User registered', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 201);
        } catch (ValidationException $e) {
            Log::error('API Registration validation error', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            Log::error('API Registration exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
            ], 500);
        }
    }

    /**
     * Login user and create token
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Log request for debugging
            Log::info('API Login attempt', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ]);

            // Validate input
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            // Find user by email with minimal data to improve performance
            $user = User::select(['id', 'name', 'email', 'password', 'role', 'guru_id', 'kelas_id'])
                ->where('email', $request->email)
                ->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                Log::warning('API Login failed - invalid credentials', [
                    'email' => $request->email,
                    'user_exists' => $user ? 'yes' : 'no',
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah'
                ], 401);
            }

            // IMPORTANT: Mobile API is only for non-admin roles
            $allowedMobileRoles = ['guru', 'siswa', 'kepsek', 'kurikulum'];

            if (!in_array($user->role, $allowedMobileRoles)) {
                Log::warning('Blocked non-mobile role login attempt', [
                    'email' => $user->email,
                    'role' => $user->role,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Role "' . $user->role . '" tidak diizinkan login via aplikasi mobile.'
                ], 403);
            }

            // Ensure proper IDs are present for mobile clients:
            // - For siswa role: prefer `users.kelas_id`, fallback to `siswas.kelas_id` linked by `user_id`
            // - For guru role: prefer `users.guru_id`, fallback to `gurus.id` linked by `user_id`
            if ($user->role === 'siswa') {
                if (empty($user->kelas_id)) {
                    $siswa = Siswa::where('user_id', $user->id)->first();
                    if ($siswa) {
                        $user->kelas_id = $siswa->kelas_id;
                    }
                }
            }

            if ($user->role === 'guru') {
                if (empty($user->guru_id)) {
                    $guru = Guru::where('user_id', $user->id)->first();
                    if ($guru) {
                        $user->guru_id = $guru->id;
                    }
                }
            }

            // Revoke old tokens (optional but recommended)
            $user->tokens()->delete();

            // Create new token
            $token = $user->createToken('api_token')->plainTextToken;

            Log::info('API Login successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'kelas_id' => $user->kelas_id,
                'guru_id' => $user->guru_id,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'kelas_id' => $user->kelas_id,
                        'guru_id' => $user->guru_id,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);
        } catch (ValidationException $e) {
            Log::error('API Login validation error', [
                'errors' => $e->errors(),
                'email' => $request->email ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            Log::error('API Login exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Login gagal karena terjadi kesalahan pada server'
            ], 500);
        }
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            Log::info('API Me called', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'guru_id' => $user->guru_id ?? null,
                        'kelas_id' => $user->kelas_id ?? null,
                    ],
                    'token' => $request->bearerToken(),
                    'token_type' => 'Bearer',
                ]
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            Log::error('API Me error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pengguna'
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            Log::info('API Logout successful', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            Log::error('API Logout error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Logout gagal karena terjadi kesalahan pada server'
            ], 500);
        }
    }

    /**
     * Logout from all devices (Revoke all tokens)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Revoke all tokens
            $user->tokens()->delete();

            Log::info('API Logout all devices', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logged out from all devices successfully'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            Log::error('API Logout all error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai'
                ], 401);
            }

            $user->password = Hash::make($validated['new_password']);
            $user->save();

            // Revoke all tokens
            $user->tokens()->delete();

            // Create new token
            $token = $user->createToken('api_token')->plainTextToken;

            Log::info('API Password changed', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);
        } catch (ValidationException $e) {
            Log::error('API Change password validation error', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            Log::error('API Change password exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Password change failed',
            ], 500);
        }
    }
}
