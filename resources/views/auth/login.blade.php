<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            {{-- Menggunakan Input field custom yang stylish --}}
            <x-text-input id="email" class="block w-full px-4 py-2 border-slate-300 rounded-lg bg-slate-50 
                            focus:border-cyan-500 focus:ring-cyan-500 transition shadow-sm placeholder-slate-400" 
                            type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                            placeholder="Email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-text-input id="password" class="block w-full px-4 py-2 border-slate-300 rounded-lg bg-slate-50 
                            focus:border-cyan-500 focus:ring-cyan-500 transition shadow-sm placeholder-slate-400"
                            type="password" name="password" required autocomplete="current-password" 
                            placeholder="Password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 mb-6">
            
            <label for="remember_me" class="inline-flex items-center">
                {{-- Style Checkbox ke warna Cyan --}}
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-cyan-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Ingat Saya') }}</span>
            </label>
            
            @if (Route::has('password.request'))
                {{-- Link Lupa Password --}}
                <a class="text-sm font-medium text-cyan-600 hover:text-cyan-800 rounded-md transition" href="{{ route('password.request') }}">
                    {{ __('Lupa Password?') }}
                </a>
            @endif
        </div>

        <div class="flex flex-col items-center justify-center">
            {{-- Tombol Utama dengan Gradient Biru/Cyan --}}
            <button type="submit" 
                    class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent 
                           rounded-xl font-semibold text-white uppercase tracking-wider transition ease-in-out duration-300 
                           shadow-lg shadow-cyan-500/50"
                    style="background: linear-gradient(90deg, #3B82F6 0%, #06B6D4 100%);">
                MASUK SEKARANG
            </button>
            
            {{-- Link Kembali ke Beranda (Opsional) --}}
            <a class="mt-4 text-sm font-medium text-slate-500 hover:text-slate-700 transition" href="{{ url('/') }}">
                &larr; Kembali ke Beranda
            </a>
            
        </div>
    </form>
</x-guest-layout>