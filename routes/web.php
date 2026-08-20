<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TemplateDownloadController;

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Health check untuk desktop/web
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Aplikasi Monitoring Kelas',
        'type' => 'web',
        'timestamp' => now()->toIso8601String(),
        'endpoints' => [
            'admin_panel' => url('/admin'),
            'api' => url('/api'),
        ]
    ]);
});

// Regular web authentication routes (for non-admin users)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', function () {
    return redirect()->route('logout');
})->name('logout.get');

// Template download routes (protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/download/template/mata-pelajaran', [TemplateDownloadController::class, 'downloadMataPelajaran'])->name('download.template.mata-pelajaran');
    Route::get('/download/template/siswa', [TemplateDownloadController::class, 'downloadSiswa'])->name('download.template.siswa');
    Route::get('/download/template/guru', [TemplateDownloadController::class, 'downloadGuru'])->name('download.template.guru');
    Route::get('/download/template/kelas', [TemplateDownloadController::class, 'downloadKelas'])->name('download.template.kelas');
    Route::get('/download/template/jadwal', [TemplateDownloadController::class, 'downloadJadwal'])->name('download.template.jadwal');
    Route::get('/download/template/user', [TemplateDownloadController::class, 'downloadUser'])->name('download.template.user');
    Route::get('/download/template/izin-guru', [TemplateDownloadController::class, 'downloadIzinGuru'])->name('download.template.izin-guru');
    Route::get('/download/template/guru-pengganti', [TemplateDownloadController::class, 'downloadGuruPengganti'])->name('download.template.guru-pengganti');
    Route::get('/download/template/kehadiran', [TemplateDownloadController::class, 'downloadKehadiran'])->name('download.template.kehadiran');
});

// Admin panel specific routes
// Jangan daftarkan route admin logout di sini, biarkan Filament yang mengelola

// Home route - this can be accessed after login
Route::get('/home', function () {
    if (auth()->check()) {
        // If user is authenticated, redirect based on role
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            return redirect('/admin');
        } else {
            // Redirect regular users elsewhere (you can adjust this logic)
            return redirect('/');
        }
    }
    return redirect('/login');
})->middleware('auth')->name('home');

// Additional routes can be added here if needed