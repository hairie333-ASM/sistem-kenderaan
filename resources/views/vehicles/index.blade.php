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

    <!-- Expiry Alert Banner -->
    @if($stats['alerts_total'] > 0)
        <div class="p-4 rounded-2xl border {{ $stats['alerts_expired'] > 0 ? 'bg-rose-50/90 border-rose-300' : 'bg-amber-50/90 border-amber-300' }} shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $stats['alerts_expired'] > 0 ? 'bg-rose-600 text-white' : 'bg-amber-500 text-slate-900' }} flex items-center justify-center shrink-0 text-lg shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-sm font-black {{ $stats['alerts_expired'] > 0 ? 'text-rose-900' : 'text-amber-900' }}">
                                PERINGATAN PEMBAHARUAN CUKAI JALAN & INSURANS
                            </h2>
                            @if($stats['alerts_expired'] > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-200 text-rose-900 animate-pulse">
                                    {{ $stats['alerts_expired'] }} Tamat Tempoh
                                </span>
                            @endif
                            @if($stats['alerts_expiring'] > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900">
                                    {{ $stats['alerts_expiring'] }} Hampir Tamat (≤ 30 Hari)
                                </span>
                            @endif
                        </div>
                        <p class="text-xs {{ $stats['alerts_expired'] > 0 ? 'text-rose-700' : 'text-amber-700' }} mt-0.5">
                            Terdapat {{ $stats['alerts_total'] }} kenderaan memerlukan perhatian atau pembaharuan dokumen rasmi oleh Pegawai UPF.
                        </p>
                    </div>
                </div>

                <!-- Quick Filter Buttons -->
                <div class="flex flex-wrap items-center gap-2 self-start md:self-auto text-xs">
                    <a href="{{ route('vehicles.index', ['alert' => 'all']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ request('alert') === 'all' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                        Semua Alert ({{ $stats['alerts_total'] }})
                    </a>
                    @if($stats['alerts_expired'] > 0)
                        <a href="{{ route('vehicles.index', ['alert' => 'expired']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ request('alert') === 'expired' ? 'bg-rose-600 text-white' : 'bg-rose-100 text-rose-800 hover:bg-rose-200 border border-rose-300' }}">
                            Tamat Tempoh ({{ $stats['alerts_expired'] }})
                        </a>
                    @endif
                    @if($stats['alerts_expiring'] > 0)
                        <a href="{{ route('vehicles.index', ['alert' => 'expiring']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ request('alert') === 'expiring' ? 'bg-amber-500 text-slate-900' : 'bg-amber-100 text-amber-800 hover:bg-amber-200 border border-amber-300' }}">
                            Hampir Tamat ({{ $stats['alerts_expiring'] }})
                        </a>
                    @endif
                    @if(request()->filled('alert'))
                        <a href="{{ route('vehicles.index') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold transition" title="Padam Tapis Alert">
                            <i class="fa-solid fa-xmark mr-1"></i> Reset Tapis
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('vehicles.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Plat, Jenama, Model atau Kod..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <option value="">Semua Status Kenderaan</option>
                    <option value="Available" {{ request('status') === 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Assigned" {{ request('status') === 'Assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="In Use" {{ request('status') === 'In Use' ? 'selected' : '' }}>In Use</option>
                    <option value="Maintenance" {{ request('status') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Out of Service" {{ request('status') === 'Out of Service' ? 'selected' : '' }}>Out of Service</option>
                </select>
            </div>
            <div>
                <select name="alert" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <option value="">Semua Status Dokumen</option>
                    <option value="all" {{ request('alert') === 'all' ? 'selected' : '' }}>Semua Alert Pembaharuan ({{ $stats['alerts_total'] }})</option>
                    <option value="expired" {{ request('alert') === 'expired' ? 'selected' : '' }}>Tamat Tempoh ({{ $stats['alerts_expired'] }})</option>
                    <option value="expiring" {{ request('alert') === 'expiring' ? 'selected' : '' }}>Hampir Tamat ≤ 30 Hari ({{ $stats['alerts_expiring'] }})</option>
                    <option value="roadtax" {{ request('alert') === 'roadtax' ? 'selected' : '' }}>Isu Cukai Jalan Sahaja</option>
                    <option value="insurance" {{ request('alert') === 'insurance' ? 'selected' : '' }}>Isu Insurans Sahaja</option>
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
            <div class="bg-white rounded-3xl border {{ $vehicle->alert_badge ? $vehicle->alert_badge['border_class'] : 'border-slate-200 hover:border-asm-300' }} shadow-sm hover:shadow-md transition overflow-hidden flex flex-col justify-between group">
                <div>
                    <!-- Vehicle Top Alert Ribbon if Expired or Expiring -->
                    @if($vehicle->alert_badge)
                        <div class="px-4 py-1.5 text-[10px] font-bold flex items-center justify-between {{ $vehicle->alert_badge['bar_class'] }}">
                            <span class="flex items-center gap-1.5">
                                <i class="{{ $vehicle->alert_badge['icon'] }}"></i>
                                <span>{{ $vehicle->alert_badge['label'] }}: Semakan Dokumen</span>
                            </span>
                            <span class="text-[9px] uppercase tracking-wider font-black bg-black/15 px-2 py-0.5 rounded-full">
                                {{ $vehicle->roadtax_status['is_expired'] || $vehicle->insurance_status['is_expired'] ? 'Tamat Tempoh' : 'Perlu Tindakan' }}
                            </span>
                        </div>
                    @endif

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

                        <!-- Roadtax & Insurans Expiry Badges -->
                        <div class="pt-2 border-t border-slate-100 space-y-1.5 text-[11px]">
                            <!-- Roadtax Row -->
                            <div class="flex items-center justify-between p-2 rounded-xl {{ $vehicle->roadtax_status['status'] === 'expired' ? 'bg-rose-50 border border-rose-200' : ($vehicle->roadtax_status['status'] === 'expiring' ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-100') }}">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-invoice {{ $vehicle->roadtax_status['text_class'] }} text-xs"></i>
                                    <div>
                                        <span class="text-[9px] uppercase font-bold text-slate-400 block leading-tight">Cukai Jalan (Roadtax)</span>
                                        <span class="font-bold text-slate-800">{{ $vehicle->roadtax_expiry ? $vehicle->roadtax_expiry->format('d/m/Y') : '-' }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black border {{ $vehicle->roadtax_status['badge_class'] }}">
                                    {{ $vehicle->roadtax_status['days_text'] }}
                                </span>
                            </div>

                            <!-- Insurance Row -->
                            <div class="flex items-center justify-between p-2 rounded-xl {{ $vehicle->insurance_status['status'] === 'expired' ? 'bg-rose-50 border border-rose-200' : ($vehicle->insurance_status['status'] === 'expiring' ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-100') }}">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-shield-halved {{ $vehicle->insurance_status['text_class'] }} text-xs"></i>
                                    <div>
                                        <span class="text-[9px] uppercase font-bold text-slate-400 block leading-tight">Insurans Kenderaan</span>
                                        <span class="font-bold text-slate-800">{{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('d/m/Y') : '-' }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black border {{ $vehicle->insurance_status['badge_class'] }}">
                                    {{ $vehicle->insurance_status['days_text'] }}
                                </span>
                            </div>

                            <!-- Servis Info -->
                            <div class="flex items-center justify-between px-2 pt-0.5 text-[11px] text-slate-500">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-wrench text-[10px] text-slate-400"></i>
                                    Servis Seterusnya:
                                </span>
                                <strong class="text-slate-700 font-mono">{{ $vehicle->next_service_mileage ? number_format($vehicle->next_service_mileage) . ' KM' : '-' }}</strong>
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
