@extends('layouts.app')

@section('title', 'Master Data Pemandu')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-id-badge mr-1"></i> Pengurusan Pemandu UPF
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                MASTER DATA PEMANDU
            </h1>
            <p class="text-xs text-slate-500">
                Senarai pemandu kenderaan rasmi Akademi Sains Malaysia (ASM)
            </p>
        </div>

        @if(auth()->user()->isUpf())
            <a href="{{ route('drivers.create') }}" class="px-4 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 self-start sm:self-auto">
                <i class="fa-solid fa-circle-plus"></i> + Tambah Pemandu Baharu
            </a>
        @endif
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Pemandu</div>
            <div class="text-xl font-black text-slate-900 mt-0.5">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Boleh Bertugas</div>
            <div class="text-xl font-black text-emerald-600 mt-0.5">{{ $stats['available'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Sedang Bertugas</div>
            <div class="text-xl font-black text-blue-600 mt-0.5">{{ $stats['assigned'] }}</div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Bercuti / Off Duty</div>
            <div class="text-xl font-black text-amber-600 mt-0.5">{{ $stats['on_leave'] + $stats['off_duty'] }}</div>
        </div>
    </div>

    <!-- Drivers Grid Cards (Section 5) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($drivers as $driver)
            <div class="bg-white rounded-3xl border border-slate-200 hover:border-emerald-300 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col justify-between group">
                <div class="p-5 space-y-4">
                    <!-- Avatar & Status -->
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-lg shadow-inner">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $driver->status_badge['class'] }}">
                            {{ $driver->status_badge['label'] }}
                        </span>
                    </div>

                    <!-- Driver Identity -->
                    <div>
                        <div class="text-[10px] font-mono text-slate-400 uppercase">{{ $driver->driver_code }}</div>
                        <h2 class="text-base font-black text-slate-900 leading-snug">{{ $driver->name }}</h2>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $driver->position }}</div>
                    </div>

                    <!-- Contact & License -->
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Telefon:</span>
                            <a href="tel:{{ $driver->phone }}" class="font-bold text-emerald-700 hover:underline">
                                <i class="fa-solid fa-phone text-[10px] mr-1"></i>{{ $driver->phone }}
                            </a>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Kelas Lesen:</span>
                            <span class="font-bold text-slate-800">{{ $driver->license_class }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Tamat Lesen:</span>
                            <span class="font-bold text-slate-800">{{ $driver->license_expiry ? $driver->license_expiry->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>

                    @if($driver->notes)
                        <div class="text-[11px] text-slate-600 bg-amber-50/60 p-2.5 rounded-xl border border-amber-200 italic line-clamp-2">
                            {{ $driver->notes }}
                        </div>
                    @endif
                </div>

                <!-- Footer Card Actions -->
                <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2 text-xs">
                    <a href="{{ route('drivers.show', $driver->id) }}" class="flex-1 py-1.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-center transition">
                        Profil Pemandu
                    </a>
                    @if(auth()->user()->isUpf())
                        <a href="{{ route('drivers.edit', $driver->id) }}" class="p-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 transition" title="Kemaskini">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-4 p-12 text-center text-slate-400 bg-white rounded-3xl border border-slate-200">
                <i class="fa-solid fa-id-badge text-4xl mb-2 text-slate-300"></i>
                <div class="text-sm font-bold text-slate-700">Tiada rekod pemandu didaftarkan.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
