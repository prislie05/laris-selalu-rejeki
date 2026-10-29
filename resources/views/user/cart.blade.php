@extends('layouts.mobile', ['title' => 'Keranjang Belanja - Toko Lily'])

@section('content')
<div class="py-2" x-data="{
    paymentMethod: 'qris',
    notes: '',
    items: {{ json_encode($carts->map(function($c) {
        return [
            'id' => $c->id,
            'product_id' => $c->product_id,
            'name' => $c->product->name,
            'price' => (float) $c->product->price,
            'stock' => (int) $c->product->stock,
            'quantity' => (int) $c->quantity,
            'is_selected' => (bool) $c->is_selected && $c->product->stock > 0,
            'is_out_of_stock' => $c->product->stock <= 0
        ];
    })) }},
    get totalSelected() {
        return this.items
            .filter(item => item.is_selected && !item.is_out_of_stock)
            .reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },
    get selectedCount() {
        return this.items.filter(item => item.is_selected && !item.is_out_of_stock).length;
    }
}">

    <div class="flex items-center justify-between mb-3 pb-2 border-b-2 border-[#1a4734]">
        <h2 class="font-black text-sm text-[#1a4734] uppercase tracking-wide flex items-center space-x-1.5">
            <svg class="w-5 h-5 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Keranjang Belanja</span>
        </h2>
        <span class="text-xs font-bold text-[#1a4734]">
            {{ $carts->count() }} Macam Barang
        </span>
    </div>

    @if($carts->isEmpty())
        <div class="bg-white p-6 rounded-lg border-2 border-[#1a4734] text-center my-6">
            <div class="w-12 h-12 bg-[#f9dd9c] rounded-full mx-auto flex items-center justify-center mb-2 border border-[#1a4734]">
                <svg class="w-6 h-6 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="font-extrabold text-sm text-[#1a4734]">Keranjang Masih Kosong</h3>
            <p class="text-xs text-gray-600 mt-1 mb-4">Yuk pilih kebutuhan belanja Anda di etalase Toko Lily.</p>
            <a href="{{ route('user.shop') }}" class="inline-block bg-[#418b24] text-white px-4 py-2 rounded font-black text-xs uppercase hover:opacity-90">
                Mulai Belanja Sekarang
            </a>
        </div>
    @else
        <!-- Cart Items List -->
        <div class="space-y-3 mb-6">
            @foreach($carts as $cart)
                @php 
                    $isOutOfStock = $cart->product->stock <= 0;
                @endphp
                <div class="bg-white rounded-lg border-2 border-[#1a4734] p-3 {{ $isOutOfStock ? 'opacity-85 bg-red-50/60' : '' }}">
                    <div class="flex items-start space-x-2.5">
                        
                        <!-- Checkbox (Checklist Selection) -->
                        <div class="pt-1">
                            @if($isOutOfStock)
                                <input 
                                    type="checkbox" 
                                    disabled 
                                    class="w-5 h-5 rounded border-[#870903] text-gray-300 cursor-not-allowed"
                                >
                            @else
                                <form action="{{ route('user.cart.update', $cart->id) }}" method="POST" id="form-toggle-{{ $cart->id }}">
                                    @csrf
                                    <input type="hidden" name="action" value="toggle_select">
                                    <input 
                                        type="checkbox" 
                                        {{ $cart->is_selected ? 'checked' : '' }} 
                                        onchange="document.getElementById('form-toggle-{{ $cart->id }}').submit();"
                                        class="w-5 h-5 rounded border-[#1a4734] text-[#418b24] focus:ring-0 cursor-pointer"
                                    >
                                </form>
                            @endif
                        </div>

                        <!-- Product Thumbnail & Details -->
                        <div class="w-14 h-14 bg-[#f9dd9c]/40 rounded border border-[#1a4734] overflow-hidden flex-shrink-0 flex items-center justify-center">
                            @if($cart->product->image_url)
                                <img src="{{ $cart->product->image_url }}" alt="{{ $cart->product->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="font-black text-[#1a4734] text-[10px]">TL</span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h4 class="font-extrabold text-xs text-[#1a4734] truncate {{ $isOutOfStock ? 'line-through text-gray-500' : '' }}">
                                    {{ $cart->product->name }}
                                </h4>
                                <!-- Hapus Item -->
                                <form action="{{ route('user.cart.delete', $cart->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[#870903] hover:text-[#e90c00] text-xs font-black p-0.5" title="Hapus dari keranjang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <p class="text-xs font-bold text-[#870903] mt-0.5">
                                Rp {{ number_format($cart->product->price, 0, ',', '.') }}
                            </p>

                            <!-- Validasi Stok Habis Real-Time -->
                            @if($isOutOfStock)
                                <div class="mt-1 inline-block bg-[#870903] text-white text-[10px] font-black px-2 py-0.5 rounded">
                                    Stok Habis
                                </div>
                            @else
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex items-center space-x-1.5">
                                        <!-- Decrement -->
                                        <form action="{{ route('user.cart.update', $cart->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="decrement">
                                            <button type="submit" class="w-6 h-6 bg-[#1a4734] text-white rounded font-black text-xs flex items-center justify-center">
                                                -
                                            </button>
                                        </form>

                                        <span class="w-8 text-center font-extrabold text-xs text-[#1a4734]">
                                            {{ $cart->quantity }}
                                        </span>

                                        <!-- Increment -->
                                        <form action="{{ route('user.cart.update', $cart->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="increment">
                                            <button 
                                                type="submit" 
                                                {{ $cart->quantity >= $cart->product->stock ? 'disabled' : '' }}
                                                class="w-6 h-6 bg-[#1a4734] text-white rounded font-black text-xs flex items-center justify-center disabled:opacity-40"
                                            >
                                                +
                                            </button>
                                        </form>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-[10px] text-gray-500 block">Subtotal:</span>
                                        <span class="text-xs font-black text-[#1a4734]">
                                            Rp {{ number_format($cart->product->price * $cart->quantity, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <!-- Checkout & Payment Panel -->
        <div class="bg-white p-4 rounded-lg border-2 border-[#1a4734] sticky bottom-20 z-20 shadow-md">
            <form action="{{ route('user.checkout') }}" method="POST" class="space-y-3">
                @csrf

                <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                    <span class="text-xs font-bold text-[#1a4734]">Barang Dicentang:</span>
                    <span class="text-xs font-black text-[#418b24]" x-text="selectedCount + ' Item'">
                        {{ $carts->where('is_selected', true)->filter(fn($c) => $c->product->stock > 0)->count() }} Item
                    </span>
                </div>

                <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                    <span class="text-xs font-extrabold text-[#1a4734]">Total Tagihan:</span>
                    <span class="text-base font-black text-[#870903]">
                        Rp {{ number_format($selectedTotal, 0, ',', '.') }}
                    </span>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#1a4734] mb-1">Metode Pembayaran</label>
                    <select name="payment_method" class="w-full bg-[#f9dd9c]/40 border border-[#1a4734] px-2.5 py-1.5 rounded text-xs font-bold text-[#1a4734] focus:outline-none">
                        <option value="qris">QRIS (Otomatis / Instan)</option>
                        <option value="cash">Tunai (Bayar Saat Ambil di Toko)</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#1a4734] mb-1">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Contoh: Titip di teras atau bungkus plastik ganda" class="w-full bg-white border border-[#1a4734] px-2.5 py-1 rounded text-xs text-[#1a4734] focus:outline-none">
                </div>

                <button 
                    type="submit" 
                    {{ $selectedTotal <= 0 ? 'disabled' : '' }}
                    class="w-full bg-[#418b24] text-white py-2.5 rounded font-black text-xs uppercase tracking-wider hover:opacity-90 active:scale-[0.99] transition-transform flex items-center justify-center space-x-1.5 disabled:opacity-40 disabled:cursor-not-allowed"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Checkout & Bayar Sekarang</span>
                </button>
            </form>
        </div>
    @endif

</div>
@endsection
