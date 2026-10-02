@extends('layouts.app')

@section('title', 'Pemulangan Kenderaan (Pulang) - ' . $vehicleRequest->request_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="returnCalculation()">
    <div>
        <a href="{{ route('requests.show', $vehicleRequest->id) }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Permohonan
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            REKOD PEMULANGAN KENDERAAN (PULANG)
        </h1>
        <p class="text-xs text-slate-500">
            Merekod bacaan meter akhir, pengiraan automatik jumlah kilometer, dan semakan pemulangan kelengkapan
        </p>
    </div>

    <!-- Summary Box -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-xs grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Permohonan</span>
            <div class="font-bold text-slate-900">{{ $vehicleRequest->request_number }}</div>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Kenderaan</span>
            <div class="font-bold text-slate-900">{{ $vehicleRequest->vehicle?->plate_number }}</div>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Pemandu</span>
            <div class="font-bold text-slate-900">{{ $vehicleRequest->driver?->name ?? 'Pandu Sendiri' }}</div>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Meter Semasa Ambil</span>
            <div class="font-mono font-bold text-slate-900">{{ number_format($vehicleRequest->handover?->start_mileage ?? 0) }} KM</div>
        </div>
    </div>

    <form method="POST" action="{{ route('handovers.return.store', $vehicleRequest->id) }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        @csrf

        <!-- Tarikh, Masa & Pemulang -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Pemulangan <span class="text-rose-500">*</span></label>
                <input type="date" name="return_date" value="{{ old('return_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Masa Pemulangan <span class="text-rose-500">*</span></label>
                <input type="time" name="return_time" value="{{ old('return_time', \Carbon\Carbon::now()->format('H:i')) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Yang Memulangkan <span class="text-rose-500">*</span></label>
                <input type="text" name="returned_by_name" value="{{ old('returned_by_name', $vehicleRequest->driver?->name ?: $vehicleRequest->applicant_name) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
            </div>
        </div>

        <!-- Meter Akhir & Auto Calculate Total KM (Section 17 requirement) -->
        <div class="p-5 rounded-2xl bg-emerald-50/70 border-2 border-emerald-300 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 uppercase text-[10px] mb-1">Meter Awal (KM)</label>
                    <div class="font-mono font-black text-lg text-slate-700 p-2.5 bg-white rounded-xl border border-emerald-200">
                        {{ number_format($vehicleRequest->handover?->start_mileage ?? 0) }}
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase text-[10px] mb-1">
                        Meter Akhir (Odometer) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="return_mileage" x-model.number="returnMileage" @input="calculateKm()" required
                        class="w-full p-2.5 rounded-xl border border-emerald-400 font-mono font-black text-lg text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-black text-emerald-950 uppercase text-[10px] mb-1">
                        JUMLAH PERJALANAN (AUTO CALCULATE)
                    </label>
                    <div class="font-mono font-black text-xl text-emerald-800 p-2 bg-emerald-100/80 rounded-xl border border-emerald-300 flex items-center justify-between">
                        <span x-text="totalKm.toLocaleString()"></span>
                        <span class="text-xs uppercase font-sans">KM</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block font-bold text-slate-700 uppercase text-[10px] mb-1">
                        Aras Bahan Api Semasa Pulang <span class="text-rose-500">*</span>
                    </label>
                    <select name="fuel_level" required class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white font-bold">
                        <option value="Full (Penuh)">Full (Penuh 100%)</option>
                        <option value="3/4 (Tiga Suku)" selected>3/4 (Tiga Suku 75%)</option>
                        <option value="1/2 (Separuh)">1/2 (Separuh 50%)</option>
                        <option value="1/4 (Suku)">1/4 (Suku 25%)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Checklist Pemulangan Kelengkapan -->
        <div class="space-y-3">
            <label class="block font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                Semakan Pemulangan Kelengkapan
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="return_keys" value="1" checked class="rounded border-slate-300 text-emerald-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Kunci Dipulangkan</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="return_smart_tag" value="1" checked class="rounded border-slate-300 text-emerald-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Smart Tag Dipulangkan</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="return_fuel_card" value="1" checked class="rounded border-slate-300 text-emerald-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Kad Inden Dipulangkan</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="return_gps" value="1" checked class="rounded border-slate-300 text-emerald-600 w-4 h-4">
                    <span class="font-bold text-slate-800">GPS Dipulangkan</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Keadaan Kenderaan Semasa Pemulangan</label>
            <textarea name="condition_notes" rows="2" placeholder="Cth: Dipulangkan dalam keadaan baik dan tiada calar baharu..."
                class="w-full px-3 py-2 rounded-xl border border-slate-300"></textarea>
        </div>

        <!-- Issue / Damage Prompt Checkbox -->
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center space-x-3">
            <input type="checkbox" name="has_damage_incident" id="has_damage" value="1" class="rounded border-rose-300 text-rose-600 w-5 h-5">
            <label for="has_damage" class="font-bold text-rose-900 cursor-pointer">
                Terdapat Kerosakan / Isu Mekanikal / Kemalangan (Sistem akan membuka borang Lapor Isu selepas ini)
            </label>
        </div>

        @if(auth()->user()->isUpf() || auth()->user()->isAdmin())
            <!-- PENGESAHAN PEMERIKSAAN FIZIKAL OLEH UPF -->
            <div class="p-5 rounded-2xl bg-indigo-50/70 border-2 border-indigo-300 space-y-3">
                <div class="flex items-center justify-between border-b border-indigo-200 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs font-black">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </span>
                        <span class="font-black text-xs uppercase text-indigo-950">PENGESAHAN PEMERIKSAAN FIZIKAL KENDERAAN (UPF)</span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-200 text-indigo-900">Pegawai UPF: {{ auth()->user()->name }}</span>
                </div>

                <div class="space-y-2">
                    <label class="block font-bold text-slate-800 text-[11px] uppercase">
                        Keadaan Fizikal Kenderaan Semasa Diterima <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <label class="p-3 rounded-xl border-2 border-emerald-400 bg-white hover:bg-emerald-50 cursor-pointer flex items-center space-x-2">
                            <input type="radio" name="upf_condition_status" value="Baik & Sempurna" checked class="text-emerald-600">
                            <div>
                                <div class="font-bold text-slate-900 text-xs">Baik & Sempurna</div>
                                <div class="text-[10px] text-slate-500">Tiada kerosakan, bersih</div>
                            </div>
                        </label>
                        <label class="p-3 rounded-xl border border-slate-300 bg-white hover:bg-amber-50 cursor-pointer flex items-center space-x-2">
                            <input type="radio" name="upf_condition_status" value="Memuaskan" class="text-amber-600">
                            <div>
                                <div class="font-bold text-slate-900 text-xs">Memuaskan</div>
                                <div class="text-[10px] text-slate-500">Kotoran biasa / kehausan ringan</div>
                            </div>
                        </label>
                        <label class="p-3 rounded-xl border border-slate-300 bg-white hover:bg-rose-50 cursor-pointer flex items-center space-x-2">
                            <input type="radio" name="upf_condition_status" value="Ada Kerosakan" class="text-rose-600">
                            <div>
                                <div class="font-bold text-slate-900 text-xs">Ada Kerosakan</div>
                                <div class="text-[10px] text-slate-500">Perlu tindakan/servis UPF</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-800 text-[11px] uppercase mb-1">Catatan Pemeriksaan Fizikal Pegawai UPF</label>
                    <textarea name="upf_verification_notes" rows="2" placeholder="Cth: Pemeriksaan fizikal kenderaan selesai. Kenderaan diterima dalam keadaan bersih dan baik."
                        class="w-full px-3 py-2 rounded-xl border border-indigo-200 bg-white"></textarea>
                </div>
            </div>
        @else
            <!-- MAKLUMAN PENGESAHAN FIZIKAL UPF (Untuk Pemohon / Pemandu) -->
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs space-y-1">
                <div class="font-black text-amber-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600 text-sm"></i>
                    PROSES PENGESAHAN PEMERIKSAAN FIZIKAL OLEH UPF
                </div>
                <p class="text-amber-800 leading-relaxed">
                    Setelah anda menghantar rekod pemulangan dan meter akhir ini, pihak Unit Pengurusan Fasiliti (UPF) akan melakukan pemeriksaan fizikal ke atas kenderaan bagi mengesahkan kenderaan telah diterima kembali dalam keadaan baik dan sempurna.
                </p>
            </div>
        @endif

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('requests.show', $vehicleRequest->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-black shadow transition">
                Sahkan Pemulangan Kenderaan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function returnCalculation() {
        const start = {{ $vehicleRequest->handover?->start_mileage ?? 0 }};
        return {
            startMileage: start,
            returnMileage: start + 75,
            totalKm: 75,
            calculateKm() {
                this.totalKm = Math.max(0, this.returnMileage - this.startMileage);
            }
        }
    }
</script>
@endpush
@endsection
