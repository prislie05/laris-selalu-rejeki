<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Toko Lily' }}</title>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-[#f0f2f5] text-[#1a4734] antialiased" x-data="{ showProfileModal: false }">

    <!-- Universal Fluid Mobile Container (320px - 480px+) -->
    <div class="w-full max-w-[480px] min-h-[100dvh] mx-auto overflow-x-hidden flex flex-col justify-between shadow-none border-x-2 border-[#1a4734] bg-[#f9dd9c] relative">

        <!-- Top App Bar (Semua Role) -->
        <header class="w-full bg-[#1a4734] text-white px-4 py-3.5 sticky top-0 z-30 flex items-center justify-between border-b-2 border-[#1a4734]">
            <div class="flex items-center space-x-2.5 min-w-0">
                <a href="{{ url('/') }}" class="font-black text-2xl tracking-wide text-[#f9dd9c] flex items-center space-x-1.5 truncate">
                    <span>TOKO LILY</span>
                </a>
                <span class="text-xs px-2.5 py-1 rounded font-black uppercase flex-shrink-0 {{ Auth::user()?->role === 'admin' ? 'bg-[#870903] text-white' : (Auth::user()?->role === 'seller' ? 'bg-[#e90c00] text-white' : 'bg-[#418b24] text-white') }}">
                    {{ Auth::user()?->role ?? 'Tamu' }}
                </span>
            </div>

            @auth
            <!-- Menu Profil di Pojok Kanan Atas -->
            <button 
                type="button" 
                @click="showProfileModal = true"
                class="w-11 h-11 rounded-full bg-[#f9dd9c] text-[#1a4734] flex items-center justify-center font-bold border-2 border-white hover:opacity-90 active:scale-95 transition-all flex-shrink-0 ml-2"
                title="Profil Saya"
                aria-label="Profil Akun"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#1a4734]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </button>
            @else
            <a href="{{ route('login') }}" class="text-base font-bold bg-[#418b24] text-white px-4 py-2 rounded-lg">
                Login
            </a>
            @endauth
        </header>

        <!-- Flash Notifications (Besar & Jelas untuk Lansia) -->
        <div class="w-full px-4 pt-3">
            @if(session('success'))
                <div class="bg-[#418b24] text-white px-4 py-3 rounded-lg text-base font-bold mb-2.5 flex items-start space-x-2.5 shadow-sm">
                    <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="leading-snug break-words">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-[#870903] text-white px-4 py-3 rounded-lg text-base font-bold mb-2.5 flex items-start space-x-2.5 shadow-sm">
                    <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="leading-snug break-words">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-[#870903] text-white px-4 py-3 rounded-lg text-base font-bold mb-2.5">
                    <p class="font-extrabold text-lg mb-1 flex items-center space-x-1.5">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Perhatian:</span>
                    </p>
                    <ul class="list-disc list-inside space-y-1 pl-1">
                        @foreach($errors->all() as $err)
                            <li class="leading-snug">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 w-full px-4 pt-1">
            @yield('content')

            @auth
            <!-- Spacer Pengaman Bawah agar seluruh fitur & tombol paling bawah 100% terlihat di atas Bottom Navbar saat di-scroll -->
            <div style="height: 120px; width: 100%; min-height: 120px;" class="w-full flex-shrink-0 pointer-events-none" aria-hidden="true"></div>
            @endauth
        </main>

        <!-- Bottom Navigation Bar (Fixed di bawah - 3 Menu Khusus Tiap Role) -->
        @auth
        <nav class="fixed bottom-0 left-0 right-0 w-full max-w-[480px] mx-auto bg-[#1a4734] border-t-2 border-[#143728] z-40 shadow-xl">
            <div class="grid grid-cols-3 h-16">
                @if(Auth::user()->isUser())
                    <!-- USER MENU 1: Belanja -->
                    @php $isShop = request()->routeIs('user.shop'); @endphp
                    <a href="{{ route('user.shop') }}" class="flex flex-col items-center justify-center py-1 {{ $isShop ? 'text-[#418b24] bg-[#143728] font-black' : 'text-[#f9dd9c] hover:text-white font-bold' }}">
                        <svg class="w-6 h-6 flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="text-xs font-bold leading-tight mt-0.5">Belanja</span>
                    </a>

                    <!-- USER MENU 2: Keranjang -->
                    @php 
                        $isCart = request()->routeIs('user.cart'); 
                        $cartQty = \App\Models\Cart::where('user_id', Auth::id())->sum('quantity');
                    @endphp
                    <a href="{{ route('user.cart') }}" class="flex flex-col items-center justify-center py-1 relative {{ $isCart ? 'text-[#418b24] bg-[#143728] font-black' : 'text-[#f9dd9c] hover:text-white font-bold' }}">
                        <div class="relative">
                            <svg class="w-6 h-6 flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @if($cartQty > 0)
                                <span class="absolute -top-1.5 -right-2.5 bg-[#e90c00] text-white text-[11px] font-black min-w-4 h-4 px-1 rounded-full flex items-center justify-center border border-white">
                                    {{ $cartQty > 99 ? '99+' : $cartQty }}
                                </span>
                            @endif
                        </div>
                        <span class="text-xs font-bold leading-tight mt-0.5">Keranjang</span>
                    </a>

                    <!-- USER MENU 3: History -->
                    @php $isHist = request()->routeIs('user.history'); @endphp
                    <a href="{{ route('user.history') }}" class="flex flex-col items-center justify-center py-1 {{ $isHist ? 'text-[#418b24] bg-[#143728] font-black' : 'text-[#f9dd9c] hover:text-white font-bold' }}">
                        <svg class="w-6 h-6 flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-bold leading-tight mt-0.5">History</span>
                    </a>

                @elseif(Auth::user()->isSeller())
                    <!-- SELLER MENU 1: Toko -->
                    @php $isShop = request()->routeIs('seller.shop'); @endphp
                    <a href="{{ route('seller.shop') }}" class="flex flex-col items-center justify-center py-1 {{ $isShop ? 'text-[#418b24] bg-[#143728] font-black' : 'text-[#f9dd9c] hover:text-white font-bold' }}">
                        <svg class="w-6 h-6 flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-xs font-bold leading-tight mt-0.5">Toko</span>
                    </a>

                    <!-- SELLER MENU 2: Edit -->
                    @php $isEdit = request()->routeIs('seller.products'); @endphp
                    <a href="{{ route('seller.products') }}" class="flex flex-col items-center justify-center py-1 {{ $isEdit ? 'text-[#418b24] bg-[#143728] font-black' : 'text-[#f9dd9c] hover:text-white font-bold' }}">
                        <svg class="w-6 h-6 flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="text-xs font-bold leading-tight mt-0.5">Edit</span>
                    </a>

                    <!-- SELLER MENU 3: History -->
                    @php $isHist = request()->routeIs('seller.history'); @endphp
                    <a href="{{ route('seller.history') }}" class="flex flex-col items-center justify-center py-1 {{ $isHist ? 'text-[#418b24] bg-[#143728] font-black' : 'text-[#f9dd9c] hover:text-white font-bold' }}">
                        <svg class="w-6 h-6 flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-bold leading-tight mt-0.5">History</span>
                    </a>

                @elseif(Auth::user()->isAdmin())
                    <!-- ADMIN MENU 1: Pendapatan Toko -->
                    @php $isRev = request()->routeIs('admin.revenue'); @endphp
                    <a href="{{ route('admin.revenue') }}" class="flex flex-col items-center justify-center py-1 {{ $isRev ? 'text-[#418b24] bg-[#143728] font-black' : 'text-[#f9dd9c] hover:text-white font-bold' }}">
                        <svg class="w-6 h-6 flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-bold leading-tight mt-0.5">Pendapatan</span>
                    </a>

                    <!-- ADMIN MENU 2: Catatan Keuangan -->
                    @php $isLedger = request()->routeIs('admin.ledger'); @endphp
                    <a href="{{ route('admin.ledger') }}" class="flex flex-col items-center justify-center py-1 {{ $isLedger ? 'text-[#418b24] bg-[#143728] font-black' : 'text-[#f9dd9c] hover:text-white font-bold' }}">
                        <svg class="w-6 h-6 flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-xs font-bold leading-tight mt-0.5">Keuangan</span>
                    </a>

                    <!-- ADMIN MENU 3: Admin & Manajemen -->
                    @php $isMgmt = request()->routeIs('admin.management'); @endphp
                    <a href="{{ route('admin.management') }}" class="flex flex-col items-center justify-center py-1 {{ $isMgmt ? 'text-[#418b24] bg-[#143728] font-black' : 'text-[#f9dd9c] hover:text-white font-bold' }}">
                        <svg class="w-6 h-6 flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="text-xs font-bold leading-tight mt-0.5">Admin</span>
                    </a>
                @endif
            </div>
        </nav>
        @endauth

        <!-- Profile & Account Modal (Alpine.js - Ramah Lansia) -->
        @auth
        <div 
            x-cloak 
            x-show="showProfileModal" 
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4 py-6"
            @click.self="showProfileModal = false"
        >
            <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[440px] rounded-xl p-5 text-[#1a4734] max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b-2 border-[#1a4734] mb-4">
                    <h3 class="font-black text-xl text-[#1a4734] flex items-center space-x-2">
                        <svg class="w-6 h-6 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Pengaturan Akun</span>
                    </h3>
                    <button type="button" @click="showProfileModal = false" class="text-3xl font-black text-[#870903] leading-none hover:opacity-80 p-1">&times;</button>
                </div>

                <!-- Ringkasan Akun -->
                <div class="bg-white p-3.5 rounded-lg border-2 border-[#1a4734] mb-4 text-base space-y-1.5">
                    <div class="flex justify-between py-1 border-b border-gray-200">
                        <span class="font-bold text-gray-600">Nama:</span>
                        <span class="font-extrabold text-[#1a4734] break-words">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-200">
                        <span class="font-bold text-gray-600">Email:</span>
                        <span class="font-extrabold text-[#1a4734] break-words">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="font-bold text-gray-600">Role:</span>
                        <span class="font-black text-[#418b24] uppercase text-lg">{{ Auth::user()->role }}</span>
                    </div>
                </div>

                <!-- Formulir Ubah Profil -->
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-bold text-base text-[#1a4734] mb-1">Nama Lengkap</label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name', Auth::user()->name) }}" 
                            required 
                            class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-lg font-bold text-lg text-[#1a4734] focus:outline-none"
                        >
                    </div>
                    <div>
                        <label class="block font-bold text-base text-[#1a4734] mb-1">Alamat Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email', Auth::user()->email) }}" 
                            required 
                            class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-lg font-bold text-lg text-[#1a4734] focus:outline-none"
                        >
                    </div>
                    <div>
                        <label class="block font-bold text-base text-[#1a4734] mb-1">Nomor Telepon (WhatsApp)</label>
                        <input 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', Auth::user()->phone) }}" 
                            placeholder="0812xxxxxxxx"
                            class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-lg font-bold text-lg text-[#1a4734] focus:outline-none"
                        >
                    </div>

                    <div class="pt-3 border-t-2 border-[#1a4734]">
                        <p class="font-black text-lg text-[#870903] mb-2.5">Ganti Kata Sandi (Opsional)</p>
                        <div class="space-y-3">
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-1">Password Saat Ini</label>
                                <input 
                                    type="password" 
                                    name="current_password" 
                                    placeholder="Kosongkan jika tidak diganti" 
                                    class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2 rounded-lg text-base text-[#1a4734] focus:outline-none"
                                >
                            </div>
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-1">Password Baru (min. 6 karakter)</label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    placeholder="Kata sandi baru" 
                                    class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2 rounded-lg text-base text-[#1a4734] focus:outline-none"
                                >
                            </div>
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-1">Ulangi Password Baru</label>
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    placeholder="Konfirmasi kata sandi" 
                                    class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2 rounded-lg text-base text-[#1a4734] focus:outline-none"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 flex space-x-2.5">
                        <button type="submit" class="flex-1 bg-[#418b24] text-white min-h-[50px] py-3 rounded-lg font-black text-lg hover:opacity-90 active:scale-95 transition-all">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="showProfileModal = false" class="bg-gray-300 text-gray-800 px-5 min-h-[50px] py-3 rounded-lg font-bold text-base">
                            Batal
                        </button>
                    </div>
                </form>

                <div class="pt-4 mt-3 border-t-2 border-[#1a4734]">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-[#870903] text-white min-h-[50px] py-3 rounded-lg font-black text-lg hover:opacity-90 flex items-center justify-center space-x-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth

    </div>

</body>
</html>
