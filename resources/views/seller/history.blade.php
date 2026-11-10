@extends('layouts.mobile', ['title' => 'Riwayat Penjualan & Struk - Toko Lily', 'pageBg' => '#FFFFFF'])

@section('content')
<div class="py-2">

    <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-[#1a4734]">
        <h2 class="font-black text-xl text-[#1a4734] uppercase tracking-wide flex items-center space-x-2">
            <svg class="w-6 h-6 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Riwayat Seluruh Penjualan</span>
        </h2>
        <span class="text-base font-extrabold text-[#1a4734]">{{ $orders->count() }} Transaksi</span>
    </div>

    <!-- Struk Digital Sesaat Setelah Transaksi Kasir Berhasil -->
    @if($highlightOrder)
        <div class="bg-white border-2 border-[#418b24] p-5 rounded-xl mb-5 shadow-md">
            <div class="flex flex-wrap items-center justify-between gap-1 pb-2.5 border-b-2 border-gray-300 mb-3">
                <span class="text-base font-black text-[#418b24] uppercase flex items-center space-x-1.5">
                    <svg class="w-5 h-5 text-[#418b24] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Struk Kasir Diterbitkan</span>
                </span>
                <span class="text-xs font-bold text-gray-500">{{ $highlightOrder->created_at->format('H:i:s') }}</span>
            </div>

            <!-- Struk Thermal Visual Card -->
            <div class="bg-gray-50 p-4 rounded-lg border-2 border-dashed border-gray-400 font-mono text-sm space-y-2 mb-4">
                <div class="text-center font-black text-base text-[#1a4734]">TOKO LILY - BUKTI PEMBAYARAN</div>
                <div class="text-center text-xs text-gray-600">No: {{ $highlightOrder->order_number }}</div>
                <div class="border-b-2 border-dashed border-gray-400 my-2"></div>
                
                <div class="flex justify-between text-xs font-bold">
                    <span>Kasir: {{ $highlightOrder->seller?->name ?? 'Kasir POS' }}</span>
                    <span>Plg: {{ $highlightOrder->customer_name }}</span>
                </div>

                <div class="border-b-2 border-dashed border-gray-400 my-2"></div>

                @foreach($highlightOrder->items as $itm)
                    <div class="flex justify-between text-sm">
                        <span>{{ $itm->product_name }} &times; {{ $itm->quantity }}</span>
                        <span class="font-bold">Rp {{ number_format($itm->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach

                <div class="border-b-2 border-dashed border-gray-400 my-2"></div>

                <div class="flex justify-between font-black text-lg">
                    <span>TOTAL:</span>
                    <span class="text-[#870903]">Rp {{ number_format($highlightOrder->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>Metode:</span>
                    <span class="font-bold">{{ strtoupper($highlightOrder->payment_method) }}</span>
                </div>
                @if($highlightOrder->cash_received !== null)
                    <div class="flex justify-between text-xs">
                        <span>Uang Diterima:</span>
                        <span>Rp {{ number_format($highlightOrder->cash_received, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-[#418b24]">
                        <span>Kembalian:</span>
                        <span>Rp {{ number_format($highlightOrder->cash_change ?? 0, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <a 
                href="{{ route('seller.receipt', $highlightOrder->id) }}" 
                class="w-full bg-[#1a4734] text-[#f9dd9c] min-h-[50px] py-3 rounded-xl font-black text-lg flex items-center justify-center space-x-2 hover:bg-[#143728] active:scale-95 transition-all shadow-sm"
            >
                <svg class="w-6 h-6 text-[#f9dd9c] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Download Struk Pembayaran (PDF)</span>
            </a>
        </div>
    @endif

    <!-- All Orders List -->
    <div class="space-y-4">
        @forelse($orders as $ord)
            <div class="bg-white rounded-xl border-2 border-[#1a4734] p-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-1 pb-2 border-b-2 border-gray-200">
                    <div>
                        <span class="font-black text-lg text-[#1a4734] block">#{{ $ord->order_number }}</span>
                        <div class="flex items-center space-x-2 text-xs text-gray-500 mt-0.5">
                            <span class="font-semibold">{{ $ord->created_at->format('d/m/Y H:i') }}</span>
                            <span>•</span>
                            <span class="font-black uppercase px-2 py-0.5 rounded {{ $ord->source === 'pos_cashier' ? 'bg-[#f9dd9c] text-[#1a4734]' : 'bg-blue-100 text-blue-900' }}">
                                {{ $ord->source === 'pos_cashier' ? 'Kasir POS' : 'Checkout User' }}
                            </span>
                        </div>
                    </div>
                    <span class="px-3.5 py-1.5 rounded-lg font-black text-base bg-[#418b24] text-white">
                        LUNAS
                    </span>
                </div>

                <div class="py-3 text-base space-y-1.5">
                    <div class="text-sm text-gray-600 mb-1">
                        Pelanggan: <span class="font-black text-[#1a4734] text-base">{{ $ord->customer_name ?: ($ord->user?->name ?? 'Walk-In') }}</span>
                    </div>
                    @foreach($ord->items as $item)
                        <div class="flex justify-between text-gray-800">
                            <span class="break-words font-semibold max-w-[260px]">{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                            <span class="font-bold text-[#1a4734]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-3 border-t-2 border-gray-200 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="text-xs text-gray-500 block font-bold uppercase">Total Transaksi:</span>
                        <span class="text-2xl font-black text-[#870903]">
                            Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <a 
                        href="{{ route('seller.receipt', $ord->id) }}" 
                        class="bg-[#1a4734] text-[#f9dd9c] hover:bg-[#143728] active:scale-95 px-4 min-h-[48px] py-2.5 rounded-xl font-bold text-base flex items-center space-x-1.5 transition-all shadow-sm"
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
                <p class="font-extrabold text-xl text-[#1a4734]">Belum Ada Riwayat Penjualan</p>
                <p class="text-base text-gray-700 mt-2">Seluruh transaksi kasir dan pesanan mandiri akan tercatat di sini.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
