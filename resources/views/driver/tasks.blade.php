@extends('layouts.app')

@section('title', 'Senarai Tugasan Pemandu')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="{ activeTab: 'today' }">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-asm-950 rounded-3xl p-6 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500 text-slate-950 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-clipboard-list mr-1.5"></i> Tugasan Pemandu UPF
                </span>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                    TUGASAN & JADUAL: {{ strtoupper($driver ? $driver->name : auth()->user()->name) }}
                </h1>
                <p class="text-xs text-slate-300 mt-1">
                    No. Kakitangan: <span class="font-mono text-emerald-400 font-bold">{{ $driver?->staff_number ?? '-' }}</span> |
                    No. Telefon: <span class="font-bold text-white">{{ $driver?->phone ?? '-' }}</span> |
                    Lesen: <span class="font-bold text-amber-300">{{ $driver?->license_class ?? ($driver?->license_type ?? '-') }} {{ $driver?->license_expiry ? '(Tamat: '.\Carbon\Carbon::parse($driver->license_expiry)->format('d/m/Y').')' : '' }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('schedules.calendar') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition flex items-center">
                    <i class="fa-regular fa-calendar-days mr-2"></i> Kalendar Tugasan
                </a>
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black transition flex items-center shadow-lg shadow-emerald-500/30">
                    <i class="fa-solid fa-gauge mr-1.5"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="grid grid-cols-3 gap-2 mt-6 pt-5 border-t border-white/10">
            <button @click="activeTab = 'today'"
                    :class="activeTab === 'today' ? 'bg-white text-slate-950 shadow-md font-black' : 'bg-white/10 text-slate-200 hover:bg-white/15 font-semibold'"
                    class="py-2.5 px-3 rounded-2xl text-xs sm:text-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-calendar-day text-amber-500"></i>
                <span>Hari Ini</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">{{ $todayTasks->count() }}</span>
            </button>
            <button @click="activeTab = 'upcoming'"
                    :class="activeTab === 'upcoming' ? 'bg-white text-slate-950 shadow-md font-black' : 'bg-white/10 text-slate-200 hover:bg-white/15 font-semibold'"
                    class="py-2.5 px-3 rounded-2xl text-xs sm:text-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-calendar-week text-emerald-500"></i>
                <span>Akan Datang</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">{{ $upcomingTasks->count() }}</span>
            </button>
            <button @click="activeTab = 'completed'"
                    :class="activeTab === 'completed' ? 'bg-white text-slate-950 shadow-md font-black' : 'bg-white/10 text-slate-200 hover:bg-white/15 font-semibold'"
                    class="py-2.5 px-3 rounded-2xl text-xs sm:text-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check text-sky-500"></i>
                <span>Selesai</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800">{{ $completedTasks->count() }}</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: TUGASAN HARI INI -->
    <div x-show="activeTab === 'today'" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                Tugasan Perjalanan Hari Ini ({{ \Carbon\Carbon::today()->translatedFormat('l, d M Y') }})
            </h2>
            <span class="text-xs text-slate-500 font-semibold">{{ $todayTasks->count() }} tugasan dijadualkan</span>
        </div>

        @forelse($todayTasks as $task)
            <div class="bg-white rounded-3xl border-2 {{ in_array($task->status, ['assigned', 'driver_accepted', 'in_progress']) ? 'border-amber-400 ring-4 ring-amber-50' : 'border-slate-200' }} shadow-md overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-xl bg-slate-900 text-amber-400 font-black text-xs font-mono">
                            <i class="fa-regular fa-clock mr-1"></i>
                            {{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }}
                        </span>
                        <span class="text-xs font-bold text-slate-600 font-mono">{{ $task->request_number }}</span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold border {{ $task->status_badge['class'] }}">
                        {{ strtoupper($task->status_badge['label']) }}
                    </span>
                </div>

                <div class="p-5 sm:p-6 space-y-4">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Tujuan Perjalanan</div>
                        <div class="text-base sm:text-lg font-black text-slate-900 mt-0.5 leading-snug">
                            {{ $task->purpose }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-[10px] font-bold uppercase text-slate-400 flex items-center">
                                <i class="fa-solid fa-circle-dot text-emerald-600 mr-1.5"></i> Lokasi Ambil (Pickup)
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900 mt-1">
                                {{ $task->origin }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Masa Ambil: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }}</strong>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-[10px] font-bold uppercase text-slate-400 flex items-center">
                                <i class="fa-solid fa-location-dot text-rose-600 mr-1.5"></i> Destinasi
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900 mt-1">
                                {{ $task->destination }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Anggaran Selesai: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($task->end_time)->format('h:i A') }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger & Vehicle info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-base flex-shrink-0">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Pegawai / Pemohon</div>
                                <div class="text-xs font-black text-slate-900">{{ $task->user?->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $task->user?->department ?? 'ASM' }} | {{ $task->user?->phone ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-base flex-shrink-0">
                                <i class="fa-solid fa-car-side"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Kenderaan Ditetapkan</div>
                                <div class="text-xs font-black text-slate-900">
                                    {{ $task->vehicle ? $task->vehicle->brand . ' ' . $task->vehicle->model : 'Belum Ditetapkan' }}
                                </div>
                                <div class="text-[11px] font-mono font-bold text-indigo-600">
                                    {{ $task->vehicle?->plate_number ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center gap-2">
                        @if($task->status === 'assigned')
                            <form action="{{ route('driver.tasks.accept', $task->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition shadow-md flex items-center">
                                    <i class="fa-solid fa-check mr-1.5"></i> Terima Tugasan
                                </button>
                            </form>
                        @elseif($task->status === 'driver_accepted')
                            <form action="{{ route('driver.tasks.start', $task->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs transition shadow-md flex items-center">
                                    <i class="fa-solid fa-play mr-1.5"></i> Mula Perjalanan
                                </button>
                            </form>
                            @if(!$task->handover)
                                <a href="{{ route('handovers.create', $task->id) }}" class="px-4 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition flex items-center">
                                    <i class="fa-solid fa-key mr-1.5"></i> Rekod Ambil Kenderaan
                                </a>
                            @endif
                        @elseif($task->status === 'in_progress')
                            <form action="{{ route('driver.tasks.complete', $task->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs transition shadow-md flex items-center">
                                    <i class="fa-solid fa-flag-checkered mr-1.5"></i> Selesai Tugasan
                                </button>
                            </form>
                            @if(!$task->returnRecord)
                                <a href="{{ route('handovers.return.create', $task->id) }}" class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition flex items-center">
                                    <i class="fa-solid fa-rotate-left mr-1.5"></i> Rekod Pulang & Meter
                                </a>
                            @endif
                        @endif

                        <a href="{{ route('requests.show', $task->id) }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center">
                            <i class="fa-regular fa-eye mr-1.5"></i> Butiran Penuh
                        </a>
                        <a href="{{ route('incidents.create', ['request_id' => $task->id]) }}" class="px-4 py-2.5 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition flex items-center border border-rose-200">
                            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Lapor Isu
                        </a>
                        @if($task->vehicle_id)
                            <a href="{{ route('fuel.create', ['vehicle_id' => $task->vehicle_id, 'request_id' => $task->id]) }}" class="px-4 py-2.5 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs transition flex items-center border border-amber-200">
                                <i class="fa-solid fa-gas-pump mr-1.5"></i> Rekod Minyak
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tiada tugasan dijadualkan hari ini</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Anda tiada sebarang penugasan kenderaan untuk hari ini. Sila semak tab 'Akan Datang' untuk jadual masa hadapan.
                </p>
            </div>
        @endforelse
    </div>

    <!-- TAB 2: TUGASAN AKAN DATANG -->
    <div x-show="activeTab === 'upcoming'" class="space-y-4" style="display: none;">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="fa-regular fa-calendar-days text-emerald-600"></i>
                Jadual Tugasan Masa Hadapan
            </h2>
            <span class="text-xs text-slate-500 font-semibold">{{ $upcomingTasks->count() }} tugasan akan datang</span>
        </div>

        @forelse($upcomingTasks as $task)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-3">
                        <div class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 font-bold text-xs border border-emerald-200">
                            <i class="fa-regular fa-calendar mr-1"></i>
                            {{ \Carbon\Carbon::parse($task->start_date)->translatedFormat('d M Y') }}
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-700">
                            {{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($task->end_time)->format('h:i A') }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono">({{ $task->request_number }})</span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold border {{ $task->status_badge['class'] }}">
                        {{ strtoupper($task->status_badge['label']) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Tujuan & Destinasi</div>
                        <div class="text-sm font-black text-slate-900 mt-0.5">{{ $task->purpose }}</div>
                        <div class="text-xs text-slate-600 mt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-rose-500"></i> {{ $task->origin }} → {{ $task->destination }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Pegawai & Kenderaan</div>
                        <div class="text-xs font-bold text-slate-800 mt-0.5">
                            <i class="fa-regular fa-user text-slate-400 mr-1"></i> {{ $task->user?->name }} ({{ $task->user?->department ?? 'ASM' }})
                        </div>
                        <div class="text-xs font-mono font-bold text-indigo-700 mt-1">
                            <i class="fa-solid fa-car text-slate-400 mr-1"></i> {{ $task->vehicle ? $task->vehicle->brand . ' ' . $task->vehicle->model . ' (' . $task->vehicle->plate_number . ')' : 'Belum ditetapkan' }}
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Keperluan:
                        @if($task->need_smart_tag)<span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-700 mr-1">Smart Tag</span>@endif
                        @if($task->need_fuel_card)<span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-700 mr-1">Kad Petrol</span>@endif
                        @if($task->need_gps)<span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-700">GPS</span>@endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if($task->status === 'assigned')
                            <form action="{{ route('driver.tasks.accept', $task->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition shadow-sm">
                                    <i class="fa-solid fa-check mr-1"></i> Terima Sekarang
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('requests.show', $task->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            Lihat Butiran
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tiada tugasan masa hadapan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Tiada jadual tugasan baharu yang ditugaskan kepada anda pada masa ini.
                </p>
            </div>
        @endforelse
    </div>

    <!-- TAB 3: SEJARAH TUGASAN SELESAI -->
    <div x-show="activeTab === 'completed'" class="space-y-4" style="display: none;">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-sky-600"></i>
                Sejarah Tugasan Selesai (10 Terkini)
            </h2>
            <span class="text-xs text-slate-500 font-semibold">{{ $completedTasks->count() }} rekod</span>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-black border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">No. Permohonan</th>
                            <th class="py-3.5 px-4">Tarikh & Masa</th>
                            <th class="py-3.5 px-4">Tujuan & Destinasi</th>
                            <th class="py-3.5 px-4">Kenderaan</th>
                            <th class="py-3.5 px-4">Jarak (KM)</th>
                            <th class="py-3.5 px-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($completedTasks as $task)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    {{ $task->request_number }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') }}</div>
                                    <div class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $task->purpose }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $task->destination }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $task->vehicle?->model }}</div>
                                    <div class="font-mono text-[11px] text-indigo-600 font-semibold">{{ $task->vehicle?->plate_number ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($task->returnRecord)
                                        <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                                            {{ number_format($task->returnRecord->total_km, 1) }} KM
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('requests.show', $task->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                        Butiran
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Tiada rekod tugasan selesai dijumpai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
