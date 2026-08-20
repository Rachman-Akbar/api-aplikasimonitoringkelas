<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\GuruMengajarController;
use App\Http\Controllers\GuruPenggantiController;
use App\Http\Controllers\IzinGuruController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\KehadiranGuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API Server is running',
        'timestamp' => now()->toISOString(),
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1');
});

Route::get('/', function () {
    return response()->json([
        'message' => 'Selamat datang di API Aplikasi Monitoring Kelas',
        'version' => '1.1.0',
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->middleware('role:admin');
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
    });

    Route::prefix('enums')->group(function () {
        Route::get('/', [EnumController::class, 'index']);
        Route::get('/distinct/{table}/{column}', [EnumController::class, 'getDistinct']);
        Route::get('/{type}', [EnumController::class, 'show']);
    });

    Route::prefix('user')->middleware('role:admin')->group(function () {
        Route::get('/role/{role}', [UserController::class, 'byRole']);
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show'])->whereNumber('id');
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [UserController::class, 'destroy'])->whereNumber('id');
    });

    Route::prefix('guru')->group(function () {
        Route::get('/', [GuruController::class, 'index']);
        Route::get('/{id}', [GuruController::class, 'show'])->whereNumber('id');
        Route::middleware('role:admin,kurikulum')->group(function () {
            Route::post('/', [GuruController::class, 'store']);
            Route::put('/{id}', [GuruController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [GuruController::class, 'destroy'])->whereNumber('id');
        });
    });

    Route::prefix('siswa')->group(function () {
        Route::get('/kelas/{kelasId}', [SiswaController::class, 'byKelas'])->whereNumber('kelasId');
        Route::get('/', [SiswaController::class, 'index']);
        Route::get('/{id}', [SiswaController::class, 'show'])->whereNumber('id');
        Route::middleware('role:admin,kurikulum')->group(function () {
            Route::post('/', [SiswaController::class, 'store']);
            Route::put('/{id}', [SiswaController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [SiswaController::class, 'destroy'])->whereNumber('id');
        });
    });

    Route::prefix('kelas')->group(function () {
        Route::get('/', [KelasController::class, 'index']);
        Route::get('/{id}', [KelasController::class, 'show'])->whereNumber('id');
        Route::middleware('role:admin,kurikulum')->group(function () {
            Route::post('/', [KelasController::class, 'store']);
            Route::put('/{id}', [KelasController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [KelasController::class, 'destroy'])->whereNumber('id');
        });
    });

    Route::prefix('mata-pelajaran')->group(function () {
        Route::get('/', [MataPelajaranController::class, 'index']);
        Route::get('/{id}', [MataPelajaranController::class, 'show'])->whereNumber('id');
        Route::middleware('role:admin,kurikulum')->group(function () {
            Route::post('/', [MataPelajaranController::class, 'store']);
            Route::put('/{id}', [MataPelajaranController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [MataPelajaranController::class, 'destroy'])->whereNumber('id');
        });
    });

    Route::prefix('jadwal')->group(function () {
        Route::get('/my-schedule', [JadwalController::class, 'getMySchedule']);
        Route::get('/kelas/{kelasId}/hari/{hari}', [JadwalController::class, 'getJadwalByKelasHari'])->whereNumber('kelasId');
        Route::get('/detail/kelas/{kelasId}/hari/{hari}', [JadwalController::class, 'getJadwalDetailByKelasHari'])->whereNumber('kelasId');
        Route::get('/kelas/{kelasId}', [JadwalController::class, 'byKelas'])->whereNumber('kelasId');
        Route::get('/guru/{guruId}', [JadwalController::class, 'byGuru'])->whereNumber('guruId');
        Route::get('/hari/{hari}', [JadwalController::class, 'byHari']);
        Route::post('/filter/kelas-by-hari', [JadwalController::class, 'filterKelasByHari']);
        Route::post('/filter/guru-by-hari-kelas', [JadwalController::class, 'filterGuruByHariKelas']);
        Route::post('/filter/mapel-by-hari-kelas-guru', [JadwalController::class, 'filterMapelByHariKelasGuru']);
        Route::post('/get-jam-ke', [JadwalController::class, 'getJamKe']);
        Route::post('/by-hari-kelas', [JadwalController::class, 'filterJadwalByHariKelas']);
        Route::post('/filter-flexible', [JadwalController::class, 'filterJadwalFlexible']);
        Route::post('/filter-flexible-detail', [JadwalController::class, 'filterJadwalFlexibleDetail']);
        Route::get('/', [JadwalController::class, 'index']);
        Route::get('/{id}', [JadwalController::class, 'show'])->whereNumber('id');
        Route::middleware('role:admin,kurikulum')->group(function () {
            Route::post('/', [JadwalController::class, 'store']);
            Route::put('/{id}', [JadwalController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [JadwalController::class, 'destroy'])->whereNumber('id');
        });
    });

    Route::prefix('kehadiran')->group(function () {
        Route::get('/siswa/{siswaId}', [KehadiranController::class, 'bySiswa'])->whereNumber('siswaId');
        Route::get('/jadwal/{jadwalId}/tanggal/{tanggal}', [KehadiranController::class, 'byJadwalTanggal'])->whereNumber('jadwalId');
        Route::get('/rekap/siswa/{siswaId}', [KehadiranController::class, 'rekapBySiswa'])->whereNumber('siswaId');
        Route::post('/by-class-date', [KehadiranController::class, 'byClassDate']);
        Route::get('/siswa-by-user-class', [KehadiranController::class, 'getSiswaByUserClass']);
        Route::get('/', [KehadiranController::class, 'index']);
        Route::get('/{id}', [KehadiranController::class, 'show'])->whereNumber('id');
        Route::middleware('role:admin,kurikulum,siswa')->group(function () {
            Route::post('/', [KehadiranController::class, 'store']);
            Route::post('/general', [KehadiranController::class, 'createGeneralKehadiran']);
            Route::put('/{id}', [KehadiranController::class, 'update'])->whereNumber('id');
        });
        Route::delete('/{id}', [KehadiranController::class, 'destroy'])->middleware('role:admin,kurikulum')->whereNumber('id');
    });

    Route::prefix('kehadiran-guru')->group(function () {
        Route::get('/guru/{guruId}', [KehadiranGuruController::class, 'byGuru'])->whereNumber('guruId');
        Route::get('/rekap/guru/{guruId}', [KehadiranGuruController::class, 'rekapByGuru'])->whereNumber('guruId');
        Route::get('/', [KehadiranGuruController::class, 'index']);
        Route::get('/{id}', [KehadiranGuruController::class, 'show'])->whereNumber('id');
        Route::middleware('role:admin,kurikulum,siswa')->group(function () {
            Route::post('/', [KehadiranGuruController::class, 'store']);
            Route::put('/{id}', [KehadiranGuruController::class, 'update'])->whereNumber('id');
        });
        Route::delete('/{id}', [KehadiranGuruController::class, 'destroy'])->middleware('role:admin,kurikulum')->whereNumber('id');
    });

    Route::prefix('guru-mengajar')->group(function () {
        Route::get('/hari/{hari}/kelas/{kelasId}', [GuruMengajarController::class, 'getByHariKelas'])->whereNumber('kelasId');
        Route::get('/tidak-masuk/hari/{hari}/kelas/{kelasId}', [GuruMengajarController::class, 'getGuruTidakMasuk'])->whereNumber('kelasId');
        Route::post('/by-hari-kelas', [GuruMengajarController::class, 'getByHariKelasPost']);
        Route::post('/tidak-masuk', [GuruMengajarController::class, 'getGuruTidakMasukPost']);
        Route::middleware('role:admin,kurikulum,siswa')->group(function () {
            Route::post('/', [GuruMengajarController::class, 'store']);
            Route::put('/', [GuruMengajarController::class, 'update']);
            Route::put('/{id}', [GuruMengajarController::class, 'updateById'])->whereNumber('id');
        });
    });

    Route::prefix('guru-pengganti')->group(function () {
        Route::get('/filter-by-guru/{guruId}', [GuruPenggantiController::class, 'filterByGuru'])->whereNumber('guruId');
        Route::get('/', [GuruPenggantiController::class, 'index']);
        Route::get('/{id}', [GuruPenggantiController::class, 'show'])->whereNumber('id');
        Route::middleware('role:admin,kurikulum')->group(function () {
            Route::post('/', [GuruPenggantiController::class, 'store']);
            Route::put('/{id}', [GuruPenggantiController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [GuruPenggantiController::class, 'destroy'])->whereNumber('id');
        });
    });

    Route::prefix('izin-guru')->group(function () {
        Route::get('/guru/{guruId}', [IzinGuruController::class, 'getByGuru'])->whereNumber('guruId');
        Route::post('/filter-by-day', [IzinGuruController::class, 'filterByDay']);
        Route::get('/', [IzinGuruController::class, 'index']);
        Route::get('/{id}', [IzinGuruController::class, 'show'])->whereNumber('id');
        Route::post('/', [IzinGuruController::class, 'store'])->middleware('role:admin,kurikulum,guru');
        Route::put('/{id}', [IzinGuruController::class, 'update'])->middleware('role:admin,kurikulum')->whereNumber('id');
        Route::delete('/{id}', [IzinGuruController::class, 'destroy'])->middleware('role:admin,kurikulum')->whereNumber('id');
    });
});
