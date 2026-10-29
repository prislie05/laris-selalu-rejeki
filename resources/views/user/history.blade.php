@extends('layouts.mobile', ['title' => 'Riwayat Pesanan - Toko Lily'])

@section('content')
<div class="py-2">

    <div class="flex items-center justify-between mb-3 pb-2 border-b-2 border-[#1a4734]">
        <h2 class="font-black text-sm text-[#1a4734] uppercase tracking-wide flex items-center space-x-1.5">
            <svg class="w-5 h-5 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Riwayat Pesanan Saya</span>
        </h2>
        <span class="text-xs font-bold text-[#1a4734]">
            {{ $orders->count() }} Transaksi
        </span>
    </div>

    @forelse($orders as $ord)
        <div class="bg-white rounded-lg border-2 border-[#1a4734] p-3 mb-3">
            <!-- Header of Order -->
            <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                <div>
                    <span class="font-black text-xs text-[#1a4734] block">#{{ $ord->order_number }}</span>
                    <span class="text-[10px] text-gray-500">{{ $ord->created_at->format('d M Y, H:i') }}</span>
                </div>
                <!-- Badge LUNAS (#418b24) -->
                <span class="px-2.5 py-0.5 rounded font-black text-[11px] bg-[#418b24] text-white tracking-wide">
                    LUNAS
                </span>
            </div>

            <!-- Items summary -->
            <div class="py-2 space-y-1.5 border-b border-gray-200">
                @foreach($ord->items as $item)
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-semibold text-gray-700 truncate max-w-[240px]">
                            {{ $item->product_name }} &times; {{ $item->quantity }}
                        </span>
                        <span class="font-bold text-[#1a4734]">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Footer: Total & Download Button -->
            <div class="pt-2 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-500 block">Total Pembayaran:</span>
                    <span class="text-sm font-black text-[#870903]">
                        Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                    </span>
                </div>

                <a 
                    href="{{ route('user.receipt', $ord->id) }}" 
                    class="bg-[#1a4734] text-[#f9dd9c] hover:bg-[#143728] px-3 py-1.5 rounded font-extrabold text-xs flex items-center space-x-1.5"
                    title="Unduh Struk Resmi Toko Lily"
                >
                    <svg class="w-4 h-4 text-[#f9dd9c]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Download Struk</span>
                </a>
            </div>
        </div>
    @empty
        <div class="bg-white p-6 rounded-lg border-2 border-[#1a4734] text-center my-6">
            <p class="font-extrabold text-sm text-[#1a4734]">Belum Ada Riwayat Transaksi</p>
            <p class="text-xs text-gray-600 mt-1 mb-3">Pesanan yang Anda buat akan tercatat secara rapi di halaman ini.</p>
            <a href="{{ route('user.shop') }}" class="inline-block bg-[#418b24] text-white px-3.5 py-1.5 rounded font-bold text-xs uppercase">
                Mulai Belanja
            </a>
        </div>
    @endforelse

</div>
@endsection
