<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk block akses web panel oleh non-admin
 * Admin: Akses web panel (/admin)
 * Non-admin (guru, siswa, kepsek, kurikulum): Hanya akses via mobile app
 */
class BlockNonAdminWebAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip untuk API endpoints (mobile app)
        if ($request->is('api/*')) {
            return $next($request);
        }

        // Skip login page to allow access for everyone
        if ($request->is('admin/login') || $request->is('admin')) {
            return $next($request);
        }

        // Skip for guest (belum login)
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Jika user bukan admin dan mencoba akses web panel
        if ($user->role !== 'admin' && $request->is('admin/*') && !$request->is('admin/login')) {
            \Log::warning('Non-admin tried to access web panel', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'url' => $request->fullUrl()
            ]);

            auth()->logout();

            return redirect('/admin/login')
                ->with('error', 'Akses ditolak. Role "' . $user->role . '" tidak diizinkan mengakses web panel. Silakan gunakan aplikasi mobile.');
        }

        return $next($request);
    }
}
