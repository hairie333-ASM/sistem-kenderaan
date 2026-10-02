@extends('layouts.app')

@section('title', 'Dashboard Pemohon')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-asm-950 via-asm-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="max-w-2xl relative z-10 space-y-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950 uppercase tracking-wider">
                Portal Pemohon Kenderaan Pejabat
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Uruskan permohonan kenderaan rasmi Akademi Sains Malaysia (ASM) dengan mudah dan pantas. Semak status penugasan pemandu, kenderaan, dan muat turun borang permohonan digital.
            </p>
            <div class="pt-2 flex flex-wrap items-center gap-3">
                <a href="{{ route('requests.create') }}" class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-circle-plus"></i> Buat Permohonan Kenderaan Baru
                </a>
                <a href="{{ route('requests.index') }}" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md transition">
                    Lihat Sejarah Permohonan
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl text-white pointer-events-none">
            <i class="fa-solid fa-car-side"></i>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Menunggu Semakan</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $pendingCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Sedang dinilai UPF</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Diluluskan / Ditetapkan</div>
            <div class="text-2xl font-black text-asm-600 mt-1">{{ $approvedCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Pemandu/Kereta siap</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Dalam Perjalanan</div>
            <div class="text-2xl font-black text-purple-600 mt-1">{{ $inProgressCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Tugasan sedang berjalan</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Selesai</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $completedCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Jumlah rekod perjalanan</div>
        </div>
    </div>

    <!-- Permohonan Terkini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-asm-600"></i>
                Permohonan Kenderaan Anda
            </h2>
            <a href="{{ route('requests.index') }}" class="text-xs font-bold text-asm-600 hover:underline">
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-100/75 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">No. Permohonan</th>
                        <th class="p-3">Tarikh & Masa</th>
                        <th class="p-3">Tujuan & Destinasi</th>
                        <th class="p-3">Pemandu Ditugaskan</th>
                        <th class="p-3">Kenderaan</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($myRequests as $req)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-bold text-asm-800 whitespace-nowrap">
                                {{ $req->request_number }}
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $req->start_date->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($req->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($req->end_time)->format('h:i A') }}</div>
                            </td>
                            <td class="p-3 max-w-xs">
                                <div class="font-bold text-slate-900 line-clamp-1">{{ $req->purpose }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                    <span class="truncate">{{ $req->destination }}</span>
                                </div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                @if($req->driver)
                                    <span class="font-bold text-slate-900">{{ $req->driver->name }}</span>
                                    <div class="text-[10px] text-slate-500">{{ $req->driver->phone }}</div>
                                @else
                                    <span class="text-slate-400 italic">Belum Ditetapkan</span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                @if($req->vehicle)
                                    <span class="font-bold text-slate-900">{{ $req->vehicle->brand }} {{ $req->vehicle->model }}</span>
                                    <div class="text-[10px] font-mono text-slate-500">{{ $req->vehicle->plate_number }}</div>
                                @else
                                    <span class="text-slate-400 italic">Belum Ditetapkan</span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $req->status_badge['class'] }}">
                                    {{ $req->status_badge['label'] }}
                                </span>
                            </td>
                            <td class="p-3 whitespace-nowrap text-right space-x-1">
                                <a href="{{ route('requests.show', $req->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-asm-50 text-slate-700 hover:text-asm-800 font-bold transition">
                                    Butiran
                                </a>
                                <a href="{{ route('requests.print', $req->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition" title="Cetak Borang A4">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300"></i>
                                <div>Anda belum membuat sebarang permohonan kenderaan.</div>
                                <div class="mt-3">
                                    <a href="{{ route('requests.create') }}" class="px-4 py-2 rounded-xl bg-asm-950 text-white font-bold text-xs inline-block">
                                        + Hantar Permohonan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
