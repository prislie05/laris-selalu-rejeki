@extends('layouts.mobile', ['title' => 'Lupa Password - Toko Lily'])

@section('content')
<div class="pt-4 pb-6">

    <div class="bg-[#1a4734] text-white p-5 rounded-lg text-center mb-5 border-2 border-[#1a4734]">
        <div class="w-14 h-14 bg-[#f9dd9c] text-[#1a4734] rounded-full mx-auto flex items-center justify-center font-black text-2xl mb-2 border-2 border-white">
            <svg class="w-8 h-8 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h1 class="text-xl font-black text-[#f9dd9c] tracking-wide">RESET PASSWORD</h1>
        <p class="text-xs text-gray-200 mt-1">Tersedia untuk Pengguna, Seller, dan Admin</p>
    </div>

    <div class="bg-white p-5 rounded-lg border-2 border-[#1a4734]">
        <h2 class="text-base font-extrabold text-[#1a4734] mb-3 pb-2 border-b-2 border-[#1a4734]">
            Permintaan Reset Kata Sandi
        </h2>
        <p class="text-xs text-gray-600 mb-4 leading-relaxed">
            Masukkan alamat email yang terdaftar. Sistem akan memverifikasi email Anda untuk melanjutkan perubahan kata sandi.
        </p>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Email Terdaftar</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@tokolily.com" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <button type="submit" class="w-full bg-[#e90c00] text-white py-2.5 rounded font-black text-sm uppercase tracking-wider hover:opacity-90 active:scale-[0.99] transition-transform">
                Lanjutkan Reset Password
            </button>
        </form>

        <div class="mt-4 pt-3 border-t border-gray-200 text-center">
            <a href="{{ route('login') }}" class="text-xs font-extrabold text-[#1a4734] hover:underline flex items-center justify-center space-x-1">
                <span>&larr; Kembali ke Halaman Login</span>
            </a>
        </div>
    </div>

</div>
@endsection
