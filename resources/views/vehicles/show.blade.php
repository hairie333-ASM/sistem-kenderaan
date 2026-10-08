@extends('layouts.app')

@section('title', 'Profil Kenderaan: ' . $vehicle->plate_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('vehicles.index') }}" class="hover:underline font-bold text-asm-600">Master Kenderaan</a>
                <span>/</span>
                <span class="font-mono">{{ $vehicle->plate_number }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-black border {{ $vehicle->status_badge['class'] }}">
                    {{ $vehicle->status_badge['label'] }}
                </span>
            </h1>
        </div>

        @if(auth()->user()->isUpf())
            <div class="flex items-center space-x-2">
                <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i> Kemaskini Maklumat
                </a>
            </div>
        @endif
    </div>

    <!-- Expiry Warning Banner if Roadtax or Insurance Alert Exists -->
    @if($vehicle->alert_level === 'expired')
        <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-400 text-rose-900 shadow-sm flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 text-lg shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-rose-900 flex items-center gap-2">
                        <span>AMARAN: DOKUMEN KENDERAAN TELAH TAMAT TEMPOH</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-200 text-rose-900 uppercase">Perlu Tindakan Segera</span>
                    </h3>
                    <p class="text-xs text-rose-700 mt-0.5">
                        Kenderaan ini mempunyai Cukai Jalan atau Insurans yang telah tamat tempoh. Kenderaan tidak sah digunakan di atas jalan raya sehingga pembaharuan rasmi selesai dibuat oleh UPF.
                    </p>
                    <div class="mt-2.5 flex flex-wrap gap-2 text-xs font-bold">
                        @if($vehicle->roadtax_status['is_expired'])
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 border border-rose-300">
                                <i class="fa-solid fa-file-invoice mr-1.5"></i> Cukai Jalan: {{ $vehicle->roadtax_status['days_text'] }} ({{ $vehicle->roadtax_expiry?->format('d/m/Y') }})
                            </span>
                        @endif
                        @if($vehicle->insurance_status['is_expired'])
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 border border-rose-300">
                                <i class="fa-solid fa-shield-halved mr-1.5"></i> Insurans: {{ $vehicle->insurance_status['days_text'] }} ({{ $vehicle->insurance_expiry?->format('d/m/Y') }})
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @if(auth()->user()->isUpf())
                <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shrink-0 shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i> Kemaskini Tarikh
                </a>
            @endif
        </div>
    @elseif($vehicle->alert_level === 'expiring')
        <div class="p-4 rounded-2xl bg-amber-50 border-2 border-amber-400 text-amber-900 shadow-sm flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-900 flex items-center justify-center shrink-0 text-lg shadow-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-amber-900 flex items-center gap-2">
                        <span>PERINGATAN: DOKUMEN KENDERAAN HAMPIR TAMAT TEMPOH</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900 uppercase">Dalam Tempoh 30 Hari</span>
                    </h3>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Cukai Jalan atau Insurans bagi kenderaan ini akan tamat tidak lama lagi. Sila buat persiapan pembaharuan bersama pembekal insurans/JPJ bagi mengelakkan gangguan tugasan rasmi.
                    </p>
                    <div class="mt-2.5 flex flex-wrap gap-2 text-xs font-bold">
                        @if($vehicle->roadtax_status['is_expiring'])
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 border border-amber-300">
                                <i class="fa-solid fa-file-invoice mr-1.5"></i> Cukai Jalan: {{ $vehicle->roadtax_status['days_text'] }} ({{ $vehicle->roadtax_expiry?->format('d/m/Y') }})
                            </span>
                        @endif
                        @if($vehicle->insurance_status['is_expiring'])
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 border border-amber-300">
                                <i class="fa-solid fa-shield-halved mr-1.5"></i> Insurans: {{ $vehicle->insurance_status['days_text'] }} ({{ $vehicle->insurance_expiry?->format('d/m/Y') }})
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @if(auth()->user()->isUpf())
                <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-black text-xs shrink-0 shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i> Kemaskini Tarikh
                </a>
            @endif
        </div>
    @endif

    <!-- Main Specs & Identity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Vehicle Plate & Quick Status -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4 text-center">
            <div class="py-3 px-6 rounded-2xl bg-slate-950 text-white font-mono font-black text-2xl tracking-widest border-4 border-slate-800 shadow-inner">
                {{ $vehicle->plate_number }}
            </div>
            <div class="text-xs text-slate-500">
                Kod Kenderaan: <strong class="font-mono text-slate-800">{{ $vehicle->vehicle_code }}</strong>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-1 text-left">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Status Semasa:</span>
                    <strong class="text-slate-900">{{ $vehicle->status }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Bacaan Meter Semasa:</span>
                    <strong class="font-mono text-slate-900">{{ number_format($vehicle->current_mileage) }} KM</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Jenis Bahan Api:</span>
                    <strong class="text-slate-900">{{ $vehicle->fuel_type }}</strong>
                </div>
            </div>

            @if($vehicle->notes)
                <div class="text-[11px] text-slate-600 bg-amber-50 p-3 rounded-2xl border border-amber-200 text-left italic">
                    <strong>Catatan UPF:</strong> {{ $vehicle->notes }}
                </div>
            @endif
        </div>

        <!-- Specifications & Regulatory Compliance -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                <span>Spesifikasi & Pematuhan Undang-Undang</span>
                <span class="text-xs text-slate-400 font-normal">Jabatan Pengangkutan Jalan (JPJ)</span>
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Jenama:</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $vehicle->brand }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Model:</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $vehicle->model }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Jenis Kenderaan:</span>
                    <span class="font-bold text-slate-800">{{ $vehicle->type }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Tahun Dikeluarkan:</span>
                    <span class="font-bold text-slate-800">{{ $vehicle->year ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Warna:</span>
                    <span class="font-bold text-slate-800">{{ $vehicle->color ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Syarikat Insurans:</span>
                    <span class="font-bold text-slate-800">{{ $vehicle->insurance_company ?: 'Etiqa Takaful' }}</span>
                </div>
            </div>

            <!-- Roadtax, Insurans & Puspakom Status Badges -->
            <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <!-- Roadtax Box -->
                <div class="p-3.5 rounded-2xl border {{ $vehicle->roadtax_status['status'] === 'expired' ? 'bg-rose-50/70 border-rose-300 ring-1 ring-rose-200' : ($vehicle->roadtax_status['status'] === 'expiring' ? 'bg-amber-50/70 border-amber-300 ring-1 ring-amber-200' : 'bg-slate-50 border-slate-200') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Cukai Jalan (Roadtax)</span>
                        <i class="fa-solid fa-file-invoice {{ $vehicle->roadtax_status['text_class'] }}"></i>
                    </div>
                    <span class="font-black text-base text-slate-900 mt-1 block">
                        {{ $vehicle->roadtax_expiry ? $vehicle->roadtax_expiry->format('d/m/Y') : 'Tiada Rekod' }}
                    </span>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black border {{ $vehicle->roadtax_status['badge_class'] }}">
                            {{ $vehicle->roadtax_status['days_text'] }}
                        </span>
                    </div>
                </div>

                <!-- Insurance Box -->
                <div class="p-3.5 rounded-2xl border {{ $vehicle->insurance_status['status'] === 'expired' ? 'bg-rose-50/70 border-rose-300 ring-1 ring-rose-200' : ($vehicle->insurance_status['status'] === 'expiring' ? 'bg-amber-50/70 border-amber-300 ring-1 ring-amber-200' : 'bg-slate-50 border-slate-200') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Tempoh Sah Insurans</span>
                        <i class="fa-solid fa-shield-halved {{ $vehicle->insurance_status['text_class'] }}"></i>
                    </div>
                    <span class="font-black text-base text-slate-900 mt-1 block">
                        {{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('d/m/Y') : 'Tiada Rekod' }}
                    </span>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black border {{ $vehicle->insurance_status['badge_class'] }}">
                            {{ $vehicle->insurance_status['days_text'] }}
                        </span>
                    </div>
                </div>

                <!-- Puspakom Box -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Pemeriksaan Puspakom</span>
                        <i class="fa-solid fa-truck-ramp-box text-slate-400"></i>
                    </div>
                    <span class="font-black text-base text-slate-900 mt-1 block">
                        {{ $vehicle->puspakom_expiry ? $vehicle->puspakom_expiry->format('d/m/Y') : 'Tidak Berkenaan' }}
                    </span>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $vehicle->puspakom_expiry ? 'Sah JPJ' : 'Dikecualikan' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Logs: Perjalanan, Minyak, Penyelenggaraan -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4" x-data="{ tab: 'trips' }">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <button type="button" @click="tab = 'trips'" :class="tab === 'trips' ? 'bg-asm-950 text-white font-bold' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-1.5 rounded-xl text-xs transition">
                    <i class="fa-solid fa-route mr-1"></i> Sejarah Perjalanan ({{ $vehicle->requests->count() }})
                </button>
                <button type="button" @click="tab = 'fuel'" :class="tab === 'fuel' ? 'bg-asm-950 text-white font-bold' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-1.5 rounded-xl text-xs transition">
                    <i class="fa-solid fa-gas-pump mr-1"></i> Rekod Minyak ({{ $vehicle->fuelLogs->count() }})
                </button>
                <button type="button" @click="tab = 'maintenance'" :class="tab === 'maintenance' ? 'bg-asm-950 text-white font-bold' : 'text-slate-600 hover:bg-slate-100'" class="px-3 py-1.5 rounded-xl text-xs transition">
                    <i class="fa-solid fa-wrench mr-1"></i> Penyelenggaraan ({{ $vehicle->maintenances->count() }})
                </button>
            </div>
        </div>

        <!-- Trips Tab -->
        <div x-show="tab === 'trips'" class="overflow-x-auto text-xs">
            <table class="w-full text-left divide-y divide-slate-100">
                <thead class="text-[10px] uppercase font-bold text-slate-400">
                    <tr>
                        <th class="p-2">No. Permohonan</th>
                        <th class="p-2">Tarikh & Masa</th>
                        <th class="p-2">Tujuan & Destinasi</th>
                        <th class="p-2">Pemandu</th>
                        <th class="p-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vehicle->requests as $req)
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 font-bold text-asm-700">
                                <a href="{{ route('requests.show', $req->id) }}" class="hover:underline">{{ $req->request_number }}</a>
                            </td>
                            <td class="p-2 whitespace-nowrap">{{ $req->start_date->format('d/m/Y') }} {{ \Carbon\Carbon::parse($req->start_time)->format('h:i A') }}</td>
                            <td class="p-2">{{ $req->purpose }} ({{ $req->destination }})</td>
                            <td class="p-2 font-bold">{{ $req->driver?->name ?? 'Tiada Pemandu' }}</td>
                            <td class="p-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $req->status_badge['class'] }}">
                                    {{ $req->status_badge['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-slate-400">Tiada sejarah perjalanan direkodkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Fuel Tab -->
        <div x-show="tab === 'fuel'" x-cloak class="overflow-x-auto text-xs">
            <table class="w-full text-left divide-y divide-slate-100">
                <thead class="text-[10px] uppercase font-bold text-slate-400">
                    <tr>
                        <th class="p-2">Tarikh</th>
                        <th class="p-2">Stesen</th>
                        <th class="p-2">Pemandu</th>
                        <th class="p-2">Liter</th>
                        <th class="p-2">Jumlah (RM)</th>
                        <th class="p-2">Kaedah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vehicle->fuelLogs as $f)
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 whitespace-nowrap">{{ $f->log_date->format('d/m/Y') }}</td>
                            <td class="p-2 font-bold">{{ $f->station_name }}</td>
                            <td class="p-2">{{ $f->driver?->name ?? '-' }}</td>
                            <td class="p-2">{{ $f->liters }} L</td>
                            <td class="p-2 font-bold text-slate-900">RM {{ number_format($f->total_amount, 2) }}</td>
                            <td class="p-2"><span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-bold">{{ $f->payment_method }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-slate-400">Tiada log minyak direkodkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Maintenance Tab -->
        <div x-show="tab === 'maintenance'" x-cloak class="overflow-x-auto text-xs">
            <table class="w-full text-left divide-y divide-slate-100">
                <thead class="text-[10px] uppercase font-bold text-slate-400">
                    <tr>
                        <th class="p-2">Tarikh</th>
                        <th class="p-2">Jenis Servis</th>
                        <th class="p-2">Bengkel</th>
                        <th class="p-2">Meter</th>
                        <th class="p-2">Kos (RM)</th>
                        <th class="p-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vehicle->maintenances as $m)
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 whitespace-nowrap">{{ $m->service_date->format('d/m/Y') }}</td>
                            <td class="p-2 font-bold">{{ $m->maintenance_type }}</td>
                            <td class="p-2">{{ $m->workshop_name }}</td>
                            <td class="p-2 font-mono">{{ number_format($m->service_mileage) }} KM</td>
                            <td class="p-2 font-bold text-slate-900">RM {{ number_format($m->cost, 2) }}</td>
                            <td class="p-2"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">{{ $m->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-slate-400">Tiada rekod penyelenggaraan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
