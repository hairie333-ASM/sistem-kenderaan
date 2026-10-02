@extends('layouts.app')

@section('title', 'Kemaskini Kenderaan: ' . $vehicle->plate_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('vehicles.show', $vehicle->id) }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Profil Kenderaan
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            KEMASKINI KENDERAAN: {{ $vehicle->plate_number }}
        </h1>
        <p class="text-xs text-slate-500">
            Mengemaskini spesifikasi, bacaan meter, dan status kenderaan jabatan UPF
        </p>
    </div>

    <form method="POST" action="{{ route('vehicles.update', $vehicle->id) }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kod Kenderaan <span class="text-rose-500">*</span></label>
                <input type="text" name="vehicle_code" value="{{ old('vehicle_code', $vehicle->vehicle_code) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Pendaftaran (Plat) <span class="text-rose-500">*</span></label>
                <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold uppercase">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenama <span class="text-rose-500">*</span></label>
                <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Model <span class="text-rose-500">*</span></label>
                <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Kenderaan <span class="text-rose-500">*</span></label>
                <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-300">
                    <option value="Sedan" {{ $vehicle->type == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                    <option value="SUV" {{ $vehicle->type == 'SUV' ? 'selected' : '' }}>SUV</option>
                    <option value="MPV" {{ $vehicle->type == 'MPV' ? 'selected' : '' }}>MPV</option>
                    <option value="Van" {{ $vehicle->type == 'Van' ? 'selected' : '' }}>Van</option>
                    <option value="Motosikal" {{ $vehicle->type == 'Motosikal' ? 'selected' : '' }}>Motosikal</option>
                    <option value="Bas" {{ $vehicle->type == 'Bas' ? 'selected' : '' }}>Bas</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Status Ketersediaan <span class="text-rose-500">*</span></label>
                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
                    <option value="Available" {{ $vehicle->status == 'Available' ? 'selected' : '' }}>Available (Boleh Digunakan)</option>
                    <option value="Assigned" {{ $vehicle->status == 'Assigned' ? 'selected' : '' }}>Assigned (Telah Ditetapkan)</option>
                    <option value="In Use" {{ $vehicle->status == 'In Use' ? 'selected' : '' }}>In Use (Sedang Digunakan)</option>
                    <option value="Maintenance" {{ $vehicle->status == 'Maintenance' ? 'selected' : '' }}>Maintenance (Penyelenggaraan)</option>
                    <option value="Out of Service" {{ $vehicle->status == 'Out of Service' ? 'selected' : '' }}>Out of Service (Tidak Beroperasi)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tahun Dikeluarkan</label>
                <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Warna</label>
                <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Bacaan Odometer Semasa (KM) <span class="text-rose-500">*</span></label>
                <input type="number" name="current_mileage" value="{{ old('current_mileage', $vehicle->current_mileage) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Bahan Api <span class="text-rose-500">*</span></label>
                <select name="fuel_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300">
                    <option value="RON95" {{ $vehicle->fuel_type == 'RON95' ? 'selected' : '' }}>RON95</option>
                    <option value="RON97" {{ $vehicle->fuel_type == 'RON97' ? 'selected' : '' }}>RON97</option>
                    <option value="Diesel" {{ $vehicle->fuel_type == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Tamat Roadtax</label>
                <input type="date" name="roadtax_expiry" value="{{ old('roadtax_expiry', $vehicle->roadtax_expiry ? $vehicle->roadtax_expiry->format('Y-m-d') : '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Tamat Insurans</label>
                <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry', $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('Y-m-d') : '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300">{{ old('notes', $vehicle->notes) }}</textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <button type="button" onclick="if(confirm('Adakah anda pasti ingin memadam kenderaan ini?')) document.getElementById('delete-vehicle-form').submit();" class="text-rose-600 font-bold hover:underline">
                <i class="fa-solid fa-trash mr-1"></i> Padam Kenderaan
            </button>
            <div class="flex items-center space-x-3">
                <a href="{{ route('vehicles.show', $vehicle->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black shadow transition">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <form id="delete-vehicle-form" method="POST" action="{{ route('vehicles.destroy', $vehicle->id) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
