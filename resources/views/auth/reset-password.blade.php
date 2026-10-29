@extends('layouts.mobile', ['title' => 'Buat Password Baru - Toko Lily'])

@section('content')
<div class="my-auto py-6 w-full space-y-5">

    <!-- Store Brand Header -->
    <div class="bg-[#1a4734] text-white p-5 rounded-xl text-center border-2 border-[#1a4734]">
        <div class="w-16 h-16 bg-[#f9dd9c] text-[#1a4734] rounded-full mx-auto flex items-center justify-center font-black text-2xl mb-2.5 border-2 border-white">
            <svg class="w-8 h-8 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-black text-[#f9dd9c] tracking-wide">BUAT KATA SANDI BARU</h1>
        <p class="text-sm font-semibold text-gray-200 mt-0.5">Perbarui kata sandi akun Anda</p>
    </div>

    <!-- Reset Password Form Card -->
    <div class="bg-white p-6 rounded-xl border-2 border-[#1a4734]">
        <h2 class="text-xl font-extrabold text-[#1a4734] mb-6 pb-3.5 border-b-2 border-[#1a4734] flex items-center space-x-2.5">
            <svg class="w-6 h-6 text-[#418b24]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Atur Ulang Kata Sandi</span>
        </h2>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <!-- 1. Email Akun (Readonly) -->
            <div class="mb-5">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Email Akun</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email', $email) }}" 
                    required 
                    readonly
                    style="min-height: 54px;"
                    class="w-full min-h-[54px] bg-gray-100 border-2 border-gray-300 px-4 py-3.5 rounded-xl text-lg font-semibold text-gray-700"
                >
            </div>

            <input type="hidden" name="token" value="{{ $token }}">

            <!-- 2. Kata Sandi Baru -->
            <div class="mb-5">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Kata Sandi Baru</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="Minimal 6 karakter" 
                    style="min-height: 54px;"
                    class="w-full min-h-[54px] bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3.5 rounded-xl text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <!-- 3. Konfirmasi Kata Sandi Baru -->
            <div class="mb-6">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Konfirmasi Kata Sandi Baru</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    placeholder="Ulangi kata sandi baru" 
                    style="min-height: 54px;"
                    class="w-full min-h-[54px] bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3.5 rounded-xl text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <!-- 4. Tombol Submit: ukuran & teks center identik dengan Login, Register, dan Forgot Password -->
            <div class="mb-6">
                <button 
                    type="submit" 
                    style="min-height: 55px; padding-top: 14px; padding-bottom: 17px;"
                    class="w-full min-h-[55px] pt-3.5 pb-[17px] px-6 bg-[#418b24] text-white rounded-xl font-black text-lg uppercase tracking-wider hover:opacity-95 active:scale-[0.99] transition-transform flex items-center justify-center text-center leading-normal shadow-sm"
                >
                    Simpan Password & Login
                </button>
            </div>
        </form>

        <div class="mt-6 pt-5 border-t-2 border-gray-100 text-center">
            <a href="{{ route('login') }}" class="text-base font-extrabold text-[#1a4734] hover:underline inline-flex items-center justify-center space-x-1">
                <span>&larr; Kembali ke Halaman Login</span>
            </a>
        </div>
    </div>

</div>
@endsection
