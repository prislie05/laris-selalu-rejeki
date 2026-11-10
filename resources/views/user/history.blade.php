@extends('layouts.mobile', ['title' => 'Riwayat Pesanan - Toko Lily', 'pageBg' => '#FFFFFF'])

@section('content')
<div class="py-2">

    <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-[#1a4734]">
        <h2 class="font-black text-xl text-[#1a4734] uppercase tracking-wide flex items-center space-x-2">
            <svg class="w-6 h-6 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Riwayat Pesanan Saya</span>
        </h2>
        <span class="text-base font-extrabold text-[#1a4734]">
            {{ $orders->count() }} Transaksi
        </span>
    </div>

    @forelse($orders as $ord)
        <div class="bg-white rounded-xl border-2 border-[#1a4734] p-4 mb-4 shadow-sm">
            <!-- Header of Order Card -->
            <div class="flex flex-wrap items-center justify-between gap-1 pb-2.5 border-b-2 border-gray-200">
                <div>
                    <span class="font-black text-lg text-[#1a4734] block">#{{ $ord->order_number }}</span>
                    <span class="text-sm font-semibold text-gray-500">{{ $ord->created_at->format('d M Y, H:i') }}</span>
                </div>
                <!-- Badge LUNAS (#418b24): Minimal 16px font-bold -->
                <span class="px-3.5 py-1.5 rounded-lg font-black text-base bg-[#418b24] text-white tracking-wider flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>LUNAS</span>
                </span>
            </div>

            <!-- Items Summary: 18px - 20px font-semibold -->
            <div class="py-3 space-y-2 border-b-2 border-gray-200">
                @foreach($ord->items as $item)
                    <div class="flex flex-wrap justify-between items-center text-base gap-1">
                        <span class="font-bold text-[#1a4734] leading-snug break-words">
                            {{ $item->product_name }} &times; {{ $item->quantity }}
                        </span>
                        <span class="font-black text-lg text-gray-800">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Footer: Total & Download Struk Button -->
            <div class="pt-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <span class="text-xs text-gray-500 block font-bold uppercase">Total Pembayaran:</span>
                    <span class="text-2xl font-black text-[#870903]">
                        Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Tombol Download Struk: min-h-[50px] text-lg font-bold -->
                <a 
                    href="{{ route('user.receipt', $ord->id) }}" 
                    class="bg-[#1a4734] text-[#f9dd9c] hover:bg-[#143728] active:scale-95 px-4 min-h-[50px] py-2.5 rounded-xl font-black text-lg flex items-center space-x-2 transition-all shadow-sm"
                    title="Unduh Struk Resmi Toko Lily"
                >
                    <svg class="w-5 h-5 text-[#f9dd9c] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Download Struk</span>
                </a>
            </div>
        </div>
    @empty
        <div class="bg-white p-8 rounded-xl border-2 border-[#1a4734] text-center my-6">
            <p class="font-extrabold text-2xl text-[#1a4734]">Belum Ada Riwayat Pesanan</p>
            <p class="text-lg text-gray-700 mt-2 mb-4">Pesanan yang Anda buat akan tercatat secara rapi di sini.</p>
            <a href="{{ route('user.shop') }}" class="inline-block bg-[#418b24] text-white px-6 min-h-[50px] py-3 rounded-xl font-black text-lg uppercase active:scale-95 transition-all">
                Mulai Belanja
            </a>
        </div>
    @endforelse

</div>
@endsection
