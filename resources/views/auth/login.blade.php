@extends('layouts.mobile', ['title' => 'Login - Toko Lily'])

@section('content')
<div class="pt-4 pb-6">

    <!-- Store Brand Header -->
    <div class="bg-[#1a4734] text-white p-5 rounded-lg text-center mb-5 border-2 border-[#1a4734]">
        <div class="w-14 h-14 bg-[#f9dd9c] text-[#1a4734] rounded-full mx-auto flex items-center justify-center font-black text-2xl mb-2 border-2 border-white">
            TL
        </div>
        <h1 class="text-xl font-black text-[#f9dd9c] tracking-wide">TOKO LILY</h1>
        <p class="text-xs text-gray-200 mt-1">E-Commerce Terpercaya</p>
    </div>

    <!-- Login Form Card -->
    <div class="bg-white p-5 rounded-lg border-2 border-[#1a4734] mb-4">
        <h2 class="text-base font-extrabold text-[#1a4734] mb-4 pb-2 border-b-2 border-[#1a4734] flex items-center space-x-2">
            <svg class="w-5 h-5 text-[#418b24]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span>Masuk ke Akun Anda</span>
        </h2>

        <form action="{{ route('login') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Alamat Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@email.com" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-bold text-[#1a4734]">Kata Sandi</label>
                    <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-[#e90c00] hover:underline">
                        Lupa Password?
                    </a>
                </div>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div class="flex items-center space-x-2 pt-1">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-[#418b24] border-[#1a4734] rounded">
                <label for="remember" class="text-xs font-semibold text-[#1a4734]">Ingat Saya di Perangkat Ini</label>
            </div>

            <button type="submit" class="w-full bg-[#418b24] text-white py-2.5 rounded font-black text-sm uppercase tracking-wider hover:opacity-90 active:scale-[0.99] transition-transform">
                Masuk Sekarang
            </button>
        </form>

        <div class="mt-4 pt-3 border-t border-gray-200 text-center">
            <p class="text-xs text-[#1a4734]">
                Belum punya akun pelanggan?
                <a href="{{ route('register') }}" class="font-extrabold text-[#418b24] hover:underline ml-1">
                    Daftar di Sini
                </a>
            </p>
        </div>
    </div>



</div>
@endsection
