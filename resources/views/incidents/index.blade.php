@extends('layouts.app')

@section('title', 'Laporan Kerosakan & Kemalangan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Pengurusan Kerosakan & Kemalangan Kenderaan
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                LAPORAN KEROSAKAN & KEMALANGAN
            </h1>
            <p class="text-xs text-slate-500">
                Pemantauan isu mekanikal kenderaan, kemalangan jalan raya, dan tindakan penyelenggaraan UPF
            </p>
        </div>

        <a href="{{ route('incidents.create') }}" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 self-start sm:self-auto">
            <i class="fa-solid fa-circle-plus"></i> + Lapor Isu Baharu
        </a>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Laporan</div>
            <div class="text-xl font-black text-slate-900 mt-0.5">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Menunggu Tindakan</div>
            <div class="text-xl font-black text-amber-600 mt-0.5">{{ $stats['reported'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Sedang Dibaiki</div>
            <div class="text-xl font-black text-purple-600 mt-0.5">{{ $stats['under_repair'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Selesai Dibaik Pulih</div>
            <div class="text-xl font-black text-emerald-600 mt-0.5">{{ $stats['resolved'] }}</div>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-100/75 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">No. Laporan</th>
                        <th class="p-3">Tarikh & Masa</th>
                        <th class="p-3">Kenderaan</th>
                        <th class="p-3">Jenis & Keterukan</th>
                        <th class="p-3">Keterangan Isu & Lokasi</th>
                        <th class="p-3">Pelapor</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($incidents as $inc)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 whitespace-nowrap font-bold text-rose-700">
                                <a href="{{ route('incidents.show', $inc->id) }}" class="hover:underline">
                                    {{ $inc->report_number }}
                                </a>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $inc->incident_date->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $inc->incident_time }}</div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $inc->vehicle?->brand }} {{ $inc->vehicle?->model }}</div>
                                <div class="font-mono text-[10px] text-slate-500 font-bold">{{ $inc->vehicle?->plate_number }}</div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-bold text-slate-900">{{ $inc->incident_type }}</span>
                                <div>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold {{ $inc->severity === 'Kritikal' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $inc->severity }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-3 max-w-xs">
                                <div class="font-medium text-slate-900 line-clamp-1">{{ $inc->description }}</div>
                                <div class="text-[10px] text-slate-400 truncate">
                                    <i class="fa-solid fa-location-dot text-rose-500 mr-0.5"></i>{{ $inc->location }}
                                </div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-medium text-slate-800">{{ $inc->reportedBy?->name ?? 'Pemandu' }}</span>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $inc->status_badge['class'] }}">
                                    {{ $inc->status_badge['label'] }}
                                </span>
                            </td>
                            <td class="p-3 whitespace-nowrap text-right">
                                <a href="{{ route('incidents.show', $inc->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-asm-100 text-slate-700 hover:text-asm-800 font-bold transition">
                                    Butiran & Tindakan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-circle-check text-3xl mb-2 text-emerald-500"></i>
                                <div>Tiada laporan kerosakan atau kemalangan aktif.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
