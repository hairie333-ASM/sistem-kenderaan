@extends('layouts.app')

@section('title', 'Jadual Pemandu Mingguan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-table-cells mr-1"></i> Paparan Matriks Mingguan
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                JADUAL PEMANDU MINGGUAN
            </h1>
            <p class="text-xs text-slate-500">
                Minggu: <strong class="text-slate-800">{{ $startOfWeek->format('d M Y') }}</strong> hingga <strong class="text-slate-800">{{ $endOfWeek->format('d M Y') }}</strong>
            </p>
        </div>

        <!-- Week Navigator -->
        <div class="flex items-center space-x-2">
            <a href="{{ route('schedules.weekly', ['week' => $prevWeek]) }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-sm transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Minggu Lepas
            </a>
            <a href="{{ route('schedules.weekly') }}" class="px-3 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs shadow-sm transition">
                Minggu Ini
            </a>
            <a href="{{ route('schedules.weekly', ['week' => $nextWeek]) }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-sm transition">
                Minggu Depan <i class="fa-solid fa-chevron-right ml-1"></i>
            </a>
        </div>
    </div>

    <!-- Weekly Driver Matrix Table -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200 border-collapse">
                <thead class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3 w-48 border-r border-slate-700">PEMANDU BERTUGAS</th>
                        @foreach($days as $day)
                            <th class="p-3 text-center border-r border-slate-700 min-w-[130px] {{ $day['is_today'] ? 'bg-asm-800 ring-2 ring-amber-400' : '' }}">
                                <div class="font-black">{{ strtoupper($day['day_name']) }}</div>
                                <div class="text-[10px] text-slate-300 font-normal">{{ $day['formatted'] }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($drivers as $driver)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Driver Info Column -->
                            <td class="p-3.5 border-r border-slate-200 bg-slate-50/60 font-bold">
                                <div class="text-sm text-slate-900 flex items-center">
                                    <i class="fa-solid fa-id-card text-emerald-600 mr-2"></i>
                                    {{ $driver->name }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $driver->phone }}</div>
                                <div class="mt-1">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $driver->status_badge['class'] }}">
                                        {{ $driver->status }}
                                    </span>
                                </div>
                            </td>

                            <!-- 7 Day Columns -->
                            @foreach($days as $day)
                                @php
                                    $tasksOnDay = $driver->assignments->filter(function($req) use ($day) {
                                        return $req->start_date->format('Y-m-d') <= $day['date'] && $req->end_date->format('Y-m-d') >= $day['date'];
                                    });
                                @endphp
                                <td class="p-2 border-r border-slate-200 align-top text-center {{ $day['is_today'] ? 'bg-amber-50/30' : '' }}">
                                    @if($tasksOnDay->count() > 0)
                                        <div class="space-y-1.5">
                                            <div class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-900 text-[10px] font-black border border-blue-300">
                                                {{ $tasksOnDay->count() }} Tugasan
                                            </div>
                                            @foreach($tasksOnDay as $t)
                                                <a href="{{ route('requests.show', $t->id) }}" class="block p-1.5 rounded-lg bg-slate-50 hover:bg-asm-50 border border-slate-200 text-left transition group">
                                                    <div class="font-bold text-[10px] text-asm-800 truncate group-hover:text-asm-600">
                                                        {{ \Carbon\Carbon::parse($t->start_time)->format('h:i A') }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-800 line-clamp-1 leading-tight font-medium">
                                                        {{ $t->purpose }}
                                                    </div>
                                                    <div class="text-[9px] text-slate-400 truncate mt-0.5">
                                                        {{ $t->destination }}
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="py-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Available
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
