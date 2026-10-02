@extends('layouts.app')

@section('title', 'Laporan Isu: ' . $incident->report_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('incidents.index') }}" class="hover:underline font-bold text-asm-600">Laporan Isu</a>
                <span>/</span>
                <span class="font-mono">{{ $incident->report_number }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>{{ $incident->report_number }}</span>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-black border {{ $incident->status_badge['class'] }}">
                    {{ $incident->status_badge['label'] }}
                </span>
            </h1>
        </div>
    </div>

    <!-- Main Incident Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-slate-100">
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Kenderaan Terlibat:</span>
                <span class="font-black text-slate-900 text-sm">{{ $incident->vehicle?->brand }} {{ $incident->vehicle?->model }}</span>
                <div class="font-mono font-bold text-asm-800">{{ $incident->vehicle?->plate_number }}</div>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Pemandu / Pelapor:</span>
                <span class="font-bold text-slate-900 text-sm">{{ $incident->driver?->name ?? $incident->reportedBy?->name }}</span>
                <div class="text-slate-500">{{ $incident->reportedBy?->position ?? 'Kakitangan ASM' }}</div>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Jenis Isu / Kategori:</span>
                <span class="font-black text-slate-800 text-sm">{{ $incident->incident_type }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Tahap Keterukan:</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $incident->severity === 'Kritikal' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $incident->severity }}
                </span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Tarikh & Masa Kejadian:</span>
                <span class="font-bold text-slate-800">{{ $incident->incident_date->format('d/m/Y') }} jam {{ $incident->incident_time }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Lokasi:</span>
                <span class="font-bold text-slate-800">{{ $incident->location }}</span>
            </div>
        </div>

        <!-- Description -->
        <div class="space-y-1">
            <span class="text-slate-400 font-bold uppercase text-[10px] block">Keterangan Penuh Kejadian / Kerosakan:</span>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed font-medium">
                {{ $incident->description }}
            </div>
        </div>

        @if($incident->photo_path)
            <div class="space-y-2">
                <span class="text-slate-400 font-bold uppercase text-[10px] block">Lampiran Gambar / Bukti:</span>
                <div class="rounded-2xl overflow-hidden border border-slate-200 max-w-sm">
                    <img src="/storage/{{ $incident->photo_path }}" alt="Bukti Kerosakan" class="w-full h-auto object-cover">
                </div>
            </div>
        @endif

        <!-- UPF Action & Status Update Panel (For UPF & Admin) -->
        @if(auth()->user()->isUpf() || auth()->user()->isAdmin())
            <div class="p-5 rounded-2xl bg-slate-900 text-white space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-wrench"></i> TINDAKAN PENGURUSAN FASILITI (UPF)
                </h3>
                <form method="POST" action="{{ route('incidents.update-status', $incident->id) }}" class="space-y-4 text-xs">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-300 uppercase mb-1">Kemaskini Status Laporan</label>
                            <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white font-bold">
                                <option value="Reported" {{ $incident->status === 'Reported' ? 'selected' : '' }}>Reported (Dilaporkan)</option>
                                <option value="Under Review" {{ $incident->status === 'Under Review' ? 'selected' : '' }}>Under Review (Dalam Semakan UPF)</option>
                                <option value="Under Repair" {{ $incident->status === 'Under Repair' ? 'selected' : '' }}>Under Repair (Sedang Dibaiki - Auto Set Kereta Maintenance)</option>
                                <option value="Resolved" {{ $incident->status === 'Resolved' ? 'selected' : '' }}>Resolved (Selesai - Auto Set Kereta Available)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-300 uppercase mb-1">Kos Pembaikan Sebenar / Anggaran (RM)</label>
                            <input type="number" step="0.01" name="cost" value="{{ old('cost', $incident->cost) }}" placeholder="Cth: 850.00"
                                class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white font-mono">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-300 uppercase mb-1">Catatan Tindakan UPF / Maklumat Bengkel</label>
                            <textarea name="upf_action_notes" rows="2" placeholder="Cth: Telah dihantar ke Pusat Servis Toyota Cheras untuk penggantian pad brek..."
                                class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white">{{ old('upf_action_notes', $incident->upf_action_notes) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow transition">
                            Simpan Tindakan UPF
                        </button>
                    </div>
                </form>
            </div>
        @else
            @if($incident->upf_action_notes)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Tindakan UPF:</span>
                    <p class="font-medium text-slate-800">{{ $incident->upf_action_notes }}</p>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
