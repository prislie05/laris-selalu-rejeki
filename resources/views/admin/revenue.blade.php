@extends('layouts.mobile', ['title' => 'Pendapatan Toko - Toko Lily'])

@section('content')
<div class="py-2">

    <!-- Header & Period Filter -->
    <div class="flex items-center justify-between mb-3 pb-2 border-b-2 border-[#1a4734]">
        <h2 class="font-black text-sm text-[#1a4734] uppercase tracking-wide flex items-center space-x-1.5">
            <svg class="w-5 h-5 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Analitik Pendapatan</span>
        </h2>
    </div>

    <!-- Filter Periode (1 Minggu, 1 Bulan, 1 Tahun) -->
    <div class="grid grid-cols-3 gap-1.5 bg-[#1a4734] p-1.5 rounded-lg mb-4">
        <a 
            href="{{ route('admin.revenue', ['period' => '1_week']) }}" 
            class="text-center py-1.5 text-xs rounded font-bold transition-colors {{ $period === '1_week' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-[#f9dd9c] hover:text-white' }}"
        >
            1 Minggu
        </a>
        <a 
            href="{{ route('admin.revenue', ['period' => '1_month']) }}" 
            class="text-center py-1.5 text-xs rounded font-bold transition-colors {{ $period === '1_month' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-[#f9dd9c] hover:text-white' }}"
        >
            1 Bulan
        </a>
        <a 
            href="{{ route('admin.revenue', ['period' => '1_year']) }}" 
            class="text-center py-1.5 text-xs rounded font-bold transition-colors {{ $period === '1_year' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-[#f9dd9c] hover:text-white' }}"
        >
            1 Tahun
        </a>
    </div>

    <!-- Analytics Cards (Solid Flat Colors) -->
    <div class="grid grid-cols-2 gap-2 mb-4">
        <!-- Total Pendapatan / Pemasukan -->
        <div class="bg-white p-3 rounded-lg border-2 border-[#418b24]">
            <span class="text-[10px] font-bold text-gray-500 uppercase block">Total Pemasukan</span>
            <span class="text-sm font-black text-[#418b24] block mt-0.5">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </span>
            <span class="text-[9px] text-gray-400 mt-1 block">Dari Penjualan & Modal</span>
        </div>

        <!-- Total Pengeluaran -->
        <div class="bg-white p-3 rounded-lg border-2 border-[#870903]">
            <span class="text-[10px] font-bold text-gray-500 uppercase block">Total Pengeluaran</span>
            <span class="text-sm font-black text-[#870903] block mt-0.5">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </span>
            <span class="text-[9px] text-gray-400 mt-1 block">Belanja Stok & Operasional</span>
        </div>

        <!-- Keuntungan Bersih (Net Profit) -->
        <div class="col-span-2 bg-[#1a4734] text-white p-3.5 rounded-lg border-2 border-[#1a4734]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-[#f9dd9c] uppercase tracking-wide block">
                        Keuntungan Bersih (Net Profit)
                    </span>
                    <span class="text-xl font-black {{ $netProfit >= 0 ? 'text-[#f9dd9c]' : 'text-[#e90c00]' }} block mt-0.5">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#f9dd9c]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-[10px] text-gray-300 mt-1">
                Akumulasi laba bersih pada periode {{ $period === '1_week' ? '7 hari terakhir' : ($period === '1_month' ? '30 hari terakhir' : '1 tahun terakhir') }}.
            </p>
        </div>
    </div>

    <!-- Tabel / Daftar Riwayat Pendapatan Lengkap -->
    <div class="bg-white rounded-lg border-2 border-[#1a4734] p-3 mb-3">
        <h3 class="font-black text-xs text-[#1a4734] uppercase mb-2 flex items-center justify-between pb-1 border-b border-gray-200">
            <span>Riwayat Transaksi Penjualan</span>
            <span class="text-[10px] text-gray-500 font-bold">{{ $orders->count() }} Transaksi</span>
        </h3>

        <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto pr-1">
            @forelse($orders as $ord)
                <div class="py-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-black text-[#1a4734]">#{{ $ord->order_number }}</span>
                        <span class="font-black text-[#418b24]">
                            Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-[10px] text-gray-500 mt-0.5">
                        <span>{{ $ord->created_at->format('d/m/Y H:i') }}</span>
                        <span class="px-1.5 py-0.2 rounded font-bold uppercase {{ $ord->source === 'pos_cashier' ? 'bg-[#f9dd9c] text-[#1a4734]' : 'bg-blue-100 text-blue-800' }}">
                            {{ $ord->source === 'pos_cashier' ? 'Kasir POS' : 'User Checkout' }}
                        </span>
                    </div>

                    <div class="text-[10px] text-gray-600 mt-1 flex justify-between">
                        <span>Plg: {{ $ord->customer_name ?: ($ord->user?->name ?? 'Walk-In') }}</span>
                        <span>Kasir: {{ $ord->seller?->name ?? 'Sistem' }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-xs text-gray-500">
                    Belum ada riwayat transaksi pada periode ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
