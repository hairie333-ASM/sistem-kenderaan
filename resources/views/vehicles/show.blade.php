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

            <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Tamat Cukai Jalan (Roadtax)</span>
                    <span class="font-bold text-sm text-slate-900 mt-1 block">
                        {{ $vehicle->roadtax_expiry ? $vehicle->roadtax_expiry->format('d/m/Y') : '-' }}
                    </span>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Tamat Tempoh Insurans</span>
                    <span class="font-bold text-sm text-slate-900 mt-1 block">
                        {{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('d/m/Y') : '-' }}
                    </span>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Pemeriksaan Puspakom</span>
                    <span class="font-bold text-sm text-slate-900 mt-1 block">
                        {{ $vehicle->puspakom_expiry ? $vehicle->puspakom_expiry->format('d/m/Y') : 'Tidak Berkenaan' }}
                    </span>
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
