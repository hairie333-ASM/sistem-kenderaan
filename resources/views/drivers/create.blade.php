@extends('layouts.app')

@section('title', 'Daftar Pemandu Baharu')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('drivers.index') }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Senarai Pemandu
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            DAFTAR PEMANDU KENDERAAN BAHARU
        </h1>
        <p class="text-xs text-slate-500">
            Mendaftar pemandu rasmi UPF beserta kelayakan lesen dan akaun akses sistem
        </p>
    </div>

    <form method="POST" action="{{ route('drivers.store') }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kod Pemandu <span class="text-rose-500">*</span></label>
                <input type="text" name="driver_code" value="{{ old('driver_code', 'DRV-' . sprintf('%02d', \App\Models\Driver::count() + 1)) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penuh <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Cth: Ahmad bin Zakaria"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Kakitangan</label>
                <input type="text" name="staff_number" value="{{ old('staff_number') }}" placeholder="Cth: ASM-P005"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan</label>
                <input type="text" name="ic_number" value="{{ old('ic_number') }}" placeholder="Cth: 880101-14-5555"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jawatan <span class="text-rose-500">*</span></label>
                <input type="text" name="position" value="{{ old('position', 'Pemandu Kenderaan Gred H11') }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon <span class="text-rose-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="Cth: 012-3456789"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Lesen Memandu <span class="text-rose-500">*</span></label>
                <input type="text" name="license_number" value="{{ old('license_number') }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kelas Lesen <span class="text-rose-500">*</span></label>
                <input type="text" name="license_class" value="{{ old('license_class', 'D, B2') }}" required placeholder="Cth: D, B2, E"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Tamat Lesen <span class="text-rose-500">*</span></label>
                <input type="date" name="license_expiry" value="{{ old('license_expiry') }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Status Ketersediaan <span class="text-rose-500">*</span></label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
                    <option value="Available">Available (Boleh Bertugas)</option>
                    <option value="Assigned">Assigned (Sedang Bertugas)</option>
                    <option value="On Leave">On Leave (Bercuti)</option>
                    <option value="Off Duty">Off Duty (Tamat Bertugas)</option>
                    <option value="Unavailable">Unavailable (Tidak Tersedia)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Waris Kecemasan</label>
                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Cth: Isteri / Ibu"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon Waris</label>
                <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Cth: Pemandu kenderaan utama VIP..." class="w-full px-3 py-2 rounded-xl border border-slate-300">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Optional User Account Creation -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
            <label class="flex items-center space-x-2 cursor-pointer font-bold text-slate-800">
                <input type="checkbox" name="create_user_account" value="1" checked class="rounded border-slate-300 text-asm-600">
                <span>Cipta Akaun Log Masuk Sistem Untuk Pemandu Ini</span>
            </label>
            <div>
                <label class="block text-slate-600 mb-1">Emel Log Masuk Rasmi</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="nama.pemandu@akademisains.gov.my"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                <div class="text-[11px] text-slate-400 mt-1">Kata laluan lalai akaun baharu: <span class="font-mono">password</span></div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('drivers.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black shadow transition">
                Daftar Pemandu
            </button>
        </div>
    </form>
</div>
@endsection
