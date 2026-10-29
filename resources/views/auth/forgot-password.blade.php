@extends('layouts.mobile', ['title' => 'Lupa Password - Toko Lily'])

@section('content')
<div class="pt-4 pb-8 space-y-5">

    <div class="bg-[#1a4734] text-white p-6 rounded-xl text-center border-2 border-[#1a4734]">
        <div class="w-16 h-16 bg-[#f9dd9c] text-[#1a4734] rounded-full mx-auto flex items-center justify-center font-black text-2xl mb-3 border-2 border-white">
            <svg class="w-8 h-8 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-black text-[#f9dd9c] tracking-wide">RESET PASSWORD</h1>
        <p class="text-sm font-semibold text-gray-200 mt-1">Tersedia untuk Pengguna, Seller, dan Admin</p>
    </div>

    <div class="bg-white p-6 rounded-xl border-2 border-[#1a4734]">
        <h2 class="text-xl font-extrabold text-[#1a4734] mb-3 pb-3 border-b-2 border-[#1a4734]">
            Permintaan Reset Kata Sandi
        </h2>
        <p class="text-base text-gray-700 mb-5 leading-relaxed">
            Masukkan alamat email yang terdaftar. Sistem akan memverifikasi email Anda untuk melanjutkan perubahan kata sandi.
        </p>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Email Terdaftar</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@tokolily.com" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full min-h-[52px] bg-[#e90c00] text-white py-3.5 px-4 rounded-lg font-black text-lg uppercase tracking-wider hover:opacity-95 active:scale-[0.99] transition-transform">
                    Lanjutkan Reset Password
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t-2 border-gray-100 text-center">
            <a href="{{ route('login') }}" class="text-base font-extrabold text-[#1a4734] hover:underline inline-flex items-center justify-center space-x-1">
                <span>&larr; Kembali ke Halaman Login</span>
            </a>
        </div>
    </div>

</div>
@endsection
