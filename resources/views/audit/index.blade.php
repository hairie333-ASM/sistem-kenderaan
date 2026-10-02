@extends('layouts.app')

@section('title', 'Jejak Audit & Log Keselamatan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-900 text-white uppercase tracking-wider mb-2">
                <i class="fa-solid fa-shield-halved mr-1.5 text-emerald-400"></i> Rekod Keselamatan & Pematuhan
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Jejak Audit Sistem (Audit Trail)
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Log semua aktiviti transaksi, permohonan, penugasan UPF, rekod pemandu, dan perubahan tetapan sistem secara automatik.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 text-xs font-mono font-bold border border-slate-200">
                <i class="fa-solid fa-list-check mr-1.5 text-slate-500"></i> Jumlah Log: {{ $logs->total() }}
            </span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('audit.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <!-- Search Text -->
            <div class="sm:col-span-6">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Carian Kata Kunci
                </label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama pengguna, tindakan, atau butiran log..."
                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-xs font-medium bg-slate-50 focus:bg-white transition">
            </div>

            <!-- Module Filter -->
            <div class="sm:col-span-4">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">
                    <i class="fa-solid fa-cubes mr-1"></i> Modul Sistem
                </label>
                <select name="module" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-xs font-bold bg-slate-50 focus:bg-white transition">
                    <option value="">-- Semua Modul --</option>
                    @foreach($modules as $m)
                        <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-filter mr-1.5"></i> Tapis
                </button>
                @if(request()->hasAny(['search', 'module']))
                    <a href="{{ route('audit.index') }}" class="py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-black border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Tarikh & Masa</th>
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Modul</th>
                        <th class="py-3.5 px-4">Tindakan</th>
                        <th class="py-3.5 px-6">Butiran / Ringkasan</th>
                        <th class="py-3.5 px-4 text-right">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Timestamp -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 font-mono">
                                    {{ $log->created_at ? $log->created_at->format('d/m/Y') : '-' }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    {{ $log->created_at ? $log->created_at->format('h:i:s A') : '-' }}
                                </div>
                            </td>

                            <!-- User -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-black">
                                        {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <span>{{ $log->user_name ?? 'Sistem' }}</span>
                                </div>
                                @if($log->user)
                                    <div class="text-[10px] text-slate-400 pl-7">{{ $log->user->role_label }}</div>
                                @endif
                            </td>

                            <!-- Module -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $log->module ?? 'Sistem' }}
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-xl text-[11px] font-black 
                                    @if(str_contains(strtolower($log->action), 'padam') || str_contains(strtolower($log->action), 'tolak') || str_contains(strtolower($log->action), 'batal'))
                                        bg-rose-50 text-rose-700 border border-rose-200
                                    @elseif(str_contains(strtolower($log->action), 'tambah') || str_contains(strtolower($log->action), 'lulus') || str_contains(strtolower($log->action), 'selesai'))
                                        bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif(str_contains(strtolower($log->action), 'tetap') || str_contains(strtolower($log->action), 'ambil') || str_contains(strtolower($log->action), 'pulang'))
                                        bg-indigo-50 text-indigo-700 border border-indigo-200
                                    @else
                                        bg-sky-50 text-sky-700 border border-sky-200
                                    @endif">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <!-- Details -->
                            <td class="py-3.5 px-6">
                                <div class="text-xs text-slate-800 font-medium max-w-xl break-words">
                                    {{ $log->details }}
                                </div>
                            </td>

                            <!-- IP Address -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono text-[11px] text-slate-400">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-2">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>
                                <div class="font-bold text-slate-700 text-sm">Tiada log aktiviti dijumpai</div>
                                <p class="text-xs text-slate-400 mt-1">Tiada rekod yang sepadan dengan kriteria carian anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
