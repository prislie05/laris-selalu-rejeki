@extends('layouts.mobile', ['title' => 'Riwayat Penjualan & Struk - Toko Lily'])

@section('content')
<div class="py-2">

    <div class="flex items-center justify-between mb-3 pb-2 border-b-2 border-[#1a4734]">
        <h2 class="font-black text-sm text-[#1a4734] uppercase tracking-wide flex items-center space-x-1.5">
            <svg class="w-5 h-5 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Riwayat Seluruh Penjualan</span>
        </h2>
        <span class="text-xs font-bold text-[#1a4734]">{{ $orders->count() }} Transaksi</span>
    </div>

    <!-- Struk Digital Sesaat Setelah Transaksi Kasir Berhasil -->
    @if($highlightOrder)
        <div class="bg-white border-2 border-[#418b24] p-4 rounded-lg mb-4 shadow-sm">
            <div class="flex items-center justify-between pb-2 border-b border-gray-300 mb-2">
                <span class="text-xs font-black text-[#418b24] uppercase flex items-center space-x-1">
                    <svg class="w-4 h-4 text-[#418b24]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Struk Kasir Diterbitkan</span>
                </span>
                <span class="text-[10px] font-bold text-gray-500">{{ $highlightOrder->created_at->format('H:i') }}</span>
            </div>

            <!-- Struk Thermal Visual representation -->
            <div class="bg-gray-50 p-3 rounded border border-dashed border-gray-400 font-mono text-[11px] space-y-1 mb-3">
                <div class="text-center font-bold text-xs text-[#1a4734]">TOKO LILY - BUKTI PEMBAYARAN</div>
                <div class="text-center text-[10px] text-gray-500">No: {{ $highlightOrder->order_number }}</div>
                <div class="border-b border-dashed border-gray-400 my-1.5"></div>
                
                <div class="flex justify-between text-[10px]">
                    <span>Kasir: {{ $highlightOrder->seller?->name ?? 'Kasir POS' }}</span>
                    <span>Plg: {{ $highlightOrder->customer_name }}</span>
                </div>

                <div class="border-b border-dashed border-gray-400 my-1.5"></div>

                @foreach($highlightOrder->items as $itm)
                    <div class="flex justify-between">
                        <span>{{ $itm->product_name }} &times; {{ $itm->quantity }}</span>
                        <span>Rp {{ number_format($itm->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach

                <div class="border-b border-dashed border-gray-400 my-1.5"></div>

                <div class="flex justify-between font-bold text-xs">
                    <span>TOTAL:</span>
                    <span class="text-[#870903]">Rp {{ number_format($highlightOrder->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-[10px]">
                    <span>Metode:</span>
                    <span>{{ strtoupper($highlightOrder->payment_method) }}</span>
                </div>
                @if($highlightOrder->cash_received !== null)
                    <div class="flex justify-between text-[10px]">
                        <span>Uang Diterima:</span>
                        <span>Rp {{ number_format($highlightOrder->cash_received, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[10px] font-bold text-[#418b24]">
                        <span>Kembalian:</span>
                        <span>Rp {{ number_format($highlightOrder->cash_change ?? 0, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <a 
                href="{{ route('seller.receipt', $highlightOrder->id) }}" 
                class="w-full bg-[#1a4734] text-[#f9dd9c] py-2 rounded font-extrabold text-xs flex items-center justify-center space-x-1.5 hover:bg-[#143728]"
            >
                <svg class="w-4 h-4 text-[#f9dd9c]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Download Struk Pembayaran (PDF)</span>
            </a>
        </div>
    @endif

    <!-- All Orders List -->
    <div class="space-y-3">
        @forelse($orders as $ord)
            <div class="bg-white rounded-lg border-2 border-[#1a4734] p-3">
                <div class="flex items-center justify-between pb-1.5 border-b border-gray-200">
                    <div>
                        <span class="font-black text-xs text-[#1a4734] block">#{{ $ord->order_number }}</span>
                        <div class="flex items-center space-x-1 text-[10px] text-gray-500">
                            <span>{{ $ord->created_at->format('d/m/Y H:i') }}</span>
                            <span>•</span>
                            <span class="font-bold uppercase {{ $ord->source === 'pos_cashier' ? 'text-[#e90c00]' : 'text-[#418b24]' }}">
                                {{ $ord->source === 'pos_cashier' ? 'Kasir POS' : 'Checkout User' }}
                            </span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded font-black text-[10px] bg-[#418b24] text-white">
                        LUNAS
                    </span>
                </div>

                <div class="py-2 text-xs space-y-1">
                    <div class="text-[11px] text-gray-600 mb-1">
                        Pelanggan: <span class="font-bold text-[#1a4734]">{{ $ord->customer_name ?: ($ord->user?->name ?? 'Walk-In') }}</span>
                    </div>
                    @foreach($ord->items as $item)
                        <div class="flex justify-between text-gray-700">
                            <span class="truncate max-w-[210px]">{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                            <span class="font-bold text-[#1a4734]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2 border-t border-gray-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-500 block">Total Transaksi:</span>
                        <span class="text-xs font-black text-[#870903]">
                            Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <a 
                        href="{{ route('seller.receipt', $ord->id) }}" 
                        class="bg-[#1a4734] text-[#f9dd9c] hover:bg-[#143728] px-3 py-1.5 rounded font-bold text-xs flex items-center space-x-1"
                    >
                        <svg class="w-3.5 h-3.5 text-[#f9dd9c]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Download Struk</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 rounded-lg border-2 border-[#1a4734] text-center">
                <p class="font-bold text-xs text-[#1a4734]">Belum ada riwayat transaksi penjualan.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
