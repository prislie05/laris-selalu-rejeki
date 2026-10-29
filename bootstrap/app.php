<?php

use App\Http\Middleware\CheckBannedUser;
use App\Http\Middleware\RoleMiddleware;
use App\Models\SystemErrorLog;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'check.banned' => CheckBannedUser::class,
        ]);

        $middleware->web(append: [
            CheckBannedUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->report(function (Throwable $e) {
            try {
                if (Schema::hasTable('system_error_logs')) {
                    $request = request();
                    $payload = [];

                    if ($request) {
                        $payload = $request->except(['password', 'password_confirmation', 'token', 'secret']);
                    }

                    SystemErrorLog::create([
                        'user_id' => Auth::id(),
                        'route' => $request?->path(),
                        'method' => $request?->method(),
                        'error_message' => $e->getMessage() ?: get_class($e),
                        'stack_trace' => substr($e->getTraceAsString(), 0, 5000),
                        'payload' => $payload,
                        'severity_level' => $e instanceof \Error ? 'critical' : 'error',
                    ]);
                }
            } catch (\Throwable $loggingException) {
                // Silently avoid recursion if logging itself fails
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal.',
                    'data' => null,
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                    'data' => null,
                    'errors' => ['auth' => ['Silakan login terlebih dahulu.']],
                ], 401);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data atau endpoint tidak ditemukan.',
                    'data' => null,
                    'errors' => ['not_found' => [$e->getMessage() ?: 'Resource tidak ditemukan']],
                ], 404);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage() ?: 'Terjadi kesalahan sistem internal.',
                    'data' => null,
                    'errors' => ['exception' => [$e->getMessage()]],
                ], $status >= 400 && $status < 600 ? $status : 500);
            }
        });
    })->create();
