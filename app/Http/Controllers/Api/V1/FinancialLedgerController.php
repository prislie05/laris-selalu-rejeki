<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreLedgerRequest;
use App\Http\Requests\UpdateLedgerRequest;
use App\Http\Resources\FinancialLedgerResource;
use App\Models\FinancialLedger;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialLedgerController extends BaseApiController
{
    /**
     * Display a listing of financial ledger entries with search and filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FinancialLedger::with(['user', 'order'])->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('period')) {
            $period = $request->input('period');
            $startDate = match ($period) {
                '1_week' => Carbon::now()->subDays(7)->toDateString(),
                '1_month' => Carbon::now()->subDays(30)->toDateString(),
                '1_year' => Carbon::now()->subYear()->toDateString(),
                default => null,
            };

            if ($startDate) {
                $query->where('transaction_date', '>=', $startDate);
            }
        }

        $ledgers = $query->paginate(20);

        return $this->successResponse('Catatan keuangan toko.', [
            'ledgers' => FinancialLedgerResource::collection($ledgers),
            'pagination' => [
                'current_page' => $ledgers->currentPage(),
                'last_page' => $ledgers->lastPage(),
                'total' => $ledgers->total(),
            ],
        ]);
    }

    /**
     * Financial analytics summary with 1 week, 1 month, 1 year period filter.
     */
    public function summary(Request $request): JsonResponse
    {
        $period = $request->input('period', '1_month');
        $startDate = match ($period) {
            '1_week' => Carbon::now()->subDays(7)->toDateString(),
            '1_month' => Carbon::now()->subDays(30)->toDateString(),
            '1_year' => Carbon::now()->subYear()->toDateString(),
            default => Carbon::now()->subDays(30)->toDateString(),
        };

        $incomeQuery = FinancialLedger::where('type', 'income')->where('transaction_date', '>=', $startDate);
        $expenseQuery = FinancialLedger::where('type', 'expense')->where('transaction_date', '>=', $startDate);

        $totalIncome = (float) $incomeQuery->sum('amount');
        $totalExpense = (float) $expenseQuery->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $ordersCount = Order::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->count();

        return $this->successResponse('Ringkasan analitik keuangan toko.', [
            'period' => $period,
            'start_date' => $startDate,
            'total_income' => $totalIncome,
            'formatted_total_income' => 'Rp ' . number_format($totalIncome, 0, ',', '.'),
            'total_expense' => $totalExpense,
            'formatted_total_expense' => 'Rp ' . number_format($totalExpense, 0, ',', '.'),
            'net_profit' => $netProfit,
            'formatted_net_profit' => 'Rp ' . number_format($netProfit, 0, ',', '.'),
            'total_orders' => $ordersCount,
        ]);
    }

    /**
     * Store a new financial ledger entry.
     */
    public function store(StoreLedgerRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $ledger = FinancialLedger::create($validated);
        $ledger->load('user');

        return $this->successResponse('Catatan keuangan berhasil disimpan.', [
            'ledger' => new FinancialLedgerResource($ledger),
        ], 201);
    }

    /**
     * Update an existing financial ledger entry.
     */
    public function update(UpdateLedgerRequest $request, int $id): JsonResponse
    {
        $ledger = FinancialLedger::findOrFail($id);
        $ledger->update($request->validated());
        $ledger->load('user');

        return $this->successResponse('Catatan keuangan berhasil diperbarui.', [
            'ledger' => new FinancialLedgerResource($ledger),
        ]);
    }

    /**
     * Delete a financial ledger entry.
     */
    public function destroy(int $id): JsonResponse
    {
        $ledger = FinancialLedger::findOrFail($id);
        $ledger->delete();

        return $this->successResponse('Catatan keuangan berhasil dihapus.');
    }
}
