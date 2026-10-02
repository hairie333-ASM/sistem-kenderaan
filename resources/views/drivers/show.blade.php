@extends('layouts.app')

@section('title', 'Profil Pemandu: ' . $driver->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('drivers.index') }}" class="hover:underline font-bold text-asm-600">Master Pemandu</a>
                <span>/</span>
                <span class="font-mono">{{ $driver->driver_code }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>{{ $driver->name }}</span>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-black border {{ $driver->status_badge['class'] }}">
                    {{ $driver->status_badge['label'] }}
                </span>
            </h1>
        </div>

        @if(auth()->user()->isUpf())
            <div class="flex items-center space-x-2">
                <a href="{{ route('drivers.edit', $driver->id) }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i> Kemaskini Maklumat
                </a>
            </div>
        @endif
    </div>

    <!-- Driver Identity & License Credentials Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Identity Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4 text-center">
            <div class="w-20 h-20 rounded-3xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-3xl mx-auto shadow-inner">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div>
                <h2 class="text-lg font-black text-slate-900">{{ $driver->name }}</h2>
                <div class="text-xs text-slate-500">{{ $driver->position }}</div>
                <div class="text-[11px] font-mono text-emerald-600 font-bold mt-0.5">{{ $driver->driver_code }}</div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-1.5 text-left">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">No. Telefon:</span>
                    <a href="tel:{{ $driver->phone }}" class="font-bold text-emerald-700 hover:underline">{{ $driver->phone }}</a>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">No. Kakitangan:</span>
                    <strong class="font-mono text-slate-800">{{ $driver->staff_number ?: '-' }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">No. Kad Pengenalan:</span>
                    <strong class="font-mono text-slate-800">{{ $driver->ic_number ?: '-' }}</strong>
                </div>
            </div>

            @if($driver->emergency_contact_name)
                <div class="p-3 rounded-2xl bg-amber-50/70 border border-amber-200 text-left text-xs space-y-0.5">
                    <span class="text-[10px] uppercase font-bold text-amber-800 block">Waris Kecemasan:</span>
                    <div class="font-bold text-slate-900">{{ $driver->emergency_contact_name }}</div>
                    <div class="text-[11px] text-slate-600">{{ $driver->emergency_contact_phone }}</div>
                </div>
            @endif
        </div>

        <!-- License & Operational Credentials -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                <span>Kelayakan Memandu & Rekod JPJ</span>
                <span class="text-xs text-slate-400 font-normal">Kategori Lesen & Status</span>
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">No. Lesen Memandu:</span>
                    <span class="font-mono font-bold text-slate-900 text-sm">{{ $driver->license_number ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Kelas Lesen:</span>
                    <span class="font-bold text-emerald-700 text-sm">{{ $driver->license_class }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Tarikh Luput Lesen:</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $driver->license_expiry ? $driver->license_expiry->format('d/m/Y') : '-' }}</span>
                </div>
            </div>

            @if($driver->notes)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 space-y-1">
                    <span class="font-bold text-slate-900 uppercase text-[10px]">Catatan Pengurusan UPF:</span>
                    <p class="italic leading-relaxed">{{ $driver->notes }}</p>
                </div>
            @endif

            <!-- Quick Trip Statistics -->
            <div class="grid grid-cols-3 gap-3 pt-2 text-center text-xs">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] text-slate-400 uppercase font-bold">Jumlah Tugasan</div>
                    <div class="text-xl font-black text-slate-900 mt-1">{{ $driver->assignments->count() }}</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] text-slate-400 uppercase font-bold">Tugasan Selesai</div>
                    <div class="text-xl font-black text-emerald-600 mt-1">
                        {{ $driver->assignments->where('status', 'completed')->count() }}
                    </div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] text-slate-400 uppercase font-bold">Log Minyak</div>
                    <div class="text-xl font-black text-amber-600 mt-1">{{ $driver->fuelLogs->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment History Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
            <span>Sejarah Tugasan Pemandu</span>
            <a href="{{ route('schedules.excel', ['driver_id' => $driver->id]) }}" class="text-xs font-bold text-asm-600 hover:underline">
                Lihat Dalam Jadual Excel
            </a>
        </h2>

        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left divide-y divide-slate-100">
                <thead class="text-[10px] uppercase font-bold text-slate-400">
                    <tr>
                        <th class="p-2">No. Permohonan</th>
                        <th class="p-2">Tarikh & Masa</th>
                        <th class="p-2">Tugasan & Destinasi</th>
                        <th class="p-2">Kenderaan</th>
                        <th class="p-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($driver->assignments as $a)
                        <tr class="hover:bg-slate-50">
                            <td class="p-2 font-bold text-asm-700">
                                <a href="{{ route('requests.show', $a->id) }}" class="hover:underline">{{ $a->request_number }}</a>
                            </td>
                            <td class="p-2 whitespace-nowrap">{{ $a->start_date->format('d/m/Y') }} {{ \Carbon\Carbon::parse($a->start_time)->format('h:i A') }}</td>
                            <td class="p-2">{{ $a->purpose }} ({{ $a->destination }})</td>
                            <td class="p-2 font-mono font-bold">{{ $a->vehicle?->plate_number ?? '-' }}</td>
                            <td class="p-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $a->status_badge['class'] }}">
                                    {{ $a->status_badge['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-slate-400">Tiada tugasan direkodkan untuk pemandu ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
