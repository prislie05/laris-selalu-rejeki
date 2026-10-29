<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AuditLogResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\AuditLog;
use App\Models\Product;
use App\Models\SystemErrorLog;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminManagementController extends BaseApiController
{
    /**
     * Get all users for admin management.
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::query()->orderBy('id', 'desc');

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(20);

        return $this->successResponse('Daftar pengguna.', [
            'users' => UserResource::collection($users),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Toggle ban / unban status of user.
     */
    public function toggleBan(Request $request, int $id): JsonResponse
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->id === $request->user()->id) {
            return $this->errorResponse('Anda tidak dapat memblokir akun Anda sendiri.', [
                'user' => ['Tidak diizinkan memblokir diri sendiri.'],
            ], 422);
        }

        $targetUser->is_banned = !$targetUser->is_banned;
        $targetUser->save();

        $action = $targetUser->is_banned ? 'BAN_USER' : 'UNBAN_USER';
        $statusMsg = $targetUser->is_banned ? 'dinonaktifkan (banned)' : 'diaktifkan kembali';

        AuditLogger::log(
            action: $action,
            model: $targetUser,
            newValues: ['is_banned' => $targetUser->is_banned]
        );

        return $this->successResponse("Akun {$targetUser->name} berhasil {$statusMsg}.", [
            'user' => new UserResource($targetUser),
        ]);
    }

    /**
     * Change user role (user, seller, admin).
     */
    public function changeRole(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:user,seller,admin'],
        ]);

        $targetUser = User::findOrFail($id);

        if ($targetUser->id === $request->user()->id) {
            return $this->errorResponse('Anda tidak dapat mengubah role akun Anda sendiri.', [
                'user' => ['Tidak diizinkan mengubah role sendiri.'],
            ], 422);
        }

        $oldRole = $targetUser->role;
        $targetUser->role = $request->input('role');
        $targetUser->save();

        AuditLogger::log(
            action: 'ROLE_CHANGE',
            model: $targetUser,
            oldValues: ['role' => $oldRole],
            newValues: ['role' => $targetUser->role]
        );

        return $this->successResponse("Role akun {$targetUser->name} berhasil diubah menjadi {$targetUser->role}.", [
            'user' => new UserResource($targetUser),
        ]);
    }

    /**
     * Delete an account.
     */
    public function deleteUser(Request $request, int $id): JsonResponse
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->id === $request->user()->id) {
            return $this->errorResponse('Anda tidak dapat menghapus akun Anda sendiri.', [
                'user' => ['Tidak diizinkan menghapus akun aktif Anda.'],
            ], 422);
        }

        $userName = $targetUser->name;
        $targetUser->delete();

        return $this->successResponse("Akun {$userName} berhasil dihapus permanen.");
    }

    /**
     * Get store activity log (audit trail for catalog, products, user mutations).
     */
    public function storeActivityLogs(Request $request): JsonResponse
    {
        $logs = AuditLog::with('user')
            ->orderBy('id', 'desc')
            ->paginate(25);

        $lowStockProducts = Product::where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();

        return $this->successResponse('Log aktivitas etalase dan stok kritis.', [
            'audit_logs' => AuditLogResource::collection($logs),
            'low_stock_products' => ProductResource::collection($lowStockProducts),
            'low_stock_count' => $lowStockProducts->count(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'total' => $logs->total(),
            ],
        ]);
    }

    /**
     * Get system error logs.
     */
    public function systemErrorLogs(): JsonResponse
    {
        $logs = SystemErrorLog::with('user')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return $this->successResponse('Log sistem error tingkat produksi.', [
            'error_logs' => $logs,
        ]);
    }
}
