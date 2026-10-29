<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_banned) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun Anda dinonaktifkan (banned).',
                    'data' => null,
                    'errors' => ['account' => ['Akun Anda telah di-ban oleh Admin.']],
                ], 403);
            }

            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan oleh Admin.');
        }

        return $next($request);
    }
}
