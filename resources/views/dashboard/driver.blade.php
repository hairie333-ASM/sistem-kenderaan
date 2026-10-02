@extends('layouts.app')

@section('title', 'Dashboard Pemandu')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Driver Welcome Banner (Mobile Optimized) -->
    <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-asm-950 rounded-3xl p-5 sm:p-7 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-slate-950 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-id-card mr-1"></i> Dashboard Operasi Pemandu
                </span>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                    SELAMAT DATANG, {{ strtoupper($driver ? $driver->name : auth()->user()->name) }}
                </h1>
                <p class="text-xs text-slate-300 mt-1">
                    No. Kakitangan: <span class="font-mono text-emerald-400 font-bold">{{ $driver?->staff_number ?? 'ASM-P001' }}</span> |
                    Status: <span class="font-bold text-white">{{ $driver?->status ?? 'Boleh Bertugas' }}</span>
                </p>
            </div>
            <div class="hidden sm:block text-right">
                <div class="text-xs text-slate-300">Tarikh Hari Ini</div>
                <div class="text-base font-bold text-amber-400">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</div>
            </div>
        </div>

        <!-- Quick Counters -->
        <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-white/10 text-center">
            <div class="bg-white/5 rounded-xl p-2 backdrop-blur-sm">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Tugasan Hari Ini</div>
                <div class="text-xl font-black text-amber-400">{{ $todayTripsCount }}</div>
            </div>
            <div class="bg-white/5 rounded-xl p-2 backdrop-blur-sm">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Minggu Ini</div>
                <div class="text-xl font-black text-emerald-400">{{ $thisWeekCount }}</div>
            </div>
            <div class="bg-white/5 rounded-xl p-2 backdrop-blur-sm">
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Bulan Ini</div>
                <div class="text-xl font-black text-sky-400">{{ $thisMonthCount }}</div>
            </div>
        </div>
    </div>

    <!-- TUGASAN HARI INI (Matching prompt section 11 & 37) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                TUGASAN HARI INI
            </h2>
            <span class="text-xs font-bold text-slate-500">
                {{ $todayTasks->count() }} Tugasan
            </span>
        </div>

        @forelse($todayTasks as $task)
            <div class="bg-white rounded-3xl border-2 {{ in_array($task->status, ['assigned', 'driver_accepted', 'in_progress']) ? 'border-asm-500 ring-4 ring-asm-50' : 'border-slate-200' }} shadow-md overflow-hidden">
                <!-- Task Header -->
                <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-xl bg-asm-950 text-white font-black text-xs">
                            <i class="fa-regular fa-clock mr-1"></i>
                            {{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }}
                        </span>
                        <span class="text-xs font-bold text-slate-500">{{ $task->request_number }}</span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold border {{ $task->status_badge['class'] }}">
                        {{ strtoupper($task->status_badge['label']) }}
                    </span>
                </div>

                <!-- Task Details Body -->
                <div class="p-5 sm:p-6 space-y-4">
                    <!-- Title & Purpose -->
                    <div>
                        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Tujuan Perjalanan</div>
                        <div class="text-base sm:text-lg font-black text-slate-900 leading-snug mt-0.5">
                            {{ $task->purpose }}
                        </div>
                    </div>

                    <!-- Pickup & Destination Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Pickup Location -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-[10px] font-bold uppercase text-slate-400 flex items-center">
                                <i class="fa-solid fa-circle-dot text-emerald-600 mr-1.5"></i> LOKASI AMBIL (PICKUP)
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900 mt-1">
                                {{ $task->origin }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Masa Ambil: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($task->start_time)->format('h:i A') }}</strong>
                            </div>
                        </div>

                        <!-- Destination Location -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-[10px] font-bold uppercase text-slate-400 flex items-center">
                                <i class="fa-solid fa-location-dot text-rose-600 mr-1.5"></i> DESTINASI
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900 mt-1">
                                {{ $task->destination }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Jangka Tiba: <strong class="text-slate-700">{{ $task->arrival_time ? \Carbon\Carbon::parse($task->arrival_time)->format('h:i A') : '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger & Vehicle Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <!-- Passenger -->
                        <div class="flex items-center space-x-3 p-3 rounded-2xl bg-amber-50/60 border border-amber-200">
                            <div class="w-10 h-10 rounded-xl bg-amber-200 text-amber-900 flex items-center justify-center font-bold text-base shrink-0">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[10px] uppercase font-bold text-amber-800">Pegawai / Penumpang</div>
                                <div class="text-xs font-bold text-slate-900 truncate">{{ $task->other_passengers ?: $task->applicant_name }}</div>
                                <div class="text-[10px] text-slate-500 truncate">{{ $task->applicant_phone }}</div>
                            </div>
                            @if($task->applicant_phone)
                                <a href="tel:{{ $task->applicant_phone }}" class="p-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition" title="Hubungi Pegawai">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                </a>
                            @endif
                        </div>

                        <!-- Assigned Vehicle -->
                        <div class="flex items-center space-x-3 p-3 rounded-2xl bg-blue-50/60 border border-blue-200">
                            <div class="w-10 h-10 rounded-xl bg-blue-200 text-blue-900 flex items-center justify-center font-bold text-base shrink-0">
                                <i class="fa-solid fa-car"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[10px] uppercase font-bold text-blue-800">Kenderaan Ditetapkan</div>
                                <div class="text-xs font-bold text-slate-900 truncate">{{ $task->vehicle?->brand }} {{ $task->vehicle?->model }}</div>
                                <div class="text-[11px] font-mono font-black text-asm-800">{{ $task->vehicle?->plate_number ?? 'Belum Ditugaskan' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Equipment Assigned Checklist Pills -->
                    <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                        <span class="text-slate-400 font-bold text-[10px] uppercase">Kelengkapan:</span>
                        <span class="px-2 py-0.5 rounded-md font-bold text-[10px] {{ $task->assigned_smart_tag ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400 line-through' }}">
                            {{ $task->assigned_smart_tag ? '✓' : '✗' }} Smart Tag
                        </span>
                        <span class="px-2 py-0.5 rounded-md font-bold text-[10px] {{ $task->assigned_fuel_card ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400 line-through' }}">
                            {{ $task->assigned_fuel_card ? '✓' : '✗' }} Kad Inden Petrol
                        </span>
                        <span class="px-2 py-0.5 rounded-md font-bold text-[10px] {{ $task->assigned_gps ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400 line-through' }}">
                            {{ $task->assigned_gps ? '✓' : '✗' }} GPS
                        </span>
                    </div>

                    <!-- BIG ACTION BUTTONS (Prompt requirements) -->
                    <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-2">
                        <a href="{{ route('requests.show', $task->id) }}" class="flex-1 sm:flex-none px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-eye"></i> [LIHAT BUTIRAN]
                        </a>

                        @if($task->status === 'assigned')
                            <form method="POST" action="{{ route('driver.tasks.accept', $task->id) }}" class="flex-1 sm:flex-none">
                                @csrf
                                <button type="submit" class="w-full px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs text-center transition shadow-md flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-check"></i> [TERIMA TUGASAN]
                                </button>
                            </form>
                        @endif

                        @if(in_array($task->status, ['assigned', 'driver_accepted']))
                            <form method="POST" action="{{ route('driver.tasks.start', $task->id) }}" class="flex-1 sm:flex-none">
                                @csrf
                                <button type="submit" class="w-full px-5 py-3 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black text-xs text-center transition shadow-md flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-car"></i> [MULA TUGASAN]
                                </button>
                            </form>
                        @endif

                        @if($task->status === 'in_progress')
                            <form method="POST" action="{{ route('driver.tasks.complete', $task->id) }}" class="flex-1 sm:flex-none">
                                @csrf
                                <button type="submit" class="w-full px-5 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-black text-xs text-center transition shadow-md flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-flag-checkered"></i> [SELESAI TUGASAN]
                                </button>
                            </form>
                        @endif

                        <!-- Handover / Return Record Button if In Progress or Completed -->
                        @if($task->status === 'in_progress' && !$task->handover)
                            <a href="{{ route('handovers.create', $task->id) }}" class="flex-1 sm:flex-none px-4 py-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-key"></i> [REKOD AMBIL / METER]
                            </a>
                        @endif

                        @if(in_array($task->status, ['in_progress', 'completed']) && $task->handover && !$task->returnRecord)
                            <a href="{{ route('handovers.return.create', $task->id) }}" class="flex-1 sm:flex-none px-4 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-rotate-left"></i> [REKOD PULANG]
                            </a>
                        @endif

                        <a href="{{ route('incidents.create', ['request_id' => $task->id, 'vehicle_id' => $task->assigned_vehicle_id]) }}" class="flex-1 sm:flex-none px-4 py-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation"></i> [LAPOR ISU]
                        </a>

                        <a href="{{ route('fuel.create', ['request_id' => $task->id]) }}" class="flex-1 sm:flex-none px-4 py-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-gas-pump"></i> [LOG MINYAK]
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 border border-slate-200 text-center text-slate-400 space-y-2">
                <i class="fa-solid fa-circle-check text-4xl text-emerald-500"></i>
                <div class="text-sm font-bold text-slate-700">Tiada tugasan aktif untuk hari ini.</div>
                <div class="text-xs text-slate-400">Sila semak bahagian Tugasan Akan Datang di bawah atau rujuk jadual mingguan.</div>
            </div>
        @endforelse
    </div>

    <!-- Upcoming Tasks -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                <i class="fa-regular fa-calendar text-asm-600"></i>
                Tugasan Akan Datang Saya
            </h2>
            <a href="{{ route('driver.tasks') }}" class="text-xs font-bold text-asm-600 hover:underline">Semua</a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($upcomingTasks as $upcoming)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center space-x-2 text-xs">
                            <span class="font-bold text-asm-700">{{ $upcoming->start_date->format('d/m/Y') }}</span>
                            <span class="text-slate-400">•</span>
                            <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($upcoming->start_time)->format('h:i A') }}</span>
                        </div>
                        <div class="text-xs font-bold text-slate-900 mt-0.5">{{ $upcoming->purpose }}</div>
                        <div class="text-[11px] text-slate-500">{{ $upcoming->origin }} → {{ $upcoming->destination }}</div>
                        <div class="text-[11px] text-slate-600 mt-1">
                            Kenderaan: <strong class="text-slate-800">{{ $upcoming->vehicle?->plate_number }} ({{ $upcoming->vehicle?->model }})</strong>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $upcoming->status_badge['class'] }}">
                            {{ $upcoming->status_badge['label'] }}
                        </span>
                        <div class="mt-2">
                            <a href="{{ route('requests.show', $upcoming->id) }}" class="text-xs font-bold text-asm-600 hover:underline">Butiran</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-xs text-slate-400">Tiada tugasan akan datang yang berjadual.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
