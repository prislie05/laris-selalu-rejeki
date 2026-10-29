<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLedgerRequest;
use App\Http\Requests\UpdateLedgerRequest;
use App\Models\AuditLog;
use App\Models\FinancialLedger;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tab 1: Pendapatan Toko (Revenue & Profit Analytics).
     */
    public function revenue(Request $request): View
    {
        $period = $request->input('period', '1_month');

        $startDate = match ($period) {
            '1_week' => Carbon::now()->subDays(7)->startOfDay(),
            '1_month' => Carbon::now()->subDays(30)->startOfDay(),
            '1_year' => Carbon::now()->subYear()->startOfDay(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };

        // Total Income & Expense in period
        $totalIncome = (float) FinancialLedger::where('type', 'income')
            ->where('transaction_date', '>=', $startDate->toDateString())
            ->sum('amount');

        $totalExpense = (float) FinancialLedger::where('type', 'expense')
            ->where('transaction_date', '>=', $startDate->toDateString())
            ->sum('amount');

        $netProfit = $totalIncome - $totalExpense;

        // All order revenue transactions
        $orders = Order::with(['items', 'seller', 'user'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.revenue', compact(
            'period',
            'totalIncome',
            'totalExpense',
            'netProfit',
            'orders'
        ));
    }

    /**
     * Tab 2: Catatan Keuangan (Financial Ledger CRUD).
     */
    public function ledger(Request $request): View
    {
        $typeFilter = $request->input('type');
        $query = FinancialLedger::with('user')->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');

        if ($typeFilter && in_array($typeFilter, ['income', 'expense'], true)) {
            $query->where('type', $typeFilter);
        }

        $ledgers = $query->get();

        // Real-time totals
        $accumulatedIncome = (float) FinancialLedger::where('type', 'income')->sum('amount');
        $accumulatedExpense = (float) FinancialLedger::where('type', 'expense')->sum('amount');
        $currentBalance = $accumulatedIncome - $accumulatedExpense;

        $categories = [
            'income' => ['Penjualan Etalase', 'Penjualan Kasir POS', 'Modal Tambahan', 'Pendapatan Lain'],
            'expense' => ['Belanja Stok Barang', 'Operasional Toko', 'Gaji & Bonus', 'Listrik & Air', 'Pengeluaran Lain'],
        ];

        return view('admin.ledger', compact(
            'ledgers',
            'accumulatedIncome',
            'accumulatedExpense',
            'currentBalance',
            'typeFilter',
            'categories'
        ));
    }

    /**
     * Store financial ledger entry.
     */
    public function storeLedger(StoreLedgerRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        FinancialLedger::create($validated);

        return back()->with('success', "Catatan keuangan ({$validated['type']}) berhasil disimpan!");
    }

    /**
     * Update financial ledger entry.
     */
    public function updateLedger(UpdateLedgerRequest $request, int $id): RedirectResponse
    {
        $ledger = FinancialLedger::findOrFail($id);
        $ledger->update($request->validated());

        return back()->with('success', 'Catatan keuangan berhasil diperbarui!');
    }

    /**
     * Delete financial ledger entry.
     */
    public function destroyLedger(int $id): RedirectResponse
    {
        $ledger = FinancialLedger::findOrFail($id);
        $desc = $ledger->category;
        $ledger->delete();

        return back()->with('success', "Catatan '{$desc}' berhasil dihapus.");
    }

    /**
     * Tab 3: Admin & Manajemen Akun + Log Aktivitas Etalase.
     */
    public function management(Request $request): View
    {
        $users = User::orderBy('id', 'asc')->get();

        // Etalase change logs (CREATE, UPDATE, DELETE, STOCK_CHANGE on Product)
        $catalogLogs = AuditLog::with('user')
            ->where(function ($q) {
                $q->where('model_type', Product::class)
                  ->orWhere('action', 'STOCK_CHANGE');
            })
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        // Peringatan stok menipis (<= 5)
        $lowStockProducts = Product::where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();

        return view('admin.management', compact('users', 'catalogLogs', 'lowStockProducts'));
    }

    /**
     * Toggle ban status of a user.
     */
    public function toggleBanUser(Request $request, int $id): RedirectResponse
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat memblokir akun Anda sendiri!');
        }

        $targetUser->is_banned = !$targetUser->is_banned;
        $targetUser->save();

        $action = $targetUser->is_banned ? 'BAN_USER' : 'UNBAN_USER';
        $statusMsg = $targetUser->is_banned ? 'diblokir (banned)' : 'diaktifkan kembali';

        AuditLogger::log(
            action: $action,
            model: $targetUser,
            newValues: ['is_banned' => $targetUser->is_banned]
        );

        return back()->with('success', "Akun {$targetUser->name} berhasil {$statusMsg}!");
    }

    /**
     * Change user role.
     */
    public function changeUserRole(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:user,seller,admin'],
        ]);

        $targetUser = User::findOrFail($id);

        if ($targetUser->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun sendiri!');
        }

        $oldRole = $targetUser->role;
        $targetUser->role = $request->role;
        $targetUser->save();

        AuditLogger::log(
            action: 'ROLE_CHANGE',
            model: $targetUser,
            oldValues: ['role' => $oldRole],
            newValues: ['role' => $targetUser->role]
        );

        return back()->with('success', "Role {$targetUser->name} berhasil diubah menjadi {$targetUser->role}!");
    }

    /**
     * Delete user account.
     */
    public function deleteUser(int $id): RedirectResponse
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $name = $targetUser->name;
        $targetUser->delete();

        return back()->with('success', "Akun {$name} berhasil dihapus permanen.");
    }
}
