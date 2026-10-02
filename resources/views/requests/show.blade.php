@extends('layouts.app')

@section('title', 'Butiran Permohonan: ' . $vehicleRequest->request_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ cancelModal: false, verifyReturnModal: false }">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('requests.index') }}" class="hover:underline font-bold text-asm-600">Permohonan</a>
                <span>/</span>
                <span class="font-mono">{{ $vehicleRequest->request_number }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>{{ $vehicleRequest->request_number }}</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black border {{ $vehicleRequest->status_badge['class'] }}">
                    {{ strtoupper($vehicleRequest->status_badge['label']) }}
                </span>
                @if($vehicleRequest->is_short_notice)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Notis Singkat
                    </span>
                @endif
            </h1>
        </div>

        <!-- Top Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('requests.print', $vehicleRequest->id) }}" target="_blank" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak Borang A4
            </a>

            @if(auth()->user()->isUpf() && in_array($vehicleRequest->status, ['submitted', 'under_review', 'approved']))
                <a href="{{ route('upf.assign.show', $vehicleRequest->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-black text-xs shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-user-plus"></i> Tetapkan Pemandu & Kereta
                </a>
            @endif

            @if(!in_array($vehicleRequest->status, ['completed', 'cancelled', 'rejected']))
                <button type="button" @click="cancelModal = true" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs transition flex items-center gap-1">
                    <i class="fa-solid fa-ban"></i> Batal Permohonan
                </button>
            @endif
        </div>
    </div>

    <!-- Conflict Alert Banner if detected -->
    @if($driverConflict || $vehicleConflict)
        <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-sm space-y-1">
            <div class="font-extrabold text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                AMARAN: PENUGASAN BERTINDIH DIKESAN (CONFLICT DETECTED)
            </div>
            @if($driverConflict)
                <div class="text-xs">
                    • <strong>Konflik Pemandu:</strong> {{ $vehicleRequest->driver?->name }} mempunyai tugasan bertindih dengan <a href="{{ route('requests.show', $driverConflict->id) }}" class="underline font-bold">{{ $driverConflict->request_number }}</a> ({{ $driverConflict->start_date->format('d/m/Y') }} {{ \Carbon\Carbon::parse($driverConflict->start_time)->format('h:i A') }}).
                </div>
            @endif
            @if($vehicleConflict)
                <div class="text-xs">
                    • <strong>Konflik Kenderaan:</strong> Kenderaan {{ $vehicleRequest->vehicle?->plate_number }} digunakan untuk <a href="{{ route('requests.show', $vehicleConflict->id) }}" class="underline font-bold">{{ $vehicleConflict->request_number }}</a>.
                </div>
            @endif
            @if($vehicleRequest->override_conflict)
                <div class="text-[11px] font-bold text-amber-800 bg-amber-100/70 p-1.5 rounded mt-1">
                    ✓ UPF telah mengesahkan kebenaran override: "{{ $vehicleRequest->override_conflict_reason }}"
                </div>
            @endif
        </div>
    @endif

    <!-- Workflow Progress Step Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm overflow-x-auto">
        <div class="min-w-[650px] flex items-center justify-between text-center text-xs">
            <!-- 1. Dihantar -->
            <div class="flex-1">
                <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center font-bold {{ $vehicleRequest->status !== 'draft' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                    1
                </div>
                <div class="font-bold text-slate-800 mt-1">Dihantar</div>
                <div class="text-[10px] text-slate-400">{{ $vehicleRequest->created_at->format('d/m H:i') }}</div>
            </div>

            <div class="w-12 h-1 {{ in_array($vehicleRequest->status, ['approved', 'assigned', 'driver_accepted', 'in_progress', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>

            <!-- 2. Kelulusan & Penugasan -->
            <div class="flex-1">
                <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center font-bold {{ in_array($vehicleRequest->status, ['approved', 'assigned', 'driver_accepted', 'in_progress', 'completed']) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                    2
                </div>
                <div class="font-bold text-slate-800 mt-1">Penugasan UPF</div>
                <div class="text-[10px] text-slate-400">{{ $vehicleRequest->assigned_at ? $vehicleRequest->assigned_at->format('d/m H:i') : 'Menunggu' }}</div>
            </div>

            <div class="w-12 h-1 {{ in_array($vehicleRequest->status, ['driver_accepted', 'in_progress', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>

            <!-- 3. Penerimaan Pemandu -->
            <div class="flex-1">
                <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center font-bold {{ in_array($vehicleRequest->status, ['driver_accepted', 'in_progress', 'completed']) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                    3
                </div>
                <div class="font-bold text-slate-800 mt-1">Diterima Pemandu</div>
                <div class="text-[10px] text-slate-400">{{ $vehicleRequest->driver_accepted_at ? $vehicleRequest->driver_accepted_at->format('d/m H:i') : '-' }}</div>
            </div>

            <div class="w-12 h-1 {{ in_array($vehicleRequest->status, ['in_progress', 'completed']) ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>

            <!-- 4. Perjalanan & Serahan -->
            <div class="flex-1">
                <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center font-bold {{ in_array($vehicleRequest->status, ['in_progress', 'completed']) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                    4
                </div>
                <div class="font-bold text-slate-800 mt-1">Perjalanan Aktif</div>
                <div class="text-[10px] text-slate-400">{{ $vehicleRequest->trip_started_at ? $vehicleRequest->trip_started_at->format('d/m H:i') : '-' }}</div>
            </div>

            <div class="w-12 h-1 {{ $vehicleRequest->status === 'completed' ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>

            <!-- 5. Pemulangan & Selesai -->
            <div class="flex-1">
                <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center font-bold {{ $vehicleRequest->status === 'completed' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                    5
                </div>
                <div class="font-bold text-slate-800 mt-1">Selesai</div>
                <div class="text-[10px] text-slate-400">{{ $vehicleRequest->trip_completed_at ? $vehicleRequest->trip_completed_at->format('d/m H:i') : '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Operasi Action Controls -->
    @php
        $isSelfDriveApplicant = (!$vehicleRequest->assigned_driver_id && $vehicleRequest->assigned_vehicle_id && auth()->id() === $vehicleRequest->user_id);
    @endphp
    @if(auth()->user()->isDriver() || auth()->user()->isUpf() || $isSelfDriveApplicant)
        <div class="bg-gradient-to-r from-slate-900 to-asm-950 text-white rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-xs font-bold text-amber-400 uppercase tracking-wider">
                    Tindakan Cepat Operasi {{ $isSelfDriveApplicant ? '(Pandu Sendiri)' : '' }}
                </div>
                <div class="text-xs text-slate-300">Kemaskini fasa pergerakan dan rekod serahan/pemulangan kenderaan</div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($vehicleRequest->status === 'assigned' && auth()->user()->isDriver() && !$isSelfDriveApplicant)
                    <form method="POST" action="{{ route('driver.tasks.accept', $vehicleRequest->id) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition">
                            <i class="fa-solid fa-check mr-1"></i> Saya Telah Terima Tugasan
                        </button>
                    </form>
                @endif

                @if(in_array($vehicleRequest->status, ['assigned', 'driver_accepted']) && (auth()->user()->isDriver() || $isSelfDriveApplicant))
                    <form method="POST" action="{{ route('driver.tasks.start', $vehicleRequest->id) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow transition">
                            <i class="fa-solid fa-car mr-1"></i> Mula Perjalanan
                        </button>
                    </form>
                @endif

                @if($vehicleRequest->status === 'in_progress' && (auth()->user()->isDriver() || $isSelfDriveApplicant))
                    <form method="POST" action="{{ route('driver.tasks.complete', $vehicleRequest->id) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition">
                            <i class="fa-solid fa-flag-checkered mr-1"></i> Tandakan Selesai
                        </button>
                    </form>
                @endif

                @if($vehicleRequest->assigned_vehicle_id && !$vehicleRequest->handover)
                    <a href="{{ route('handovers.create', $vehicleRequest->id) }}" class="px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow transition">
                        <i class="fa-solid fa-key mr-1"></i> Rekod Ambil / Meter
                    </a>
                @endif

                @if($vehicleRequest->handover && !$vehicleRequest->returnRecord)
                    <a href="{{ route('handovers.return.create', $vehicleRequest->id) }}" class="px-3 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow transition">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Rekod Pulang / Meter Akhir
                    </a>
                @endif

                @if($vehicleRequest->returnRecord && !$vehicleRequest->returnRecord->is_upf_verified && (auth()->user()->isUpf() || auth()->user()->isAdmin()))
                    <button type="button" @click="verifyReturnModal = true" class="px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow transition flex items-center gap-1.5 animate-pulse">
                        <i class="fa-solid fa-clipboard-check"></i> Sahkan Pemeriksaan (UPF)
                    </button>
                @endif

                @if(auth()->user()->isDriver() || auth()->user()->isUpf())
                    <a href="{{ route('fuel.create', ['request_id' => $vehicleRequest->id]) }}" class="px-3 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow transition">
                        <i class="fa-solid fa-gas-pump mr-1"></i> Log Minyak
                    </a>

                    <a href="{{ route('incidents.create', ['request_id' => $vehicleRequest->id, 'vehicle_id' => $vehicleRequest->assigned_vehicle_id]) }}" class="px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow transition">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Lapor Kerosakan
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Content Sections (Bahagian A, B, C, D) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- BAHAGIAN A: DIISI OLEH PEMOHON -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                    <span class="w-6 h-6 rounded-lg bg-asm-950 text-white flex items-center justify-center text-xs">A</span>
                    BAHAGIAN A - PEMOHON
                </h2>
                <span class="text-[10px] text-slate-400">Borang Asal UPFIT</span>
            </div>

            <div class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Nama Pemohon:</span>
                        <div class="font-bold text-slate-900">{{ $vehicleRequest->applicant_name }}</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Jawatan:</span>
                        <div class="font-bold text-slate-800">{{ $vehicleRequest->applicant_position }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Bahagian:</span>
                        <div class="text-slate-800">{{ $vehicleRequest->applicant_department }}</div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px]">No. Telefon:</span>
                        <div class="text-slate-800">{{ $vehicleRequest->applicant_phone }}</div>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Tarikh & Waktu Diperlukan:</span>
                        <div class="font-bold text-slate-900 text-sm">
                            {{ $vehicleRequest->start_date->format('d/m/Y') }} jam {{ \Carbon\Carbon::parse($vehicleRequest->start_time)->format('h:i A') }}
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Sehingga Bila (Tarikh & Masa Tamat):</span>
                        <div class="font-bold text-slate-900">
                            {{ $vehicleRequest->end_date->format('d/m/Y') }} jam {{ \Carbon\Carbon::parse($vehicleRequest->end_time)->format('h:i A') }}
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Jangka Tiba di Destinasi:</span>
                        <div class="text-slate-800 font-medium">
                            {{ $vehicleRequest->arrival_time ? \Carbon\Carbon::parse($vehicleRequest->arrival_time)->format('h:i A') : '-' }}
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Lokasi Ambil:</span>
                    <div class="font-bold text-slate-800">{{ $vehicleRequest->origin }}</div>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Destinasi:</span>
                    <div class="font-bold text-slate-900">{{ $vehicleRequest->destination }}</div>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Tujuan:</span>
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-slate-800 font-medium">
                        {{ $vehicleRequest->purpose }}
                    </div>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Pegawai Lain yang Turut Serta:</span>
                    <div class="text-slate-700">{{ $vehicleRequest->other_passengers ?: 'Tiada' }}</div>
                </div>

                <!-- Keperluan Dimohon -->
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block mb-1">Keperluan Dimohon Pemohon:</span>
                    <div class="flex flex-wrap gap-2">
                        @if($vehicleRequest->need_driver)
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-100 text-blue-900 border border-blue-200 flex items-center gap-1">
                                <i class="fa-solid fa-user-tie text-xs"></i> Perkhidmatan Pemandu UPF
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300 flex items-center gap-1">
                                <i class="fa-solid fa-car-side text-emerald-700 text-xs"></i> Pandu Sendiri (Tanpa Pemandu)
                            </span>
                        @endif
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $vehicleRequest->need_smart_tag ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400' }}">
                            {{ $vehicleRequest->need_smart_tag ? '✓ Smart Tag' : '✗ Smart Tag' }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $vehicleRequest->need_fuel_card ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400' }}">
                            {{ $vehicleRequest->need_fuel_card ? '✓ Kad Inden Petrol' : '✗ Kad Inden' }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $vehicleRequest->need_gps ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400' }}">
                            {{ $vehicleRequest->need_gps ? '✓ GPS' : '✗ GPS' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- BAHAGIAN B: DIISI OLEH UPF -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                    <span class="w-6 h-6 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center text-xs">B</span>
                    BAHAGIAN B - PENGESAHAN & PENUGASAN UPF
                </h2>
                @if(auth()->user()->isUpf())
                    <a href="{{ route('upf.assign.show', $vehicleRequest->id) }}" class="text-xs font-bold text-asm-600 hover:underline">
                        Kemaskini Penugasan
                    </a>
                @endif
            </div>

            <div class="space-y-4 text-xs">
                <!-- Pemandu -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                    <div class="text-[10px] font-bold uppercase text-slate-400">Pemandu Ditugaskan:</div>
                    @if($vehicleRequest->driver)
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-extrabold text-sm text-slate-900">{{ $vehicleRequest->driver->name }}</div>
                                <div class="text-slate-500 text-[11px]">Jawatan: {{ $vehicleRequest->driver->position }}</div>
                                <div class="text-slate-500 text-[11px]">Telefon: <strong>{{ $vehicleRequest->driver->phone }}</strong></div>
                            </div>
                            <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Ditetapkan
                            </span>
                        </div>
                    @elseif($vehicleRequest->assigned_vehicle_id)
                        <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 space-y-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 font-black text-amber-900 text-xs">
                                    <i class="fa-solid fa-car-side text-amber-600"></i>
                                    <span>Kenderaan Diberikan Kepada Pemohon (Pandu Sendiri)</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-200 text-amber-950">
                                    Pandu Sendiri
                                </span>
                            </div>
                            <div class="text-[11px] text-amber-800 leading-relaxed">
                                @if($vehicleRequest->need_driver)
                                    <span class="font-bold underline">Maklum Balas UPF:</span> Atas faktor ketiadaan pemandu pada tarikh/masa ini, pihak UPF meluluskan kenderaan jabatan untuk diserahkan kepada pemohon (<strong>{{ $vehicleRequest->applicant_name }}</strong>) untuk dipandu sendiri.
                                @else
                                    Kenderaan diluluskan untuk dipandu sendiri oleh pemohon mengikut permohonan asal.
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-rose-600 font-bold italic">Belum Ditetapkan oleh UPF</div>
                    @endif
                </div>

                <!-- Kenderaan -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                    <div class="text-[10px] font-bold uppercase text-slate-400">Kenderaan Ditetapkan:</div>
                    @if($vehicleRequest->vehicle)
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-extrabold text-sm text-slate-900">
                                    {{ $vehicleRequest->vehicle->brand }} {{ $vehicleRequest->vehicle->model }}
                                </div>
                                <div class="font-mono text-xs font-black text-asm-700">
                                    No. Pendaftaran: {{ $vehicleRequest->vehicle->plate_number }}
                                </div>
                                <div class="text-slate-500 text-[11px]">
                                    Jenis: {{ $vehicleRequest->vehicle->type }} | Warna: {{ $vehicleRequest->vehicle->color }}
                                </div>
                            </div>
                            <a href="{{ route('vehicles.show', $vehicleRequest->vehicle->id) }}" class="px-2 py-1 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-[10px]">
                                Profil Kenderaan
                            </a>
                        </div>
                    @else
                        <div class="text-rose-600 font-bold italic">Belum Ditetapkan oleh UPF</div>
                    @endif
                </div>

                <!-- Kelengkapan Ditetapkan UPF -->
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block mb-1">Kelengkapan Diluluskan & Ditetapkan UPF:</span>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="p-2 rounded-lg border text-center font-bold {{ $vehicleRequest->assigned_smart_tag ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                            {{ $vehicleRequest->assigned_smart_tag ? '✓' : '✗' }} Smart Tag
                        </div>
                        <div class="p-2 rounded-lg border text-center font-bold {{ $vehicleRequest->assigned_fuel_card ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                            {{ $vehicleRequest->assigned_fuel_card ? '✓' : '✗' }} Kad Inden
                        </div>
                        <div class="p-2 rounded-lg border text-center font-bold {{ $vehicleRequest->assigned_gps ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                            {{ $vehicleRequest->assigned_gps ? '✓' : '✗' }} GPS
                        </div>
                    </div>
                </div>

                <!-- Catatan UPF & Pegawai Pengesah -->
                <div class="pt-2 border-t border-slate-100 space-y-1">
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Catatan / Arahan UPF:</span>
                    <div class="text-slate-700 italic bg-amber-50/50 p-2 rounded border border-amber-100">
                        {{ $vehicleRequest->upf_remarks ?: 'Tiada catatan tambahan daripada UPF.' }}
                    </div>
                    <div class="text-[11px] text-slate-400 pt-1">
                        Pegawai Penugasan: <strong>{{ $vehicleRequest->assignedBy?->name ?? 'UPF Administrator' }}</strong>
                        @if($vehicleRequest->assigned_at)
                            pada {{ $vehicleRequest->assigned_at->format('d/m/Y h:i A') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BAHAGIAN C & D: SERAHAN & PEMULANGAN (AMBIL & PULANG) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- BAHAGIAN C: SERAHAN KENDERAAN (AMBIL) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                    <span class="w-6 h-6 rounded-lg bg-sky-600 text-white flex items-center justify-center text-xs">C</span>
                    BAHAGIAN C - SERAHAN KENDERAAN (AMBIL)
                </h2>
                @if(!$vehicleRequest->handover && in_array($vehicleRequest->status, ['assigned', 'driver_accepted', 'in_progress']))
                    <a href="{{ route('handovers.create', $vehicleRequest->id) }}" class="text-xs font-bold text-sky-600 hover:underline">
                        + Rekod Serahan
                    </a>
                @endif
            </div>

            @if($vehicleRequest->handover)
                @php $h = $vehicleRequest->handover; @endphp
                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Tarikh & Masa Ambil:</span>
                            <div class="font-bold text-slate-900">{{ $h->handover_date->format('d/m/Y') }} jam {{ $h->handover_time }}</div>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Diterima Oleh:</span>
                            <div class="font-bold text-slate-800">{{ $h->received_by_name }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Bacaan Meter Awal:</span>
                            <div class="font-black text-sm text-slate-900 font-mono">{{ number_format($h->start_mileage) }} KM</div>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Aras Bahan Api:</span>
                            <div class="font-bold text-emerald-700">{{ $h->fuel_level }}</div>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block mb-1">Checklist Semakan Fizikal:</span>
                        <div class="flex flex-wrap gap-1.5 text-[11px]">
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">✓ Keadaan Fizikal (Body)</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">✓ Tekanan Tayar</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">✓ Kunci Kenderaan</span>
                            @if($h->check_smart_tag) <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 border border-blue-200">✓ Smart Tag</span> @endif
                            @if($h->check_fuel_card) <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 border border-blue-200">✓ Kad Inden</span> @endif
                        </div>
                    </div>

                    @if($h->condition_notes)
                        <div class="text-[11px] text-slate-600 bg-slate-50 p-2 rounded border border-slate-200">
                            <strong>Catatan:</strong> {{ $h->condition_notes }}
                        </div>
                    @endif

                    <div class="text-[10px] text-slate-400 pt-1">
                        Pegawai Serahan UPF: <strong>{{ $h->handoverBy?->name ?? 'Pegawai UPF' }}</strong>
                    </div>
                </div>
            @else
                <div class="p-6 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-key text-2xl text-slate-300 mb-1"></i>
                    <div>Kenderaan belum diambil / diserahkan secara rasmi.</div>
                </div>
            @endif
        </div>

        <!-- BAHAGIAN D: PEMULANGAN KENDERAAN (PULANG) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                    <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs">D</span>
                    BAHAGIAN D - PEMULANGAN KENDERAAN (PULANG)
                </h2>
                @if(!$vehicleRequest->returnRecord && $vehicleRequest->handover)
                    <a href="{{ route('handovers.return.create', $vehicleRequest->id) }}" class="text-xs font-bold text-teal-600 hover:underline">
                        + Rekod Pemulangan
                    </a>
                @endif
            </div>

            @if($vehicleRequest->returnRecord)
                @php $r = $vehicleRequest->returnRecord; @endphp
                <div class="space-y-3.5 text-xs">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Tarikh & Masa Pulang:</span>
                            <div class="font-bold text-slate-900">{{ $r->return_date->format('d/m/Y') }} jam {{ $r->return_time }}</div>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Dipulangkan Oleh:</span>
                            <div class="font-bold text-slate-800">{{ $r->returned_by_name }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200 text-center">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[9px]">Meter Awal</span>
                            <div class="font-mono font-bold text-slate-700 text-xs">{{ number_format($vehicleRequest->handover?->start_mileage ?? 0) }} KM</div>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[9px]">Meter Akhir</span>
                            <div class="font-mono font-bold text-slate-900 text-xs">{{ number_format($r->return_mileage) }} KM</div>
                        </div>
                        <div>
                            <span class="text-emerald-800 font-black uppercase text-[9px]">Jumlah Perjalanan</span>
                            <div class="font-mono font-black text-emerald-800 text-sm">{{ number_format($r->total_km) }} KM</div>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block mb-1">Kelengkapan Dipulangkan:</span>
                        <div class="flex flex-wrap gap-1.5 text-[11px]">
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">✓ Kunci Kenderaan</span>
                            @if($r->return_smart_tag) <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">✓ Smart Tag</span> @endif
                            @if($r->return_fuel_card) <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">✓ Kad Inden</span> @endif
                            @if($r->return_gps) <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">✓ GPS</span> @endif
                        </div>
                    </div>

                    @if($r->condition_notes)
                        <div class="text-[11px] text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                            <strong>Catatan Pemulangan:</strong> {{ $r->condition_notes }}
                        </div>
                    @endif

                    <!-- PENGESAHAN PEMERIKSAAN KEADAAN KENDERAAN OLEH UPF -->
                    <div class="pt-2 border-t border-slate-100">
                        @if($r->is_upf_verified)
                            <div class="p-3.5 rounded-2xl bg-emerald-50 border-2 border-emerald-300 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 font-black text-xs text-emerald-950">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                        <span>DISAHKAN DALAM KEADAAN BAIK & DITERIMA OLEH UPF</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $r->upf_condition_status === 'Ada Kerosakan' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-emerald-200 text-emerald-900' }}">
                                        {{ $r->upf_condition_status ?? 'Baik & Sempurna' }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-700 bg-white/70 p-2 rounded-lg border border-emerald-200/60">
                                    <span class="font-bold text-slate-500 uppercase text-[9px] block">Catatan Pemeriksaan UPF:</span>
                                    {{ $r->upf_verification_notes ?: 'Pemeriksaan fizikal kenderaan telah selesai. Kenderaan disahkan diterima dalam keadaan baik & sempurna.' }}
                                </div>
                                <div class="text-[10px] text-slate-500 flex items-center justify-between pt-1">
                                    <span>Pegawai Pemeriksa UPF: <strong class="text-slate-900">{{ $r->verifiedBy?->name ?? $r->receivedBy?->name ?? 'Pegawai UPF' }}</strong></span>
                                    <span>Disahkan pada: <strong>{{ $r->upf_verified_at ? $r->upf_verified_at->format('d/m/Y h:i A') : '-' }}</strong></span>
                                </div>
                            </div>
                        @else
                            <div class="p-3.5 rounded-2xl bg-amber-50 border-2 border-amber-300 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 font-black text-xs text-amber-950">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                        <span>MENUNGGU PENGESAHAN KEADAAN FIZIKAL OLEH UPF</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-900">
                                        Perlu Semakan Fizikal
                                    </span>
                                </div>
                                <p class="text-[11px] text-amber-800 leading-relaxed">
                                    Kenderaan telah dipulangkan oleh {{ $r->returned_by_name }}. Pegawai UPF perlu memeriksa keadaan fizikal kenderaan dan mengesahkan penerimaan kenderaan dalam keadaan baik.
                                </p>
                                @if(auth()->user()->isUpf() || auth()->user()->isAdmin())
                                    <div class="pt-1">
                                        <button type="button" @click="verifyReturnModal = true" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow flex items-center justify-center gap-1.5 transition">
                                            <i class="fa-solid fa-clipboard-check"></i> Lakukan Pemeriksaan & Sahkan Keadaan Kenderaan
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="p-6 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-flag-checkered text-2xl text-slate-300 mb-1"></i>
                    <div>Kenderaan belum dipulangkan / direkodkan meter akhir.</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Log Minyak Berkaitan Permohonan Ini -->
    @if($vehicleRequest->fuelLogs->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-gas-pump text-amber-500"></i> Rekod Isian Minyak & Resit
            </h3>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($vehicleRequest->fuelLogs as $f)
                    <div class="py-2 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900">{{ $f->station_name }}</span>
                            <span class="text-slate-400 ml-2">{{ $f->log_date->format('d/m/Y') }} {{ $f->log_time }}</span>
                            <div class="text-[11px] text-slate-500">
                                {{ $f->liters }} Liter ({{ $f->fuel_type }}) @ RM {{ number_format($f->price_per_liter, 2) }} | Kaedah: <strong class="text-slate-700">{{ $f->payment_method }}</strong>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-sm text-slate-900">RM {{ number_format($f->total_amount, 2) }}</span>
                            @if($f->receipt_photo_path)
                                <div><a href="/storage/{{ $f->receipt_photo_path }}" target="_blank" class="text-[11px] font-bold text-asm-600 hover:underline">Lihat Resit</a></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Cancellation Modal -->
    <div x-show="cancelModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl" @click.away="cancelModal = false">
            <div class="flex items-center space-x-3 text-rose-600">
                <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                <h3 class="text-base font-black uppercase tracking-wide text-slate-900">Pengesahan Pembatalan Permohonan</h3>
            </div>

            @if($vehicleRequest->assigned_driver_id || $vehicleRequest->assigned_vehicle_id)
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium">
                    "Permohonan ini telah mempunyai pemandu dan kenderaan yang ditugaskan. Adakah anda pasti ingin membatalkan?"
                </div>
            @else
                <p class="text-xs text-slate-600">
                    Adakah anda pasti ingin membatalkan permohonan kenderaan ini? Tindakan ini akan direkodkan dalam audit trail.
                </p>
            @endif

            <form method="POST" action="{{ route('requests.cancel', $vehicleRequest->id) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sebab Pembatalan <span class="text-rose-500">*</span></label>
                    <textarea name="cancellation_reason" rows="3" required placeholder="Nyatakan sebab mesyuarat dibatalkan atau ditunda..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" @click="cancelModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Tutup
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow">
                        Sahkan Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- UPF Return Verification Modal -->
    @if($vehicleRequest->returnRecord && (auth()->user()->isUpf() || auth()->user()->isAdmin()))
        <div x-show="verifyReturnModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl" @click.away="verifyReturnModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2 text-indigo-700">
                        <i class="fa-solid fa-clipboard-check text-xl"></i>
                        <h3 class="text-sm font-black uppercase tracking-wide text-slate-900">PENGESAHAN PEMERIKSAAN KENDERAAN (UPF)</h3>
                    </div>
                    <button type="button" @click="verifyReturnModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">No. Permohonan:</span>
                        <strong class="text-slate-900">{{ $vehicleRequest->request_number }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">Kenderaan:</span>
                        <strong class="text-slate-900">{{ $vehicleRequest->vehicle?->plate_number }} ({{ $vehicleRequest->vehicle?->brand }} {{ $vehicleRequest->vehicle?->model }})</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">Dipulangkan Oleh:</span>
                        <strong class="text-slate-900">{{ $vehicleRequest->returnRecord->returned_by_name }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-bold uppercase text-[10px]">Meter Akhir & Jarak:</span>
                        <strong class="font-mono text-emerald-800">{{ number_format($vehicleRequest->returnRecord->return_mileage) }} KM ({{ number_format($vehicleRequest->returnRecord->total_km) }} KM)</strong>
                    </div>
                </div>

                <form method="POST" action="{{ route('handovers.return.verify', $vehicleRequest->id) }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-800 uppercase mb-1.5">
                            Status Pemeriksaan Fizikal Kenderaan <span class="text-rose-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="p-3 rounded-xl border-2 border-emerald-400 bg-white hover:bg-emerald-50 cursor-pointer flex items-center space-x-2.5">
                                <input type="radio" name="upf_condition_status" value="Baik & Sempurna" checked class="text-emerald-600">
                                <div>
                                    <div class="font-bold text-slate-900">Baik & Sempurna</div>
                                    <div class="text-[10px] text-slate-500">Tiada kerosakan atau kemik, ruang dalam bersih, sedia untuk perjalanan seterusnya.</div>
                                </div>
                            </label>
                            <label class="p-3 rounded-xl border border-slate-300 bg-white hover:bg-amber-50 cursor-pointer flex items-center space-x-2.5">
                                <input type="radio" name="upf_condition_status" value="Memuaskan" class="text-amber-600">
                                <div>
                                    <div class="font-bold text-slate-900">Memuaskan</div>
                                    <div class="text-[10px] text-slate-500">Kotoran biasa / haus ringan, tidak memerlukan servis pembaikan segera.</div>
                                </div>
                            </label>
                            <label class="p-3 rounded-xl border border-slate-300 bg-white hover:bg-rose-50 cursor-pointer flex items-center space-x-2.5">
                                <input type="radio" name="upf_condition_status" value="Ada Kerosakan" class="text-rose-600">
                                <div>
                                    <div class="font-bold text-slate-900">Ada Kerosakan / Perlu Tindakan Servis</div>
                                    <div class="text-[10px] text-slate-500">Terdapat isu mekanikal atau kerosakan fizikal, kenderaan akan ditukar ke Penyelenggaraan.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 uppercase mb-1">Catatan Pemeriksaan Fizikal Pegawai UPF</label>
                        <textarea name="upf_verification_notes" rows="3" placeholder="Nyatakan ulasan atau hasil pemeriksaan fizikal kenderaan..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500">Pemeriksaan fizikal selesai. Kenderaan disahkan diterima dalam keadaan baik dan sempurna.</textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="verifyReturnModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow">
                            Sahkan Penerimaan & Keadaan Kenderaan (UPF)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
