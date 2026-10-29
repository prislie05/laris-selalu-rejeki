@extends('layouts.mobile', ['title' => 'Login - Toko Lily'])

@section('content')
<div class="my-auto py-6 w-full space-y-4">

    <!-- Store Brand Header -->
    <div class="bg-[#1a4734] text-white p-5 rounded-xl text-center border-2 border-[#1a4734]">
        <div class="w-16 h-16 bg-[#f9dd9c] text-[#1a4734] rounded-full mx-auto flex items-center justify-center font-black text-3xl mb-2.5 border-2 border-white">
            TL
        </div>
        <h1 class="text-2xl font-black text-[#f9dd9c] tracking-wide">TOKO LILY</h1>
        <p class="text-sm font-semibold text-gray-200 mt-0.5">E-Commerce & Kasir POS Terpercaya</p>
    </div>

    <!-- Login Form Card -->
    <div class="bg-white p-6 rounded-xl border-2 border-[#1a4734]">
        <h2 class="text-xl font-extrabold text-[#1a4734] mb-4 pb-3 border-b-2 border-[#1a4734] flex items-center space-x-2">
            <svg class="w-6 h-6 text-[#418b24]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span>Masuk ke Akun Anda</span>
        </h2>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Alamat Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@email.com" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Kata Sandi</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <!-- 1. Ingat Saya di Perangkat Ini -->
            <div class="flex items-center space-x-3 pt-0.5">
                <input type="checkbox" name="remember" id="remember" class="w-6 h-6 text-[#418b24] border-2 border-[#1a4734] rounded">
                <label for="remember" class="text-base font-semibold text-[#1a4734] cursor-pointer">Ingat Saya di Perangkat Ini</label>
            </div>

            <!-- 2. Lupa Password diletakkan di antara Ingat Saya dan Masuk Sekarang dengan jarak seragam -->
            <div class="pt-0.5">
                <a href="{{ route('password.request') }}" class="text-base font-bold text-[#e90c00] hover:underline inline-block">
                    Lupa Password?
                </a>
            </div>

            <!-- 3. Tombol Masuk Sekarang: diperlebar 3px ke bawah & teks center di tengah -->
            <div class="pt-0.5">
                <button 
                    type="submit" 
                    style="min-height: 55px; padding-top: 14px; padding-bottom: 17px;"
                    class="w-full min-h-[55px] pt-3.5 pb-[17px] px-6 bg-[#418b24] text-white rounded-lg font-black text-lg uppercase tracking-wider hover:opacity-95 active:scale-[0.99] transition-transform flex items-center justify-center text-center leading-normal shadow-sm"
                >
                    Masuk Sekarang
                </button>
            </div>
        </form>

        <div class="mt-5 pt-4 border-t-2 border-gray-100 text-center">
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
