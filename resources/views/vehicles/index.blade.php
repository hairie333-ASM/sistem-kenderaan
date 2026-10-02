@extends('layouts.app')

@section('title', 'Master Data Kenderaan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-asm-100 text-asm-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-car mr-1"></i> Pengurusan Kenderaan Jabatan UPF
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                MASTER DATA KENDERAAN
            </h1>
            <p class="text-xs text-slate-500">
                Pendaftaran rasmi kenderaan pejabat Akademi Sains Malaysia (ASM)
            </p>
        </div>

        @if(auth()->user()->isUpf())
            <a href="{{ route('vehicles.create') }}" class="px-4 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 self-start sm:self-auto">
                <i class="fa-solid fa-circle-plus"></i> + Tambah Kenderaan Baharu
            </a>
        @endif
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Kenderaan</div>
            <div class="text-xl font-black text-slate-900 mt-0.5">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Boleh Digunakan</div>
            <div class="text-xl font-black text-emerald-600 mt-0.5">{{ $stats['available'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Telah Ditetapkan</div>
            <div class="text-xl font-black text-blue-600 mt-0.5">{{ $stats['assigned'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Sedang Digunakan</div>
            <div class="text-xl font-black text-amber-600 mt-0.5">{{ $stats['in_use'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Penyelenggaraan</div>
            <div class="text-xl font-black text-purple-600 mt-0.5">{{ $stats['maintenance'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Keluar Operasi</div>
            <div class="text-xl font-black text-rose-600 mt-0.5">{{ $stats['out_of_service'] }}</div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('vehicles.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Plat, Jenama, Model atau Kod..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <option value="">Semua Status</option>
                    <option value="Available" {{ request('status') === 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Assigned" {{ request('status') === 'Assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="In Use" {{ request('status') === 'In Use' ? 'selected' : '' }}>In Use</option>
                    <option value="Maintenance" {{ request('status') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Out of Service" {{ request('status') === 'Out of Service' ? 'selected' : '' }}>Out of Service</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs transition">
                    Tapis
                </button>
                <a href="{{ route('vehicles.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs text-center transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Vehicles Cards Grid (Prompt Section 4 & 24) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($vehicles as $vehicle)
            <div class="bg-white rounded-3xl border border-slate-200 hover:border-asm-300 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col justify-between group">
                <div>
                    <!-- Vehicle Card Header -->
                    <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">{{ $vehicle->vehicle_code }}</span>
                            <div class="text-sm font-black text-slate-900">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $vehicle->status_badge['class'] }}">
                            {{ $vehicle->status_badge['label'] }}
                        </span>
                    </div>

                    <!-- Vehicle Plate Banner -->
                    <div class="p-4 space-y-3">
                        <div class="text-center py-2 px-4 rounded-xl bg-slate-950 text-white font-mono font-black text-lg tracking-widest border-2 border-slate-800 shadow-inner">
                            {{ $vehicle->plate_number }}
                        </div>

                        <!-- Specs List -->
                        <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                            <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[9px] font-bold uppercase text-slate-400 block">Jenis / Warna</span>
                                <span class="font-bold text-slate-800">{{ $vehicle->type }} ({{ $vehicle->year ?: '-' }})</span>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[9px] font-bold uppercase text-slate-400 block">Bacaan Meter</span>
                                <span class="font-bold text-slate-900 font-mono">{{ number_format($vehicle->current_mileage) }} KM</span>
                            </div>
                        </div>

                        <!-- Roadtax & Servis -->
                        <div class="text-[11px] text-slate-500 space-y-1 pt-1">
                            <div class="flex items-center justify-between">
                                <span>Roadtax Tamat:</span>
                                <strong class="text-slate-700">{{ $vehicle->roadtax_expiry ? $vehicle->roadtax_expiry->format('d/m/Y') : '-' }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Servis Seterusnya:</span>
                                <strong class="text-slate-700">{{ $vehicle->next_service_mileage ? number_format($vehicle->next_service_mileage) . ' KM' : '-' }}</strong>
                            </div>
                        </div>

                        @if($vehicle->notes)
                            <div class="text-[11px] text-slate-600 bg-amber-50/60 p-2 rounded-xl border border-amber-200 italic line-clamp-2">
                                {{ $vehicle->notes }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2 text-xs">
                    <a href="{{ route('vehicles.show', $vehicle->id) }}" class="flex-1 py-1.5 px-3 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-bold text-center transition">
                        Lihat Profil Penuh
                    </a>
                    @if(auth()->user()->isUpf())
                        <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="p-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 transition" title="Kemaskini">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 p-12 text-center text-slate-400 bg-white rounded-3xl border border-slate-200">
                <i class="fa-solid fa-car text-4xl mb-2 text-slate-300"></i>
                <div class="text-sm font-bold text-slate-700">Tiada kenderaan dijumpai.</div>
            </div>
        @endforelse
    </div>

    @if($vehicles->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $vehicles->links() }}
        </div>
    @endif
</div>
@endsection
