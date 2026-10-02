@extends('layouts.app')

@section('title', 'Lapor Isu / Kerosakan Kenderaan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('incidents.index') }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Senarai Laporan
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            BORANG LAPORAN KEROSAKAN / KEMALANGAN
        </h1>
        <p class="text-xs text-slate-500">
            Laporkan sebarang kerosakan fizikal, mekanikal, tayar, enjin, atau kemalangan jalan raya kepada UPF
        </p>
    </div>

    <form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        @csrf

        @if($selectedRequest)
            <input type="hidden" name="request_id" value="{{ $selectedRequest->id }}">
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs flex items-center justify-between">
                <div>
                    <span class="font-bold text-rose-900">Berkaitan Tugasan:</span>
                    <strong class="text-slate-900 ml-1">{{ $selectedRequest->request_number }}</strong>
                    <div class="text-[11px] text-slate-600">{{ $selectedRequest->purpose }} ({{ $selectedRequest->destination }})</div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-200 text-rose-900">Tugasan Semasa</span>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Kenderaan -->
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kenderaan Terlibat <span class="text-rose-500">*</span></label>
                <select name="vehicle_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300">
                    <option value="">-- Pilih Kenderaan --</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ (old('vehicle_id', $selectedVehicleId) == $v->id) ? 'selected' : '' }}>
                            {{ $v->plate_number }} ({{ $v->brand }} {{ $v->model }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pemandu -->
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Pemandu Terlibat</label>
                <select name="driver_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300">
                    <option value="">-- Pilih Pemandu --</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ (old('driver_id', $selectedDriverId ?? $selectedRequest?->assigned_driver_id) == $d->id) ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jenis Isu -->
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Laporan <span class="text-rose-500">*</span></label>
                <select name="incident_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-bold">
                    <option value="Kerosakan">Kerosakan Mekanikal Am</option>
                    <option value="Kemalangan">Kemalangan Jalan Raya</option>
                    <option value="Tayar">Isu Tayar (Pancit / Haus)</option>
                    <option value="Enjin">Masalah Enjin / Suhu Panas</option>
                    <option value="Aircond">Penghawa Dingin Tidak Sejuk</option>
                    <option value="Lampu">Lampu / Elektrikal</option>
                    <option value="Body">Kerosakan Body / Calar / Kemek</option>
                    <option value="Lain-lain">Lain-lain Isu</option>
                </select>
            </div>

            <!-- Keterukan -->
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tahap Keterukan (Severity) <span class="text-rose-500">*</span></label>
                <select name="severity" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-bold">
                    <option value="Rendah">Rendah (Masih boleh dipandu selamat)</option>
                    <option value="Sederhana" selected>Sederhana (Perlu perhatian segera)</option>
                    <option value="Tinggi">Tinggi (Risiko keselamatan pemanduan)</option>
                    <option value="Kritikal">Kritikal (Kenderaan tersadai / kemalangan teruk)</option>
                </select>
            </div>

            <!-- Tarikh & Waktu -->
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Kejadian <span class="text-rose-500">*</span></label>
                <input type="date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Waktu Kejadian <span class="text-rose-500">*</span></label>
                <input type="time" name="incident_time" value="{{ old('incident_time', date('H:i')) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <!-- Lokasi -->
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">Lokasi Kejadian <span class="text-rose-500">*</span></label>
                <input type="text" name="location" value="{{ old('location') }}" required placeholder="Cth: Lebuhraya PLUS KM 302 arah Selatan / Tempat Letak Kereta MATRADE"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300">
            </div>

            <!-- Deskripsi -->
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">Penerangan Kerosakan / Kronologi Kemalangan <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="4" required placeholder="Huraikan apa yang berlaku, simptom kerosakan, atau kesan kemalangan..."
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300">{{ old('description') }}</textarea>
            </div>

            <!-- Gambar / Dokumen -->
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">Muat Naik Gambar Kerosakan / Laporan Polis</label>
                <input type="file" name="photo" accept="image/*,.pdf"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                <div class="text-[11px] text-slate-400 mt-1">Sertakan foto kerosakan jelas bagi memudahkan kelulusan pembaikan UPF</div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('incidents.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black shadow transition">
                Hantar Laporan Isu ke UPF
            </button>
        </div>
    </form>
</div>
@endsection
