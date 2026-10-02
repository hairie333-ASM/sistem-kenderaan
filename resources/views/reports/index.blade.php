@extends('layouts.app')

@section('title', 'Laporan UPF')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-asm-100 text-asm-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-chart-pie mr-1"></i> Pusat Analisis & Laporan UPF
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                LAPORAN PENGGUNAAN KENDERAAN & PEMANDU
            </h1>
            <p class="text-xs text-slate-500">
                Penyata rasmi penggunaan kenderaan pejabat, tugasan pemandu, jarak perjalanan, dan kos bahan api ASM
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </button>
            <a href="{{ route('reports.export', request()->all()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i> Eksport Excel (.CSV)
            </a>
        </div>
    </div>

    <!-- 9 Report Selector Tabs (Prompt Section 27) -->
    <div class="no-print bg-white p-2.5 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap gap-1.5 text-xs">
        <a href="{{ route('reports.index', ['type' => 'vehicle_usage']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $type === 'vehicle_usage' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            1. Penggunaan Kenderaan
        </a>
        <a href="{{ route('reports.index', ['type' => 'driver_tasks']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $type === 'driver_tasks' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            2. Tugasan Pemandu
        </a>
        <a href="{{ route('reports.index', ['type' => 'monthly']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $type === 'monthly' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            3. Analisis Bulanan
        </a>
        <a href="{{ route('reports.index', ['type' => 'mileage']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $type === 'mileage' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            4. Laporan Mileage (KM)
        </a>
        <a href="{{ route('reports.index', ['type' => 'fuel']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $type === 'fuel' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            5. Minyak & Kad Inden
        </a>
        <a href="{{ route('reports.index', ['type' => 'maintenance']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $type === 'maintenance' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            6. Penyelenggaraan
        </a>
        <a href="{{ route('reports.index', ['type' => 'incidents']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $type === 'incidents' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            7. Kerosakan & Kemalangan
        </a>
        <a href="{{ route('reports.index', ['type' => 'cancellations']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $type === 'cancellations' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            8. Pembatalan Permohonan
        </a>
        <a href="{{ route('reports.index', ['type' => 'officers']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ $type === 'officers' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            9. Mengikut Pegawai
        </a>
    </div>

    <!-- Filters Box -->
    <div class="no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-xs">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input type="hidden" name="type" value="{{ $type }}">
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Dari Tarikh</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs">
            </div>
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Hingga Tarikh</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs">
            </div>
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Pilih Kenderaan</label>
                <select name="vehicle_id" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs">
                    <option value="">Semua Kenderaan</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->plate_number }} ({{ $v->model }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-1.5 px-3 rounded-xl bg-slate-900 text-white font-bold transition">
                    Tapis Laporan
                </button>
                <a href="{{ route('reports.index', ['type' => $type]) }}" class="py-1.5 px-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-center transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Report Table Display Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-4">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-slate-900 uppercase">
                    @if($type === 'vehicle_usage') LAPORAN PENGGUNAAN KENDERAAN
                    @elseif($type === 'driver_tasks') LAPORAN TUGASAN PEMANDU
                    @elseif($type === 'monthly') LAPORAN PENGGUNAAN MENGIKUT BULAN
                    @elseif($type === 'mileage') LAPORAN BACAAN METER & MILEAGE (TOTAL KM)
                    @elseif($type === 'fuel') LAPORAN MINYAK & KAD INDEN PETROL
                    @elseif($type === 'maintenance') LAPORAN PENYELENGGARAAN & SERVIS KENDERAAN
                    @elseif($type === 'incidents') LAPORAN KEMALANGAN & KEROSAKAN
                    @elseif($type === 'cancellations') LAPORAN PEMBATALAN PERMOHONAN
                    @elseif($type === 'officers') LAPORAN PENGGUNAAN MENGIKUT PEGAWAI / BAHAGIAN
                    @endif
                </h2>
                <div class="text-xs text-slate-500">Tempoh: {{ $startDate }} hingga {{ $endDate }}</div>
            </div>
            <span class="text-xs font-mono font-bold bg-slate-100 px-2.5 py-1 rounded-lg">
                {{ is_countable($data) ? count($data) : 0 }} Rekod
            </span>
        </div>

        <div class="overflow-x-auto text-xs">
            @if($type === 'vehicle_usage')
                <table class="w-full text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 font-bold uppercase text-[10px] text-slate-600">
                        <tr>
                            <th class="p-2.5">Kenderaan</th>
                            <th class="p-2.5">No. Plat</th>
                            <th class="p-2.5">Jenis</th>
                            <th class="p-2.5">Status</th>
                            <th class="p-2.5 text-center">Jumlah Perjalanan</th>
                            <th class="p-2.5 text-right">Jumlah KM</th>
                            <th class="p-2.5 text-right">Kos Minyak (RM)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                        @foreach($data as $v)
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 font-bold font-sans text-slate-900">{{ $v->brand }} {{ $v->model }}</td>
                                <td class="p-2.5 font-black text-asm-800">{{ $v->plate_number }}</td>
                                <td class="p-2.5 font-sans">{{ $v->type }}</td>
                                <td class="p-2.5 font-sans">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $v->status_badge['class'] }}">{{ $v->status }}</span>
                                </td>
                                <td class="p-2.5 text-center font-bold">{{ $v->requests_count }}</td>
                                <td class="p-2.5 text-right font-black text-emerald-700">{{ number_format($v->total_km) }} KM</td>
                                <td class="p-2.5 text-right font-bold text-slate-900">RM {{ number_format($v->fuel_cost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif($type === 'driver_tasks')
                <table class="w-full text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 font-bold uppercase text-[10px] text-slate-600">
                        <tr>
                            <th class="p-2.5">Nama Pemandu</th>
                            <th class="p-2.5">Jawatan</th>
                            <th class="p-2.5">Telefon</th>
                            <th class="p-2.5">Status</th>
                            <th class="p-2.5 text-center">Jumlah Tugasan</th>
                            <th class="p-2.5 text-center">Tugasan Selesai</th>
                            <th class="p-2.5 text-right">Jumlah KM Dipandu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                        @foreach($data as $d)
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 font-bold font-sans text-slate-900">{{ $d->name }}</td>
                                <td class="p-2.5 font-sans text-slate-600">{{ $d->position }}</td>
                                <td class="p-2.5 font-sans">{{ $d->phone }}</td>
                                <td class="p-2.5 font-sans">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $d->status_badge['class'] }}">{{ $d->status }}</span>
                                </td>
                                <td class="p-2.5 text-center font-bold text-slate-900">{{ $d->assignments_count }}</td>
                                <td class="p-2.5 text-center font-bold text-emerald-700">{{ $d->completed_tasks }}</td>
                                <td class="p-2.5 text-right font-black text-asm-800">{{ number_format($d->total_km) }} KM</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif($type === 'monthly')
                <table class="w-full text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 font-bold uppercase text-[10px] text-slate-600">
                        <tr>
                            <th class="p-2.5">Bulan</th>
                            <th class="p-2.5 text-center">Jumlah Permohonan</th>
                            <th class="p-2.5 text-center">Tugasan Selesai</th>
                            <th class="p-2.5 text-right">Jumlah KM Perjalanan</th>
                            <th class="p-2.5 text-right">Perbelanjaan Minyak (RM)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                        @foreach($data as $m)
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 font-bold font-sans text-slate-900">{{ $m['month_name'] }}</td>
                                <td class="p-2.5 text-center font-bold">{{ $m['requests_count'] }}</td>
                                <td class="p-2.5 text-center font-bold text-emerald-700">{{ $m['completed_count'] }}</td>
                                <td class="p-2.5 text-right font-black text-slate-800">{{ number_format($m['total_km']) }} KM</td>
                                <td class="p-2.5 text-right font-bold text-slate-900">RM {{ number_format($m['fuel_cost'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif($type === 'officers')
                <table class="w-full text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 font-bold uppercase text-[10px] text-slate-600">
                        <tr>
                            <th class="p-2.5">Nama Pegawai Memohon</th>
                            <th class="p-2.5">Bahagian / Unit</th>
                            <th class="p-2.5 text-center">Jumlah Permohonan</th>
                            <th class="p-2.5 text-center">Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans text-xs">
                        @foreach($data as $o)
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 font-bold text-slate-900">{{ $o->applicant_name }}</td>
                                <td class="p-2.5 text-slate-600">{{ $o->applicant_department }}</td>
                                <td class="p-2.5 text-center font-mono font-bold">{{ $o->total_requests }}</td>
                                <td class="p-2.5 text-center font-mono font-bold text-emerald-700">{{ $o->completed_requests }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-slate-400">
                    Sila gunakan penapis di atas untuk menjana maklumat laporan.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
