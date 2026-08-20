<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnumController extends Controller
{
    /**
     * Get all available enum values for filters
     * Endpoint: GET /api/enums
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'status_kehadiran_guru' => ['hadir', 'telat', 'tidak_hadir', 'izin', 'sakit'],
                'status_kehadiran_siswa' => ['hadir', 'tidak_hadir', 'izin', 'sakit'],
                'status_penggantian' => ['dijadwalkan', 'selesai', 'dibatalkan'],
                'status_approval' => ['pending', 'disetujui', 'ditolak'],
                'jenis_izin' => ['sakit', 'izin', 'cuti', 'dinas_luar', 'lainnya'],
                'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
            ]
        ], 200);
    }

    /**
     * Get specific enum values
     * Endpoint: GET /api/enums/{type}
     */
    public function show($type): JsonResponse
    {
        $enums = [
            'status_kehadiran_guru' => ['hadir', 'telat', 'tidak_hadir', 'izin', 'sakit'],
            'status_kehadiran_siswa' => ['hadir', 'tidak_hadir', 'izin', 'sakit'],
            'status_penggantian' => ['dijadwalkan', 'selesai', 'dibatalkan'],
            'status_approval' => ['pending', 'disetujui', 'ditolak'],
            'jenis_izin' => ['sakit', 'izin', 'cuti', 'dinas_luar', 'lainnya'],
            'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
        ];

        if (!isset($enums[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Enum type not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $enums[$type]
        ], 200);
    }

    /**
     * Get distinct values from database for dynamic enums
     * Endpoint: GET /api/enums/distinct/{table}/{column}
     */
    public function getDistinct($table, $column): JsonResponse
    {
        try {
            // Whitelist allowed tables and columns for security
            $allowedTables = [
                'kehadiran_gurus' => ['status_kehadiran'],
                'kehadiran_siswas' => ['status'],
                'guru_pengganties' => ['status_penggantian'],
                'izin_gurus' => ['status_approval', 'jenis_izin'],
                'jadwals' => ['hari']
            ];

            if (!isset($allowedTables[$table]) || !in_array($column, $allowedTables[$table])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid table or column'
                ], 400);
            }

            $values = \DB::table($table)
                ->select($column)
                ->distinct()
                ->whereNotNull($column)
                ->pluck($column)
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => array_values($values)
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch distinct values',
            ], 500);
        }
    }
}
