@extends('layouts.app')

@section('title', 'Rekod Isian Minyak & Resit')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="fuelCalculation()">
    <div>
        <a href="{{ route('fuel.index') }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Log Minyak
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            REKOD ISIAN MINYAK (KAD INDEN / RESIT)
        </h1>
        <p class="text-xs text-slate-500">
            Sila masukkan butiran transaksi pembelian bahan api dan muat naik imej resit untuk semakan UPF
        </p>
    </div>

    <form method="POST" action="{{ route('fuel.store') }}" enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        @csrf

        @if($selectedRequest)
            <input type="hidden" name="request_id" value="{{ $selectedRequest->id }}">
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs flex items-center justify-between">
                <div>
                    <span class="font-bold text-amber-900">Berkaitan Tugasan:</span>
                    <strong class="text-slate-900 ml-1">{{ $selectedRequest->request_number }}</strong>
                    <div class="text-[11px] text-slate-600">{{ $selectedRequest->purpose }} ({{ $selectedRequest->destination }})</div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-900">Tugasan Aktif</span>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Kenderaan -->
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Kenderaan <span class="text-rose-500">*</span></label>
                <select name="vehicle_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300">
                    <option value="">-- Pilih Kenderaan --</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ (old('vehicle_id', $selectedRequest?->assigned_vehicle_id) == $v->id) ? 'selected' : '' }}>
                            {{ $v->plate_number }} ({{ $v->brand }} {{ $v->model }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pemandu -->
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Pemandu Bertugas</label>
                <select name="driver_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300">
                    <option value="">-- Pilih Pemandu --</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ (old('driver_id', $selectedRequest?->assigned_driver_id) == $d->id) ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tarikh & Waktu -->
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Isian <span class="text-rose-500">*</span></label>
                <input type="date" name="log_date" value="{{ old('log_date', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Waktu Isian <span class="text-rose-500">*</span></label>
                <input type="time" name="log_time" value="{{ old('log_time', date('H:i')) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <!-- Stesen & Kaedah -->
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Stesen Minyak <span class="text-rose-500">*</span></label>
                <input type="text" name="station_name" value="{{ old('station_name', 'Petronas Presint 9 Putrajaya') }}" required
                    placeholder="Cth: Petronas MATRADE / Shell Jalan Duta" class="w-full px-3 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kaedah Pembayaran <span class="text-rose-500">*</span></label>
                <select name="payment_method" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-bold">
                    <option value="Kad Inden" selected>Kad Inden Petrol (UPF ASM)</option>
                    <option value="Tunai">Tunai (Tuntutan Balik)</option>
                    <option value="Touch 'n Go">Touch 'n Go eWallet</option>
                    <option value="Kad Kredit">Kad Korporat</option>
                </select>
            </div>
        </div>

        <!-- Liter, Harga, Jumlah (Auto Calculated) -->
        <div class="p-5 rounded-2xl bg-amber-50/70 border-2 border-amber-300 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 uppercase text-[10px] mb-1">Jenis Minyak</label>
                    <select name="fuel_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white">
                        <option value="RON95" selected>RON95</option>
                        <option value="RON97">RON97</option>
                        <option value="Diesel">Diesel</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase text-[10px] mb-1">Liter Bahan Api <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="liters" x-model.number="liters" @input="calculateTotal()" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white font-mono font-bold text-sm">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase text-[10px] mb-1">Harga Seliter (RM)</label>
                    <input type="number" step="0.01" name="price_per_liter" x-model.number="pricePerLiter" @input="calculateTotal()" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white font-mono text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-amber-200">
                <span class="font-black text-amber-950 uppercase text-xs">JUMLAH BAYARAN (RM):</span>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-slate-500">RM</span>
                    <input type="number" step="0.01" name="total_amount" x-model.number="totalAmount" required
                        class="w-36 px-3 py-2 rounded-xl border border-amber-400 bg-white font-mono font-black text-lg text-slate-900 text-right">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Bacaan Odometer Semasa Isian (KM)</label>
                <input type="number" name="mileage_at_fill" value="{{ old('mileage_at_fill') }}" placeholder="Cth: 65120"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Resit / Invois Stesen</label>
                <input type="text" name="receipt_number" value="{{ old('receipt_number') }}" placeholder="Cth: PET-20261001-098"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">
                    Muat Naik Gambar / Dokumen Resit Rasmi
                </label>
                <input type="file" name="receipt_photo" accept="image/*,.pdf"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                <div class="text-[11px] text-slate-400 mt-1">Format imej PNG, JPG atau PDF (Maksimum 5MB)</div>
            </div>

            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                <input type="text" name="remarks" placeholder="Cth: Isian penuh tangki sebelum perjalanan ke Putrajaya"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('fuel.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black shadow transition">
                Simpan Log Minyak
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function fuelCalculation() {
        return {
            liters: 35.00,
            pricePerLiter: 2.05,
            totalAmount: 71.75,
            calculateTotal() {
                this.totalAmount = parseFloat((this.liters * this.pricePerLiter).toFixed(2));
            }
        }
    }
</script>
@endpush
@endsection
