<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Registrasi Mitra Supplier</h2>
        <p class="text-gray-500 text-sm">Lengkapi data perusahaan dan akun login.</p>
    </div>

    @if (session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('supplier.register.store') }}">
        @csrf

        <!-- BAGIAN A: DATA PERUSAHAAN -->
        <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-100">
            <h3 class="text-sm font-bold text-blue-800 mb-3 border-b border-blue-200 pb-2">DATA PERUSAHAAN</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block font-medium text-sm text-gray-700">Nama Perusahaan (PT/CV)</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" class="w-full border-gray-300 rounded-md shadow-sm" required autofocus placeholder="PT. Maju Jaya" />
                    @error('company_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                {{-- Input Email Perusahaan DIHAPUS --}}

                <div>
                    <label class="block font-medium text-sm text-gray-700">No. Telepon</label>
                    <input type="text" name="company_phone" value="{{ old('company_phone') }}" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="021-xxxxxx" />
                </div>
                <div>
                    <label class="block font-medium text-sm text-gray-700">Alamat Lengkap</label>
                    <textarea name="company_address" class="w-full border-gray-300 rounded-md shadow-sm" rows="2" required>{{ old('company_address') }}</textarea>
                </div>
            </div>
        </div>

        <!-- BAGIAN B: DATA AKUN LOGIN (PIC) -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 mb-3 border-b border-gray-200 pb-2">AKUN LOGIN (PIC)</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block font-medium text-sm text-gray-700">Nama Penanggung Jawab</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Budi Santoso" />
                </div>
                <div>
                    <label class="block font-medium text-sm text-gray-700">Email Login</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="budi@ptmajujaya.com" />
                    <p class="text-xs text-gray-500 mt-1">*Email ini juga akan digunakan sebagai kontak perusahaan.</p>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Password</label>
                        <input type="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm" required />
                    </div>
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Ulangi Password</label>
                        <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm" required />
                    </div>
                </div>
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 mr-4" href="{{ route('portal.supplier') }}">
                Sudah punya akun?
            </a>
            <button type="submit" class="bg-blue-800 text-white px-4 py-2 rounded-md font-bold hover:bg-blue-900 transition">
                Daftar Sekarang
            </button>
        </div>
    </form>
</x-guest-layout>