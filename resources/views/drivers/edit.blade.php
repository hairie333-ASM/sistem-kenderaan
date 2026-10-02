@extends('layouts.app')

@section('title', 'Kemaskini Pemandu: ' . $driver->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('drivers.show', $driver->id) }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Profil Pemandu
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            KEMASKINI PEMANDU: {{ $driver->name }}
        </h1>
        <p class="text-xs text-slate-500">
            Kemaskini maklumat peribadi, lesen memandu, dan status ketersediaan
        </p>
    </div>

    <form method="POST" action="{{ route('drivers.update', $driver->id) }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kod Pemandu <span class="text-rose-500">*</span></label>
                <input type="text" name="driver_code" value="{{ old('driver_code', $driver->driver_code) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penuh <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $driver->name) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Kakitangan</label>
                <input type="text" name="staff_number" value="{{ old('staff_number', $driver->staff_number) }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan</label>
                <input type="text" name="ic_number" value="{{ old('ic_number', $driver->ic_number) }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jawatan <span class="text-rose-500">*</span></label>
                <input type="text" name="position" value="{{ old('position', $driver->position) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon <span class="text-rose-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone', $driver->phone) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Lesen Memandu <span class="text-rose-500">*</span></label>
                <input type="text" name="license_number" value="{{ old('license_number', $driver->license_number) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kelas Lesen <span class="text-rose-500">*</span></label>
                <input type="text" name="license_class" value="{{ old('license_class', $driver->license_class) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Tamat Lesen <span class="text-rose-500">*</span></label>
                <input type="date" name="license_expiry" value="{{ old('license_expiry', $driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : '') }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Status Ketersediaan <span class="text-rose-500">*</span></label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
                    <option value="Available" {{ $driver->status == 'Available' ? 'selected' : '' }}>Available (Boleh Bertugas)</option>
                    <option value="Assigned" {{ $driver->status == 'Assigned' ? 'selected' : '' }}>Assigned (Sedang Bertugas)</option>
                    <option value="On Leave" {{ $driver->status == 'On Leave' ? 'selected' : '' }}>On Leave (Bercuti)</option>
                    <option value="Off Duty" {{ $driver->status == 'Off Duty' ? 'selected' : '' }}>Off Duty (Tamat Bertugas)</option>
                    <option value="Unavailable" {{ $driver->status == 'Unavailable' ? 'selected' : '' }}>Unavailable (Tidak Tersedia)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Waris Kecemasan</label>
                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $driver->emergency_contact_name) }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon Waris</label>
                <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $driver->emergency_contact_phone) }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300">{{ old('notes', $driver->notes) }}</textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <button type="button" onclick="if(confirm('Adakah anda pasti ingin memadam rekod pemandu ini?')) document.getElementById('delete-driver-form').submit();" class="text-rose-600 font-bold hover:underline">
                <i class="fa-solid fa-trash mr-1"></i> Padam Pemandu
            </button>
            <div class="flex items-center space-x-3">
                <a href="{{ route('drivers.show', $driver->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black shadow transition">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <form id="delete-driver-form" method="POST" action="{{ route('drivers.destroy', $driver->id) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
