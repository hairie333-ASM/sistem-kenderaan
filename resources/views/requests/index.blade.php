@extends('layouts.app')

@section('title', 'Senarai Permohonan Kenderaan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-2">
                <span>SENARAI PERMOHONAN KENDERAAN</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Semua rekod permohonan penggunaan kenderaan pejabat Unit Pengurusan Fasiliti (UPF)
            </p>
        </div>
        <a href="{{ route('requests.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white text-xs font-bold shadow-md transition self-start sm:self-auto">
            <i class="fa-solid fa-circle-plus mr-1.5"></i> Mohon Kenderaan Baru
        </a>
    </div>

    <!-- Filters & Search Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-3">
        <!-- Quick Status Pills -->
        <div class="flex flex-wrap items-center gap-2 text-xs border-b border-slate-100 pb-3">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status:</span>
            <a href="{{ route('requests.index') }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ !request()->filled('status') ? 'bg-asm-950 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('requests.index', ['status' => 'pending']) }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ request('status') === 'pending' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Menunggu Semakan
            </a>
            <a href="{{ route('requests.index', ['status' => 'active']) }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ request('status') === 'active' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Aktif / Berjadual
            </a>
            <a href="{{ route('requests.index', ['status' => 'completed']) }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ request('status') === 'completed' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Selesai
            </a>
            <a href="{{ route('requests.index', ['status' => 'cancelled']) }}" class="px-2.5 py-1 rounded-lg font-bold transition {{ request('status') === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Dibatalkan
            </a>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('requests.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Permohonan, Pemohon, Tujuan, Lokasi..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600 focus:border-asm-600">
            </div>
            <div>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600 focus:border-asm-600">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold transition text-center">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Tapis
                </button>
                <a href="{{ route('requests.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Requests Table & Mobile Cards -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-100/75 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">No. Permohonan</th>
                        <th class="p-3">Pegawai Pemohon</th>
                        <th class="p-3">Tarikh & Waktu</th>
                        <th class="p-3">Tujuan & Destinasi</th>
                        <th class="p-3">Pemandu</th>
                        <th class="p-3">Kenderaan</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 whitespace-nowrap font-bold text-asm-800">
                                <a href="{{ route('requests.show', $req->id) }}" class="hover:underline">
                                    {{ $req->request_number }}
                                </a>
                                @if($req->is_short_notice)
                                    <span class="block text-[9px] font-bold text-amber-700 mt-0.5" title="Notis singkat (kurang 1 hari)">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Notis Singkat
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $req->applicant_name }}</div>
                                <div class="text-[10px] text-slate-500">{{ $req->applicant_department }}</div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $req->start_date->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-500">
                                    {{ \Carbon\Carbon::parse($req->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($req->end_time)->format('h:i A') }}
                                </div>
                            </td>
                            <td class="p-3 max-w-xs">
                                <div class="font-bold text-slate-900 line-clamp-1">{{ $req->purpose }}</div>
                                <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                    <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i> {{ $req->destination }}
                                </div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                @if($req->driver)
                                    <span class="font-bold text-slate-900 flex items-center">
                                        <i class="fa-solid fa-id-card text-emerald-600 mr-1 text-xs"></i>
                                        {{ $req->driver->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Belum Ditetapkan</span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                @if($req->vehicle)
                                    <span class="font-bold text-slate-900">{{ $req->vehicle->model }}</span>
                                    <div class="text-[10px] font-mono text-slate-500">{{ $req->vehicle->plate_number }}</div>
                                @else
                                    <span class="text-slate-400 italic">Belum Ditetapkan</span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $req->status_badge['class'] }}">
                                    {{ $req->status_badge['label'] }}
                                </span>
                            </td>
                            <td class="p-3 whitespace-nowrap text-right space-x-1">
                                <a href="{{ route('requests.show', $req->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-asm-50 text-slate-700 hover:text-asm-800 transition" title="Lihat Butiran">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                @if(auth()->user()->isUpf() && in_array($req->status, ['submitted', 'under_review', 'approved']))
                                    <a href="{{ route('upf.assign.show', $req->id) }}" class="p-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold transition" title="Tetapkan Pemandu & Kenderaan">
                                        <i class="fa-solid fa-user-plus text-xs"></i>
                                    </a>
                                @endif
                                <a href="{{ route('requests.print', $req->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Cetak Borang A4">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300"></i>
                                <div>Tiada permohonan dijumpai mengikut kriteria carian.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View (Visible on small screens) -->
        <div class="block sm:hidden divide-y divide-slate-100">
            @forelse($requests as $req)
                <div class="p-4 space-y-2.5 hover:bg-slate-50 transition">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('requests.show', $req->id) }}" class="font-bold text-asm-800 text-xs hover:underline flex items-center gap-1.5">
                            <span class="font-mono">{{ $req->request_number }}</span>
                            @if($req->is_short_notice)
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    Notis Singkat
                                </span>
                            @endif
                        </a>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $req->status_badge['class'] }}">
                            {{ $req->status_badge['label'] }}
                        </span>
                    </div>

                    <div class="text-xs">
                        <div class="font-bold text-slate-900 leading-snug">{{ $req->purpose }}</div>
                        <div class="text-slate-500 text-[11px] flex items-center gap-1 mt-1">
                            <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                            <span>{{ $req->destination }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[11px] bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[9px] uppercase font-bold">Tarikh & Waktu</span>
                            <span class="font-bold text-slate-800">{{ $req->start_date->format('d/m/Y') }}</span>
                            <span class="text-slate-500 block text-[10px]">{{ \Carbon\Carbon::parse($req->start_time)->format('h:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[9px] uppercase font-bold">Pemandu / Kereta</span>
                            <span class="font-bold text-slate-800">{{ $req->driver?->name ?? 'Pandu Sendiri / Belum' }}</span>
                            <span class="text-slate-500 block text-[10px] font-mono">{{ $req->vehicle?->plate_number ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[10px] text-slate-400 truncate max-w-[150px]">Pemohon: <strong class="text-slate-700">{{ $req->applicant_name }}</strong></span>
                        <div class="flex items-center gap-1.5">
                            @if(auth()->user()->isUpf() && in_array($req->status, ['submitted', 'under_review', 'approved']))
                                <a href="{{ route('upf.assign.show', $req->id) }}" class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs">
                                    <i class="fa-solid fa-user-plus mr-0.5"></i> Tugasan
                                </a>
                            @endif
                            <a href="{{ route('requests.show', $req->id) }}" class="px-2.5 py-1 rounded-lg bg-asm-950 hover:bg-asm-900 text-white font-bold text-xs">
                                Butiran
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 text-xs">
                    Tiada permohonan dijumpai.
                </div>
            @endforelse
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
