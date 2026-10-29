<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                    'data' => null,
                    'errors' => ['auth' => ['Silakan login terlebih dahulu.']],
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        if ($user->is_banned) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun Anda telah dinonaktifkan (banned).',
                    'data' => null,
                    'errors' => ['account' => ['Akun Anda telah di-ban oleh Admin.']],
                ], 403);
            }

            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan oleh Admin.');
        }

        if (!in_array($user->role, $roles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.',
                    'data' => null,
                    'errors' => ['role' => ['Anda tidak memiliki akses ke halaman ini.']],
                ], 403);
            }

            // Redirect user to their own dashboard
            return match ($user->role) {
                'admin' => redirect()->route('admin.revenue'),
                'seller' => redirect()->route('seller.shop'),
                default => redirect()->route('user.shop'),
            };
        }

        return $next($request);
    }
}
