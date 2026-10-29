@extends('layouts.mobile', ['title' => 'Buat Password Baru - Toko Lily'])

@section('content')
<div class="my-auto py-6 w-full space-y-4">

    <div class="bg-[#1a4734] text-white p-5 rounded-xl text-center border-2 border-[#1a4734]">
        <h1 class="text-2xl font-black text-[#f9dd9c] tracking-wide">BUAT KATA SANDI BARU</h1>
        <p class="text-sm font-semibold text-gray-200 mt-0.5">Perbarui kata sandi akun Anda</p>
    </div>

    <div class="bg-white p-6 rounded-xl border-2 border-[#1a4734]">
        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Email Akun</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email', $email) }}" 
                    required 
                    readonly
                    class="w-full bg-gray-100 border-2 border-gray-300 px-4 py-3 rounded-lg text-lg font-semibold text-gray-700"
                >
            </div>

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Kata Sandi Baru</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="Minimal 6 karakter" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    placeholder="Ulangi kata sandi baru" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div class="pt-2">
                <button 
                    type="submit" 
                    style="min-height: 55px; padding-top: 14px; padding-bottom: 17px;"
                    class="w-full min-h-[55px] pt-3.5 pb-[17px] px-6 bg-[#418b24] text-white rounded-lg font-black text-lg uppercase tracking-wider hover:opacity-95 active:scale-[0.99] transition-transform flex items-center justify-center text-center leading-normal shadow-sm"
                >
                    Simpan Password & Login
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
