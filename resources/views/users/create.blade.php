@extends('layouts.app')

@section('title', 'Daftar Pengguna Baharu')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-900 text-white uppercase tracking-wider mb-2">
                <i class="fa-solid fa-user-plus mr-1.5 text-amber-400"></i> Akaun Baharu
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Daftar Pengguna Sistem
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Cipta akaun untuk staf pemohon, pegawai UPF, pemandu, atau pentadbir.
            </p>
        </div>
        <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    <!-- Create Form -->
    <form action="{{ route('users.store') }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Name -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                    Nama Penuh <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Mohd Azim bin Ahmad"
                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-semibold bg-slate-50 focus:bg-white transition">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                    Alamat Emel <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="nama@akademisains.gov.my"
                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-mono bg-slate-50 focus:bg-white transition">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                    Kata Laluan <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password" required minlength="6"
                       placeholder="Minimum 6 aksara"
                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm bg-slate-50 focus:bg-white transition">
            </div>

            <!-- Role Selection -->
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                    Peranan (Role) <span class="text-rose-500">*</span>
                </label>
                <select name="role" required
                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-bold bg-slate-50 focus:bg-white transition">
                    <option value="pemohon" {{ old('role') === 'pemohon' ? 'selected' : '' }}>Pemohon (Staff / Pegawai)</option>
                    <option value="pemandu" {{ old('role') === 'pemandu' ? 'selected' : '' }}>Pemandu Bertugas</option>
                    <option value="upf" {{ old('role') === 'upf' ? 'selected' : '' }}>Pegawai UPF</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Pentadbir Sistem (Admin)</option>
                </select>
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                    No. Telefon Bimbit
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       placeholder="012-3456789"
                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-mono bg-slate-50 focus:bg-white transition">
            </div>

            <!-- Department -->
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                    Bahagian / Unit
                </label>
                <input type="text" name="department" value="{{ old('department', 'Akademi Sains Malaysia') }}"
                       placeholder="Contoh: Unit IT / UPF / STI"
                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-medium bg-slate-50 focus:bg-white transition">
            </div>

            <!-- Position -->
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                    Jawatan Rasmi
                </label>
                <input type="text" name="position" value="{{ old('position') }}"
                       placeholder="Contoh: Pegawai Eksekutif / Pemandu"
                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-medium bg-slate-50 focus:bg-white transition">
            </div>

            <!-- Active Status Toggle -->
            <div class="sm:col-span-2 pt-2">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-black text-slate-900">Akaun Aktif</div>
                        <div class="text-[11px] text-slate-500">Akaun yang aktif boleh log masuk dan mengakses modul mengikut peranan.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-3">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('users.index') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-7 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs transition shadow-lg shadow-slate-900/15 flex items-center">
                <i class="fa-solid fa-check mr-2"></i> Simpan Pengguna
            </button>
        </div>
    </form>
</div>
@endsection
