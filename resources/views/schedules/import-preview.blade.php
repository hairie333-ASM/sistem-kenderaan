@extends('layouts.app')

@section('title', 'Pratonton Import Jadual')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('schedules.import') }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
                <i class="fa-solid fa-arrow-left mr-1"></i> Muat Naik Semula
            </a>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-2">
                <span>IMPORT PREVIEW</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold border border-emerald-300">
                    {{ count($rows) }} Rekod Dikesan
                </span>
            </h1>
            <p class="text-xs text-slate-500">
                Sila semak padanan pemandu dan kenderaan sebelum mengesahkan import ke pangkalan data
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('schedules.import') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                [Batal]
            </a>
            <form method="POST" action="{{ route('schedules.import.process') }}">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check-double"></i> [Sahkan & Import Masuk]
                </button>
            </form>
        </div>
    </div>

    <!-- Warnings Box if any -->
    @if(!empty($warnings))
        <div class="p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-900 shadow-sm space-y-2">
            <div class="font-bold text-xs uppercase flex items-center gap-1.5">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base"></i>
                Peringatan Ketidakpadanan Data ({{ count($warnings) }} Amaran)
            </div>
            <div class="text-[11px] text-amber-800 max-h-32 overflow-y-auto space-y-0.5 font-mono">
                @foreach($warnings as $w)
                    <div>• {{ $w }}</div>
                @endforeach
            </div>
            <div class="text-[10px] text-amber-700 italic">
                * Rekod yang tidak mempunyai padanan pemandu/kenderaan akan tetap diimport sebagai draf untuk ditetapkan kemudian oleh UPF.
            </div>
        </div>
    @endif

    <!-- Preview Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-3 bg-slate-100 border-b border-slate-200 font-bold text-xs text-slate-700 uppercase">
            Senarai Rekod Jadual Untuk Diimport
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-2.5">Baris</th>
                        <th class="p-2.5">Tarikh & Masa</th>
                        <th class="p-2.5">Tugasan</th>
                        <th class="p-2.5">Lokasi</th>
                        <th class="p-2.5">Pegawai</th>
                        <th class="p-2.5">Padanan Pemandu</th>
                        <th class="p-2.5">Padanan Kenderaan</th>
                        <th class="p-2.5 text-center">Status Padanan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                    @foreach($rows as $r)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-2.5 text-slate-400 font-bold">{{ $r['row_idx'] }}</td>
                            <td class="p-2.5 whitespace-nowrap">
                                <span class="font-bold text-slate-800">{{ $r['date'] }}</span>
                                <div class="text-[10px] text-slate-400">{{ $r['start_time'] }}</div>
                            </td>
                            <td class="p-2.5 font-sans max-w-xs truncate">{{ $r['purpose'] }}</td>
                            <td class="p-2.5 font-sans truncate max-w-xs text-slate-600">{{ $r['location'] }}</td>
                            <td class="p-2.5 font-sans whitespace-nowrap">{{ $r['officer'] }}</td>
                            <td class="p-2.5 font-sans whitespace-nowrap">
                                @if($r['driver_id'])
                                    <span class="text-emerald-700 font-bold">✓ {{ $r['driver_name'] }}</span>
                                @else
                                    <span class="text-amber-600 italic">{{ $r['driver_name'] ?: 'Tiada' }}</span>
                                @endif
                            </td>
                            <td class="p-2.5 font-sans whitespace-nowrap">
                                @if($r['vehicle_id'])
                                    <span class="text-emerald-700 font-bold">✓ {{ $r['vehicle_str'] }}</span>
                                @else
                                    <span class="text-amber-600 italic">{{ $r['vehicle_str'] ?: 'Tiada' }}</span>
                                @endif
                            </td>
                            <td class="p-2.5 text-center whitespace-nowrap font-sans">
                                @if($r['driver_id'] && $r['vehicle_id'])
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Lengkap
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Perlu Semakan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
