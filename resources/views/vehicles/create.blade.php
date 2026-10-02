@extends('layouts.app')

@section('title', 'Tambah Kenderaan Baharu')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('vehicles.index') }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Senarai Kenderaan
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            DAFTAR KENDERAAN JABATAN BAHARU
        </h1>
        <p class="text-xs text-slate-500">
            Menambah kenderaan rasmi ke dalam Vehicle Master Unit Pengurusan Fasiliti
        </p>
    </div>

    <form method="POST" action="{{ route('vehicles.store') }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kod Kenderaan <span class="text-rose-500">*</span></label>
                <input type="text" name="vehicle_code" value="{{ old('vehicle_code', 'VEH-' . sprintf('%02d', \App\Models\Vehicle::count() + 1)) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Pendaftaran (Plat) <span class="text-rose-500">*</span></label>
                <input type="text" name="plate_number" value="{{ old('plate_number') }}" required placeholder="Cth: W 4949 M"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold uppercase">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenama <span class="text-rose-500">*</span></label>
                <input type="text" name="brand" value="{{ old('brand') }}" required placeholder="Cth: Honda / Toyota / Proton"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Model <span class="text-rose-500">*</span></label>
                <input type="text" name="model" value="{{ old('model') }}" required placeholder="Cth: Accord 2.0 / CR-V / Innova"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Kenderaan <span class="text-rose-500">*</span></label>
                <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-300">
                    <option value="Sedan">Sedan</option>
                    <option value="SUV">SUV</option>
                    <option value="MPV">MPV</option>
                    <option value="Van">Van</option>
                    <option value="Motosikal">Motosikal</option>
                    <option value="Bas">Bas</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Status Ketersediaan <span class="text-rose-500">*</span></label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
                    <option value="Available">Available (Boleh Digunakan)</option>
                    <option value="Assigned">Assigned (Telah Ditetapkan)</option>
                    <option value="In Use">In Use (Sedang Digunakan)</option>
                    <option value="Maintenance">Maintenance (Penyelenggaraan)</option>
                    <option value="Out of Service">Out of Service (Tidak Beroperasi)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tahun Dikeluarkan</label>
                <input type="number" name="year" value="{{ old('year', 2023) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Warna</label>
                <input type="text" name="color" value="{{ old('color') }}" placeholder="Cth: Hitam Metalik" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Bacaan Odometer Semasa (KM) <span class="text-rose-500">*</span></label>
                <input type="number" name="current_mileage" value="{{ old('current_mileage', 0) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Bahan Api <span class="text-rose-500">*</span></label>
                <select name="fuel_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300">
                    <option value="RON95">RON95</option>
                    <option value="RON97">RON97</option>
                    <option value="Diesel">Diesel</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Tamat Roadtax</label>
                <input type="date" name="roadtax_expiry" value="{{ old('roadtax_expiry') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Tamat Insurans</label>
                <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Cth: Kegunaan rasmi Pengurusan Tertinggi ASM..." class="w-full px-3 py-2 rounded-xl border border-slate-300">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('vehicles.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black shadow transition">
                Daftar Kenderaan
            </button>
        </div>
    </form>
</div>
@endsection
