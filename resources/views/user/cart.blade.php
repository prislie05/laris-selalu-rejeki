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

    <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-[#1a4734]">
        <h2 class="font-black text-xl text-[#1a4734] uppercase tracking-wide flex items-center space-x-2">
            <svg class="w-6 h-6 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Keranjang Belanja</span>
        </h2>
        <span class="text-base font-extrabold text-[#1a4734]">
            {{ $carts->count() }} Macam
        </span>
    </div>

    @if($carts->isEmpty())
        <div class="bg-white p-8 rounded-xl border-2 border-[#1a4734] text-center my-6">
            <div class="w-16 h-16 bg-[#f9dd9c] rounded-full mx-auto flex items-center justify-center mb-3 border-2 border-[#1a4734]">
                <svg class="w-8 h-8 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="font-black text-2xl text-[#1a4734]">Keranjang Masih Kosong</h3>
            <p class="text-lg text-gray-700 mt-2 mb-6">Pilih barang kebutuhan harian Anda di etalase Toko Lily.</p>
            <a href="{{ route('user.shop') }}" class="inline-block bg-[#418b24] text-white px-6 min-h-[50px] py-3 rounded-xl font-black text-lg uppercase hover:opacity-90 active:scale-95 transition-all">
                Mulai Belanja Sekarang
            </a>
        </div>
    @else
        <!-- Cart Items List (Checkbox & Tombol Sentuh Ramah Jari Orang Tua) -->
        <div class="space-y-4 mb-6">
            @foreach($carts as $cart)
                @php 
                    $isOutOfStock = $cart->product->stock <= 0;
                @endphp
                <div class="bg-white rounded-xl border-2 border-[#1a4734] p-4 {{ $isOutOfStock ? 'opacity-85 bg-red-50/80 border-[#870903]' : '' }} shadow-sm">
                    <div class="flex items-start space-x-3.5">
                        
                        <!-- Checkbox (Checklist Selection: Minimal 24x24px w-6 h-6) -->
                        <div class="pt-1.5 flex-shrink-0">
                            @if($isOutOfStock)
                                <input 
                                    type="checkbox" 
                                    disabled 
                                    class="w-6 h-6 rounded-md border-2 border-[#870903] text-gray-300 cursor-not-allowed"
                                >
                            @else
                                <form action="{{ route('user.cart.update', $cart->id) }}" method="POST" id="form-toggle-{{ $cart->id }}">
                                    @csrf
                                    <input type="hidden" name="action" value="toggle_select">
                                    <input 
                                        type="checkbox" 
                                        {{ $cart->is_selected ? 'checked' : '' }} 
                                        onchange="document.getElementById('form-toggle-{{ $cart->id }}').submit();"
                                        class="w-6 h-6 rounded-md border-2 border-[#1a4734] text-[#418b24] focus:ring-0 cursor-pointer"
                                    >
                                </form>
                            @endif
                        </div>

                        <!-- Product Details (Tanpa Foto) -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-1">
                                <!-- Nama Barang: 20px font-bold -->
                                <h4 class="font-bold text-xl text-[#1a4734] leading-snug break-words {{ $isOutOfStock ? 'line-through text-gray-500' : '' }}">
                                    {{ $cart->product->name }}
                                </h4>
                                <!-- Hapus Item -->
                                <form action="{{ route('user.cart.delete', $cart->id) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    <button type="submit" class="text-[#870903] hover:text-[#e90c00] p-1 active:scale-90" title="Hapus dari keranjang">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <!-- Harga Satuan: 22px font-extrabold -->
                            <p class="text-xl font-black text-[#870903] mt-1">
                                Rp {{ number_format($cart->product->price, 0, ',', '.') }}
                            </p>

                            <!-- Validasi Stok Habis Real-Time (Badge Tegas #870903) -->
                            @if($isOutOfStock)
                                <div class="mt-2 inline-block bg-[#870903] text-white text-base font-black px-3 py-1 rounded-md">
                                    Stok Habis
                                </div>
                            @else
                                <div class="flex flex-wrap items-center justify-between gap-2 mt-3 pt-2 border-t border-gray-200">
                                    <!-- Tombol +/- Kuantitas (Ukuran Minimal 44x44px w-11 h-11 & Angka text-xl) -->
                                    <div class="flex items-center space-x-2">
                                        <!-- Decrement -->
                                        <form action="{{ route('user.cart.update', $cart->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="decrement">
                                            <button type="submit" class="w-11 h-11 bg-[#1a4734] text-white rounded-lg font-black text-xl flex items-center justify-center active:scale-95">
                                                -
                                            </button>
                                        </form>

                                        <span class="w-10 text-center font-black text-xl text-[#1a4734]">
                                            {{ $cart->quantity }}
                                        </span>

                                        <!-- Increment -->
                                        <form action="{{ route('user.cart.update', $cart->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="increment">
                                            <button 
                                                type="submit" 
                                                {{ $cart->quantity >= $cart->product->stock ? 'disabled' : '' }}
                                                class="w-11 h-11 bg-[#1a4734] text-white rounded-lg font-black text-xl flex items-center justify-center active:scale-95 disabled:opacity-40"
                                            >
                                                +
                                            </button>
                                        </form>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-xs text-gray-500 block font-semibold">Subtotal:</span>
                                        <span class="text-xl font-black text-[#1a4734]">
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
        <div class="bg-white p-5 rounded-xl border-2 border-[#1a4734] sticky bottom-24 z-20 shadow-lg">
            <form action="{{ route('user.checkout') }}" method="POST" class="space-y-4">
                @csrf

                <div class="flex items-center justify-between pb-2.5 border-b border-gray-200">
                    <span class="text-base font-bold text-[#1a4734]">Barang Dicentang:</span>
                    <span class="text-lg font-black text-[#418b24]" x-text="selectedCount + ' Item'">
                        {{ $carts->where('is_selected', true)->filter(fn($c) => $c->product->stock > 0)->count() }} Item
                    </span>
                </div>

                <div class="flex flex-wrap items-center justify-between pb-3 border-b-2 border-gray-300 gap-1">
                    <span class="text-lg font-extrabold text-[#1a4734]">Total Tagihan:</span>
                    <span class="text-3xl font-black text-[#870903]">
                        Rp {{ number_format($selectedTotal, 0, ',', '.') }}
                    </span>
                </div>

                <div>
                    <label class="block text-base font-bold text-[#1a4734] mb-1.5">Pilih Cara Pembayaran</label>
                    <select name="payment_method" class="w-full bg-[#f9dd9c]/40 border-2 border-[#1a4734] px-4 py-3 rounded-xl text-lg font-bold text-[#1a4734] focus:outline-none">
                        <option value="qris">QRIS (Otomatis / Instan)</option>
                        <option value="cash">Tunai (Bayar Saat Ambil di Toko)</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>

                <div>
                    <label class="block text-base font-bold text-[#1a4734] mb-1.5">Catatan Pesanan (Opsional)</label>
                    <input 
                        type="text" 
                        name="notes" 
                        placeholder="Contoh: Titip di teras atau bungkus ganda" 
                        class="w-full bg-white border-2 border-[#1a4734] px-4 py-2.5 rounded-xl text-base text-[#1a4734] focus:outline-none"
                    >
                </div>

                <button 
                    type="submit" 
                    {{ $selectedTotal <= 0 ? 'disabled' : '' }}
                    class="w-full bg-[#418b24] text-white min-h-[52px] py-3.5 rounded-xl font-black text-xl uppercase tracking-wider hover:opacity-90 active:scale-95 transition-all flex items-center justify-center space-x-2 disabled:opacity-40 disabled:cursor-not-allowed shadow-md"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Checkout & Bayar Sekarang</span>
                </button>
            </form>
        </div>
    @endif

</div>
@endsection
