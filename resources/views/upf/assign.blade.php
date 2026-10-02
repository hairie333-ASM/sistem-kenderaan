@extends('layouts.app')

@section('title', 'Penugasan UPF: ' . $vehicleRequest->request_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('requests.show', $vehicleRequest->id) }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Butiran Permohonan
            </a>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-2">
                <span>PENUGASAN PEMANDU & KENDERAAN (UPF)</span>
            </h1>
            <p class="text-xs text-slate-500">
                Menetapkan pemandu bertugas, kenderaan jabatan, dan kelengkapan bagi permohonan {{ $vehicleRequest->request_number }}
            </p>
        </div>
    </div>

    <!-- Conflict Warning Alert (From Session) -->
    @if(session('conflict_error'))
        <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-400 text-rose-950 shadow-md flex items-start space-x-3 animate-pulse">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-2xl mt-0.5 shrink-0"></i>
            <div class="text-xs space-y-1">
                <div class="font-black text-sm uppercase text-rose-900">AMARAN KONFLIK PENUGASAN (CONFLICT DETECTED)</div>
                <p class="font-bold text-rose-800">{{ session('conflict_error')['message'] }}</p>
                <p class="text-slate-600">
                    Sistem mengesan pertindihan slot masa dengan tugasan sedia ada. Sekiranya UPF ingin meneruskan juga (contoh: pemandu boleh kembali tepat pada waktu atau urusan dekat), sila tandakan kotak <strong>"Sahkan Override Konflik Penugasan"</strong> di bawah berserta alasan.
                </p>
            </div>
        </div>
    @endif

    <!-- Request Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <span class="text-xs font-black uppercase text-slate-700">Ringkasan Permohonan: {{ $vehicleRequest->request_number }}</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $vehicleRequest->status_badge['class'] }}">
                {{ $vehicleRequest->status_badge['label'] }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px]">Pemohon:</span>
                <div class="font-bold text-slate-900">{{ $vehicleRequest->applicant_name }}</div>
                <div class="text-slate-500 text-[11px]">{{ $vehicleRequest->applicant_department }}</div>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px]">Tarikh & Waktu Diperlukan:</span>
                <div class="font-bold text-slate-900">{{ $vehicleRequest->start_date->format('d/m/Y') }}</div>
                <div class="text-slate-700 font-medium">
                    {{ \Carbon\Carbon::parse($vehicleRequest->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($vehicleRequest->end_time)->format('h:i A') }}
                </div>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px]">Destinasi:</span>
                <div class="font-bold text-slate-900">{{ $vehicleRequest->destination }}</div>
                <div class="text-[11px] text-slate-500 truncate">Ambil di: {{ $vehicleRequest->origin }}</div>
            </div>
        </div>

        <div class="pt-2 border-t border-slate-100 text-xs">
            <span class="text-slate-400 font-bold uppercase text-[10px]">Tujuan Urusan:</span>
            <div class="font-medium text-slate-800">{{ $vehicleRequest->purpose }}</div>
        </div>
    </div>

    <!-- Assignment Form -->
    <form method="POST" action="{{ route('upf.assign', $vehicleRequest->id) }}" class="space-y-6"
          x-data="{ 
              driverId: '{{ old('assigned_driver_id', $vehicleRequest->assigned_driver_id ?? '') }}', 
              remarks: '{{ addslashes(old('upf_remarks', $vehicleRequest->upf_remarks ?? '')) }}' 
          }">
        @csrf

        @if(!$vehicleRequest->need_driver)
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 flex items-start gap-3">
                <i class="fa-solid fa-car-side text-emerald-600 text-lg mt-0.5 shrink-0"></i>
                <div class="text-xs">
                    <strong class="font-extrabold text-sm block">Pemohon Memohon Pandu Sendiri (Tanpa Pemandu)</strong>
                    <p class="text-emerald-800 mt-0.5">Pemohon telah menyatakan dalam borang permohonan bahawa kenderaan akan dipandu sendiri oleh pemohon. Sila pilih kenderaan dan kelengkapan.</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="border-b border-slate-100 pb-3 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">
                    PENETAPAN PEMANDU, KENDERAAN & KELENGKAPAN
                </h2>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="driverId = ''; remarks = 'Dimaklumkan bahawa atas ketiadaan pemandu bagi tarikh dan masa yang dimohon, pihak UPF meluluskan kenderaan jabatan diberikan kepada pemohon untuk dipandu sendiri.'"
                            class="px-3 py-1 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-extrabold text-[11px] border border-amber-300 transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-user-xmark text-amber-700"></i> Ketiadaan Pemandu (Serah Kereta Kepada Pemohon)
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <!-- Driver Selection -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block font-bold text-slate-800 uppercase tracking-wider">
                            Pilih Pemandu Bertugas
                        </label>
                        <span x-show="driverId === ''" class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                            Pandu Sendiri
                        </span>
                    </div>

                    <select name="assigned_driver_id" x-model="driverId" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600 font-medium">
                        <option value="">-- Ketiadaan Pemandu / Pandu Sendiri (Serah Kenderaan Kepada Pemohon) --</option>
                        @foreach($drivers as $d)
                            <option value="{{ $d->id }}">
                                {{ $d->name }} [{{ $d->is_available_for_trip ? 'Boleh Bertugas' : ($d->conflict_task ? 'KONFLIK: ' . $d->conflict_task->request_number : $d->status) }}]
                            </option>
                        @endforeach
                    </select>

                    <!-- Alert when no driver is selected -->
                    <div x-show="driverId === ''" x-cloak class="p-3 rounded-xl bg-amber-50 border border-amber-300 text-amber-950 space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-xs">
                            <i class="fa-solid fa-circle-info text-amber-600"></i>
                            <span>Maklum Balas: Kenderaan Diberikan Kepada Pemohon</span>
                        </div>
                        <p class="text-[11px] text-amber-900 leading-relaxed">
                            Tiada pemandu UPF ditugaskan. Kenderaan jabatan yang dipilih di bawah akan diserahkan terus kepada pemohon (<strong>{{ $vehicleRequest->applicant_name }}</strong>) untuk dipandu sendiri.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1 text-[11px]">
                        <span class="font-bold text-slate-700 uppercase text-[10px] block">Status Ketersediaan Pemandu:</span>
                        @foreach($drivers as $d)
                            <div class="flex items-center justify-between py-0.5">
                                <span class="text-slate-700">{{ $d->name }}</span>
                                @if($d->is_available_for_trip)
                                    <span class="text-emerald-700 font-bold">✓ Boleh Bertugas</span>
                                @elseif($d->conflict_task)
                                    <span class="text-rose-600 font-bold" title="Tugasan: {{ $d->conflict_task->purpose }}">
                                        ⚠ Bertindih ({{ $d->conflict_task->request_number }})
                                    </span>
                                @else
                                    <span class="text-amber-700 font-bold">{{ $d->status }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Vehicle Selection -->
                <div class="space-y-2">
                    <label class="block font-bold text-slate-800 uppercase tracking-wider">
                        Pilih Kenderaan Jabatan <span class="text-rose-500">*</span>
                    </label>
                    <select name="assigned_vehicle_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                        <option value="">-- Pilih Kenderaan --</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ old('assigned_vehicle_id', $vehicleRequest->assigned_vehicle_id) == $v->id ? 'selected' : '' }}>
                                {{ $v->brand }} {{ $v->model }} ({{ $v->plate_number }}) [{{ $v->is_available_for_trip ? 'Available' : ($v->conflict_task ? 'KONFLIK: ' . $v->conflict_task->request_number : $v->status) }}]
                            </option>
                        @endforeach
                    </select>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1 text-[11px]">
                        <span class="font-bold text-slate-700 uppercase text-[10px] block">Status Ketersediaan Kenderaan:</span>
                        <div class="max-h-36 overflow-y-auto divide-y divide-slate-100 pr-1">
                            @foreach($vehicles as $v)
                                <div class="flex items-center justify-between py-1">
                                    <span class="font-medium text-slate-800">{{ $v->model }} <span class="font-mono text-slate-500">({{ $v->plate_number }})</span></span>
                                    @if($v->is_available_for_trip)
                                        <span class="text-emerald-700 font-bold">✓ Tersedia</span>
                                    @elseif($v->status === 'Maintenance')
                                        <span class="text-purple-700 font-bold">🔧 Penyelenggaraan</span>
                                    @elseif($v->conflict_task)
                                        <span class="text-rose-600 font-bold">⚠ Bertindih ({{ $v->conflict_task->request_number }})</span>
                                    @else
                                        <span class="text-slate-500 font-bold">{{ $v->status }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipment Checklist -->
            <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                <span class="block font-bold text-slate-800 uppercase tracking-wider">
                    Kelengkapan Yang Disediakan Oleh UPF
                </span>
                <div class="grid grid-cols-3 gap-3">
                    <label class="p-3 rounded-xl border border-slate-300 hover:border-asm-500 cursor-pointer transition flex items-center space-x-2">
                        <input type="checkbox" name="assigned_smart_tag" value="1" {{ old('assigned_smart_tag', $vehicleRequest->assigned_smart_tag ?? $vehicleRequest->need_smart_tag) ? 'checked' : '' }} class="rounded border-slate-300 text-asm-600 w-4 h-4">
                        <span class="font-bold text-slate-800">Smart Tag</span>
                    </label>

                    <label class="p-3 rounded-xl border border-slate-300 hover:border-asm-500 cursor-pointer transition flex items-center space-x-2">
                        <input type="checkbox" name="assigned_fuel_card" value="1" {{ old('assigned_fuel_card', $vehicleRequest->assigned_fuel_card ?? $vehicleRequest->need_fuel_card) ? 'checked' : '' }} class="rounded border-slate-300 text-asm-600 w-4 h-4">
                        <span class="font-bold text-slate-800">Kad Inden Petrol</span>
                    </label>

                    <label class="p-3 rounded-xl border border-slate-300 hover:border-asm-500 cursor-pointer transition flex items-center space-x-2">
                        <input type="checkbox" name="assigned_gps" value="1" {{ old('assigned_gps', $vehicleRequest->assigned_gps ?? $vehicleRequest->need_gps) ? 'checked' : '' }} class="rounded border-slate-300 text-asm-600 w-4 h-4">
                        <span class="font-bold text-slate-800">GPS Navigation</span>
                    </label>
                </div>
            </div>

            <!-- Catatan UPF -->
            <div class="text-xs space-y-1">
                <label class="block font-bold text-slate-800 uppercase tracking-wider">Catatan Arahan UPF</label>
                <textarea name="upf_remarks" x-model="remarks" rows="2" placeholder="Cth: Sila ambil pemohon di lobi MATRADE tepat jam 7:15 AM..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600"></textarea>
            </div>

            <!-- OVERRIDE SECTION (Prompt requirements 15 & 35) -->
            <div class="p-4 rounded-xl bg-amber-50/70 border-2 border-amber-300 space-y-3 text-xs">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="override_conflict" id="override_conflict" value="1" class="rounded border-amber-400 text-amber-600 w-4 h-4">
                    <label for="override_conflict" class="font-bold text-amber-950 uppercase cursor-pointer">
                        Sahkan Override Konflik Penugasan (UPF Override Permission)
                    </label>
                </div>
                <div class="text-[11px] text-slate-600">
                    Tandakan pilihan ini sekiranya anda ingin meluluskan penugasan walaupun terdapat amaran pertindihan masa pemandu atau kenderaan.
                </div>
                <div>
                    <input type="text" name="override_conflict_reason" placeholder="Nyatakan justifikasi pelepasan khas / override..."
                        class="w-full px-3 py-2 rounded-xl border border-amber-300 text-xs focus:ring-2 focus:ring-amber-500 bg-white">
                </div>
            </div>

            <!-- Action Submit Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('requests.show', $vehicleRequest->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black text-xs shadow-lg hover:shadow-xl transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan & Sahkan Penugasan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
