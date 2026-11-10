@extends('layouts.mobile', ['title' => 'Login - Laris Selalu Rejeki', 'pageBg' => '#f9dd9c'])

@section('content')
<div class="my-auto py-6 w-full space-y-5">

    <!-- Store Brand Header -->
    <div class="bg-[#1a4734] text-white p-5 rounded-xl text-center border-2 border-[#1a4734]">
        <div class="w-16 h-16 bg-[#f9dd9c] text-[#1a4734] rounded-full mx-auto flex items-center justify-center font-black text-3xl mb-2.5 border-2 border-white">
            LSR
        </div>
        <h1 class="text-2xl font-black text-[#f9dd9c] tracking-wide">Laris Selalu Rejeki</h1>
        <p class="text-sm font-semibold text-gray-200 mt-0.5">E-Commerce & Kasir POS Terpercaya</p>
    </div>

    <!-- Login Form Card -->
    <div class="bg-white p-6 rounded-xl border-2 border-[#1a4734]">
        <h2 class="text-xl font-extrabold text-[#1a4734] mb-6 pb-3.5 border-b-2 border-[#1a4734] flex items-center space-x-2.5">
            <svg class="w-6 h-6 text-[#418b24]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span>Masuk ke Akun Anda</span>
        </h2>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <!-- 1. Alamat Email -->
            <div class="mb-5">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Alamat Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@email.com" 
                    style="min-height: 54px; border-radius: 8px;"
                    class="w-full min-h-[54px] bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3.5 rounded-[8px] text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <!-- 2. Kata Sandi -->
            <div class="mb-5">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Kata Sandi</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••" 
                    style="min-height: 54px; border-radius: 8px;"
                    class="w-full min-h-[54px] bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3.5 rounded-[8px] text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <!-- 3. Ingat Saya di Perangkat Ini -->
            <div class="flex items-center space-x-3 mb-5">
                <input type="checkbox" name="remember" id="remember" class="w-6 h-6 text-[#418b24] border-2 border-[#1a4734] rounded">
                <label for="remember" class="text-base font-semibold text-[#1a4734] cursor-pointer">Ingat Saya di Perangkat Ini</label>
            </div>

            <!-- 4. Lupa Password diletakkan di antara Ingat Saya dan Masuk Sekarang dengan jarak seimbang -->
            <div class="mb-6">
                <a href="{{ route('password.request') }}" class="text-base font-bold text-[#e90c00] hover:underline inline-block">
                    Lupa Password?
                </a>
            </div>

            <!-- 5. Tombol Masuk Sekarang: diperlebar 3px ke bawah & teks center di tengah -->
            <div class="mb-5">
                <button 
                    type="submit" 
                    style="min-height: 55px; padding-top: 14px; padding-bottom: 17px; border-radius: 8px;"
                    class="w-full min-h-[55px] pt-3.5 pb-[17px] px-6 bg-[#418b24] text-white rounded-[8px] font-black text-lg uppercase tracking-wider hover:opacity-95 active:scale-[0.99] transition-transform flex items-center justify-center text-center leading-normal shadow-sm"
                >
                    Masuk Sekarang
                </button>
            </div>

            <!-- Pembatas ATAU -->
            <div class="relative flex items-center justify-center my-5">
                <div class="border-t-2 border-gray-200 w-full"></div>
                <span class="bg-white px-3 text-xs font-black text-gray-500 uppercase tracking-widest absolute">ATAU</span>
            </div>

            <!-- Tombol Call To Action: Masuk dengan Akun Google (Mendukung User, Seller, dan Admin) -->
            <div class="mb-6">
                <a 
                    href="{{ route('auth.google') }}" 
                    style="min-height: 55px; border-radius: 8px;"
                    class="w-full min-h-[55px] px-5 bg-white hover:bg-gray-50 active:scale-[0.99] border-2 border-[#1a4734] text-[#1a4734] rounded-[8px] font-black text-lg transition-transform flex items-center justify-center text-center space-x-3 shadow-sm"
                >
                    <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Masuk dengan Google</span>
                </a>
                <p class="text-xs text-center text-gray-500 font-semibold mt-2">
                    Mendukung akun Pelanggan, Kasir/Seller, dan Admin
                </p>
            </div>
        </form>

        <div class="mt-6 pt-5 border-t-2 border-gray-100 text-center">
            <p class="text-base text-[#1a4734]">
                Belum punya akun pelanggan?
                <a href="{{ route('register') }}" class="font-extrabold text-[#418b24] hover:underline ml-1">
                    Daftar di Sini
                </a>
            </p>
        </div>
    </div>

</div>
@endsection
