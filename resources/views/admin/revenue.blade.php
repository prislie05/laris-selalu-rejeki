@extends('layouts.mobile', ['title' => 'Pendapatan Toko - Toko Lily', 'pageBg' => '#FFFFFF'])

@section('content')
<div class="py-2">

    <!-- Header & Period Filter (Tipografi Jelas & Besar) -->
    <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-[#1a4734]">
        <h2 class="font-black text-xl text-[#1a4734] uppercase tracking-wide flex items-center space-x-2">
            <svg class="w-6 h-6 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Analitik Pendapatan</span>
        </h2>
    </div>

    <!-- Filter Periode (1 Minggu, 1 Bulan, 1 Tahun: min-h-[48px] text-base font-bold) -->
    <div class="grid grid-cols-3 gap-2 bg-[#1a4734] p-1.5 rounded-xl mb-5">
        <a 
            href="{{ route('admin.revenue', ['period' => '1_week']) }}" 
            class="text-center min-h-[48px] py-3 text-base rounded-lg font-bold transition-all flex items-center justify-center {{ $period === '1_week' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-[#f9dd9c] hover:text-white' }}"
        >
            1 Minggu
        </a>
        <a 
            href="{{ route('admin.revenue', ['period' => '1_month']) }}" 
            class="text-center min-h-[48px] py-3 text-base rounded-lg font-bold transition-all flex items-center justify-center {{ $period === '1_month' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-[#f9dd9c] hover:text-white' }}"
        >
            1 Bulan
        </a>
        <a 
            href="{{ route('admin.revenue', ['period' => '1_year']) }}" 
            class="text-center min-h-[48px] py-3 text-base rounded-lg font-bold transition-all flex items-center justify-center {{ $period === '1_year' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-[#f9dd9c] hover:text-white' }}"
        >
            1 Tahun
        </a>
    </div>

    <!-- Analytics Cards (Solid Flat Colors & Angka Besar 24px - 28px) -->
    <div class="grid grid-cols-2 gap-3 mb-5">
        <!-- Total Pendapatan / Pemasukan -->
        <div class="bg-white p-4 rounded-xl border-2 border-[#418b24] shadow-sm">
            <span class="text-xs font-black text-gray-600 uppercase block">Total Pemasukan</span>
            <span class="text-2xl font-black text-[#418b24] block mt-1 break-words">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </span>
            <span class="text-xs text-gray-500 mt-1.5 block font-semibold">Penjualan & Modal</span>
        </div>

        <!-- Total Pengeluaran -->
        <div class="bg-white p-4 rounded-xl border-2 border-[#870903] shadow-sm">
            <span class="text-xs font-black text-gray-600 uppercase block">Total Pengeluaran</span>
            <span class="text-2xl font-black text-[#870903] block mt-1 break-words">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </span>
            <span class="text-xs text-gray-500 mt-1.5 block font-semibold">Belanja Stok Toko</span>
        </div>

        <!-- Keuntungan Bersih (Net Profit: 28px - 32px font-extrabold) -->
        <div class="col-span-2 bg-[#1a4734] text-white p-5 rounded-xl border-2 border-[#1a4734] shadow-md">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <span class="text-base font-extrabold text-[#f9dd9c] uppercase tracking-wider block">
                        Keuntungan Bersih (Net Profit)
                    </span>
                    <span class="text-3xl font-black {{ $netProfit >= 0 ? 'text-[#f9dd9c]' : 'text-[#e90c00]' }} block mt-1 break-words">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </span>
                </div>
                <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-[#f9dd9c]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-300 mt-2 leading-relaxed">
                Akumulasi laba bersih pada periode {{ $period === '1_week' ? '7 hari terakhir' : ($period === '1_month' ? '30 hari terakhir' : '1 tahun terakhir') }}.
            </p>
        </div>
    </div>

    <!-- Tabel / Daftar Riwayat Pendapatan Lengkap -->
    <div class="bg-white rounded-xl border-2 border-[#1a4734] p-4 mb-4 shadow-sm">
        <h3 class="font-black text-lg text-[#1a4734] uppercase mb-3 flex items-center justify-between pb-2 border-b-2 border-gray-200">
            <span>Riwayat Transaksi Penjualan</span>
            <span class="text-sm text-gray-600 font-extrabold">{{ $orders->count() }} Transaksi</span>
        </h3>

        <div class="divide-y-2 divide-gray-100 max-h-96 overflow-y-auto pr-1">
            @forelse($orders as $ord)
                <div class="py-3 text-base">
                    <div class="flex flex-wrap items-center justify-between gap-1">
                        <span class="font-black text-[#1a4734] text-lg">#{{ $ord->order_number }}</span>
                        <span class="font-black text-xl text-[#418b24]">
                            Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-500 mt-1">
                        <span class="font-semibold">{{ $ord->created_at->format('d/m/Y H:i') }}</span>
                        <span class="px-2 py-0.5 rounded font-black uppercase {{ $ord->source === 'pos_cashier' ? 'bg-[#f9dd9c] text-[#1a4734]' : 'bg-blue-100 text-blue-900' }}">
                            {{ $ord->source === 'pos_cashier' ? 'Kasir POS' : 'User Checkout' }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-700 mt-1.5 flex flex-wrap justify-between gap-1">
                        <span>Plg: <strong class="text-[#1a4734]">{{ $ord->customer_name ?: ($ord->user?->name ?? 'Walk-In') }}</strong></span>
                        <span>Kasir: <strong class="text-[#1a4734]">{{ $ord->seller?->name ?? 'Sistem' }}</strong></span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-base text-gray-500 font-semibold">
                    Belum ada riwayat transaksi pada periode ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
