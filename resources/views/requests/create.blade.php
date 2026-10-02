@extends('layouts.app')

@section('title', 'Borang Permohonan Kenderaan Pejabat')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="requestForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('requests.index') }}" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Senarai
            </a>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-2">
                <span>BORANG PERMOHONAN PENGGUNAAN KENDERAAN PEJABAT</span>
            </h1>
            <p class="text-xs text-slate-500">
                Rujukan Format: ASM/UPFIT/PERMOHONAN KENDERAAN PEJABAT (BAHAGIAN A)
            </p>
        </div>
    </div>

    <!-- Notice Warning Alert (Dynamic) -->
    <div x-show="isShortNotice" x-cloak class="p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-900 shadow-sm flex items-start space-x-3">
        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-xl mt-0.5 shrink-0"></i>
        <div class="text-xs">
            <div class="font-extrabold text-sm uppercase">Peringatan Notis Singkat</div>
            <p class="mt-1" x-text="warningMessage || 'Permohonan kurang daripada tempoh minimum yang ditetapkan (1 hari). Sila semak dengan UPF.'"></p>
            <p class="text-[11px] text-amber-700 mt-1 italic">
                * Permohonan masih boleh dihantar, namun tertakluk kepada budi bicara dan kelulusan khas UPF.
            </p>
        </div>
    </div>

    <!-- Main Form Grid -->
    <form method="POST" action="{{ route('requests.store') }}" class="space-y-6">
        @csrf

        <!-- Section A: Maklumat Pemohon -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">
                        BAHAGIAN A: MAKLUMAT PEMOHON
                    </h2>
                    <p class="text-[11px] text-slate-500">Maklumat auto-diisi berdasarkan profil akaun anda</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                    Langkah 1 dari 3
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Pemohon</label>
                    <input type="text" name="applicant_name" value="{{ old('applicant_name', $user->name) }}" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600 bg-slate-50">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jawatan</label>
                    <input type="text" name="applicant_position" value="{{ old('applicant_position', $user->position ?: 'Pegawai ASM') }}" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600 bg-slate-50">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Bahagian / Unit</label>
                    <input type="text" name="applicant_department" value="{{ old('applicant_department', $user->department ?: 'Akademi Sains Malaysia') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600 bg-slate-50">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">No. Telefon Pemohon</label>
                    <input type="text" name="applicant_phone" value="{{ old('applicant_phone', $user->phone ?: '012-3456789') }}" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Emel Rasmi</label>
                    <input type="email" name="applicant_email" value="{{ old('applicant_email', $user->email) }}" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600 bg-slate-50">
                </div>
            </div>
        </div>

        <!-- Section B: Maklumat Perjalanan & Masa -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">
                        MAKLUMAT PERJALANAN & KEPERLUAN MASA
                    </h2>
                    <p class="text-[11px] text-slate-500">Nyatakan tarikh, tempoh dan destinasi penggunaan</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                    Langkah 2 dari 3
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <!-- Tarikh Mula -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tarikh Diperlukan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="start_date" x-model="startDate" @change="checkAvailability()" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                </div>

                <!-- Masa Mula -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Masa Diperlukan (Ambil) <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="start_time" x-model="startTime" @change="checkAvailability()" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                </div>

                <!-- Tarikh Tamat -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Sehingga Bila (Tarikh Tamat) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="end_date" x-model="endDate" @change="checkAvailability()" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                </div>

                <!-- Masa Tamat -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Masa Tamat (Jangka Pulang) <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="end_time" x-model="endTime" @change="checkAvailability()" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                </div>

                <!-- Masa Tiba di Destinasi -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Jangka Masa Tiba di Destinasi
                    </label>
                    <input type="time" name="arrival_time" value="{{ old('arrival_time', '09:00') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                </div>

                <!-- Lokasi Ambil -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Lokasi Ambil (Pickup Point) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="origin" value="{{ old('origin', 'Pejabat ASM, Menara MATRADE') }}" required
                        placeholder="Cth: Pejabat ASM / Rumah President"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                </div>

                <!-- Lokasi / Destinasi -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Lokasi / Destinasi Urusan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="destination" value="{{ old('destination') }}" required
                        placeholder="Cth: Pejabat Perdana Menteri, Putrajaya / Pusat Konvensyen Sime Darby"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                </div>

                <!-- Tujuan -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tujuan Penggunaan Kenderaan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="purpose" rows="3" required placeholder="Nyatakan mesyuarat, program atau tujuan rasmi secara spesifik..."
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">{{ old('purpose') }}</textarea>
                </div>

                <!-- Pegawai Lain yang Turut Serta -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Pegawai Lain yang Turut Serta (Penumpang)
                    </label>
                    <textarea name="other_passengers" rows="2" placeholder="Senaraikan nama pegawai lain dan jawatan (jika ada)..."
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">{{ old('other_passengers') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Section C: Keperluan & Kelengkapan -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">
                        KEPERLUAN KHIDMAT PEMANDU & KELENGKAPAN
                    </h2>
                    <p class="text-[11px] text-slate-500">Pilih sama ada memerlukan pemandu UPF atau memandu sendiri kenderaan jabatan</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                    Langkah 3 dari 3
                </span>
            </div>

            <!-- Pilihan Mod Pemanduan: Perlukan Pemandu vs Pandu Sendiri -->
            <div class="space-y-2">
                <label class="block font-bold text-slate-800 uppercase tracking-wider text-xs">
                    Pilihan Mod Pemanduan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Option 1: Perlukan Pemandu UPF -->
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none"
                           :class="needDriver == 1 ? 'border-asm-600 bg-asm-50/50 ring-2 ring-asm-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="need_driver" value="1" x-model="needDriver" class="sr-only">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm"
                                     :class="needDriver == 1 ? 'bg-asm-600 text-white' : 'bg-slate-100 text-slate-500'">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <span class="font-extrabold text-sm text-slate-900">Perkhidmatan Pemandu UPF</span>
                            </div>
                            <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                  :class="needDriver == 1 ? 'border-asm-600' : 'border-slate-300'">
                                <span class="w-2 h-2 rounded-full bg-asm-600" x-show="needDriver == 1"></span>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 pl-10 leading-relaxed">
                            Pemandu jabatan UPF akan ditugaskan untuk menghantar pegawai ke destinasi mengikut jadual tugasan rasmi.
                        </p>
                    </label>

                    <!-- Option 2: Pandu Sendiri (Tanpa Pemandu) -->
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none"
                           :class="needDriver == 0 ? 'border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="need_driver" value="0" x-model="needDriver" class="sr-only">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm"
                                     :class="needDriver == 0 ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500'">
                                    <i class="fa-solid fa-car-side"></i>
                                </div>
                                <span class="font-extrabold text-sm text-slate-900">Pandu Sendiri (Tanpa Pemandu)</span>
                            </div>
                            <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                  :class="needDriver == 0 ? 'border-emerald-600' : 'border-slate-300'">
                                <span class="w-2 h-2 rounded-full bg-emerald-600" x-show="needDriver == 0"></span>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 pl-10 leading-relaxed">
                            Pemohon / pegawai sendiri akan memandu kenderaan pejabat (memiliki lesen memandu sah). Sesuai untuk pergerakan fleksibel.
                        </p>
                    </label>
                </div>

                <!-- Self Drive Information Banner -->
                <div x-show="needDriver == 0" x-cloak class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1.5 mt-2 text-xs">
                    <div class="flex items-center gap-2 font-bold text-xs">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Makluman Permohonan Pandu Sendiri</span>
                    </div>
                    <p class="text-[11px] text-emerald-800 leading-relaxed">
                        Anda memilih untuk <strong>memandu sendiri kenderaan jabatan</strong> tanpa memerlukan pemandu UPF. Pegawai yang memandu disyaratkan memiliki lesen memandu sah (Kelas D/DA). Proses serahan kenderaan dan bacaan meter awal/akhir akan dilakukan terus bersama pihak UPF.
                    </p>
                </div>
            </div>

            <!-- Kelengkapan Tambahan -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <label class="block font-bold text-slate-800 uppercase tracking-wider text-xs">
                    Kelengkapan Kenderaan Diperlukan
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <!-- Smart Tag -->
                    <label class="p-3 rounded-xl border border-slate-200 hover:border-asm-500 cursor-pointer transition flex items-center space-x-2 bg-slate-50">
                        <input type="checkbox" name="need_smart_tag" value="1" checked class="rounded border-slate-300 text-asm-600 focus:ring-asm-500 w-4 h-4">
                        <div>
                            <span class="font-bold text-slate-800 block">Smart Tag</span>
                            <span class="text-[10px] text-slate-400">Laluan tol lebuh raya</span>
                        </div>
                    </label>

                    <!-- Kad Inden Petrol -->
                    <label class="p-3 rounded-xl border border-slate-200 hover:border-asm-500 cursor-pointer transition flex items-center space-x-2 bg-slate-50">
                        <input type="checkbox" name="need_fuel_card" value="1" class="rounded border-slate-300 text-asm-600 focus:ring-asm-500 w-4 h-4">
                        <div>
                            <span class="font-bold text-slate-800 block">Kad Inden Petrol</span>
                            <span class="text-[10px] text-slate-400">Pengisian bahan api</span>
                        </div>
                    </label>

                    <!-- GPS -->
                    <label class="p-3 rounded-xl border border-slate-200 hover:border-asm-500 cursor-pointer transition flex items-center space-x-2 bg-slate-50">
                        <input type="checkbox" name="need_gps" value="1" class="rounded border-slate-300 text-asm-600 focus:ring-asm-500 w-4 h-4">
                        <div>
                            <span class="font-bold text-slate-800 block">GPS Navigation</span>
                            <span class="text-[10px] text-slate-400">Peranti pandu arah</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Catatan Tambahan -->
            <div class="text-xs pt-2">
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Tambahan (Pilihan)</label>
                <input type="text" name="applicant_remarks" placeholder="Cth: Pegawai memandu sendiri / perlukan kenderaan berkapasiti muatan tinggi"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
            </div>
        </div>

        <!-- Live Availability Preview Drawer (Section 8 requirement) -->
        <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-satellite-dish"></i> SEMAKAN KETERSEDIAAN MASA NYATA (LIVE AVAILABILITY)
                    </h3>
                    <p class="text-[11px] text-slate-300">
                        Status kenderaan dan pemandu bagi slot masa yang dipilih
                    </p>
                </div>
                <button type="button" @click="checkAvailability()" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-xs text-white transition flex items-center gap-1">
                    <i class="fa-solid fa-arrows-rotate" :class="{ 'animate-spin': loading }"></i> Kemaskini
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Driver Availability -->
                <div class="bg-white/5 rounded-xl p-3 border border-white/10 space-y-2">
                    <div class="text-[10px] font-bold uppercase text-slate-400">DRIVER AVAILABILITY</div>
                    <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                        <template x-for="d in drivers" :key="d.id">
                            <div class="flex items-center justify-between p-1.5 rounded bg-white/5 text-[11px]">
                                <span class="font-medium text-slate-200" x-text="d.name"></span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                    :class="d.is_available ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30'"
                                    x-text="d.is_available ? 'Available' : (d.conflict_with ? 'Bertindih' : d.status)">
                                </span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Vehicle Availability -->
                <div class="bg-white/5 rounded-xl p-3 border border-white/10 space-y-2">
                    <div class="text-[10px] font-bold uppercase text-slate-400">VEHICLE AVAILABILITY</div>
                    <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                        <template x-for="v in vehicles" :key="v.id">
                            <div class="flex items-center justify-between p-1.5 rounded bg-white/5 text-[11px]">
                                <div>
                                    <span class="font-bold text-slate-200" x-text="v.plate_number"></span>
                                    <span class="text-slate-400 text-[10px] ml-1" x-text="v.name"></span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                    :class="v.is_available ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30'"
                                    x-text="v.is_available ? 'Available' : (v.conflict_with ? 'Bertindih' : v.status)">
                                </span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="text-[10px] text-slate-400 italic">
                * Peringatan: Availability hanya memaparkan status ketersediaan awal. Pegawai UPF berkuasa penuh menetapkan pemandu dan kenderaan yang paling sesuai mengikut keutamaan urusan.
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('requests.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black text-xs shadow-lg hover:shadow-xl transition flex items-center space-x-2">
                <span>Hantar Permohonan ke UPF</span>
                <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function requestForm() {
        return {
            startDate: "{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}",
            startTime: '08:30',
            endDate: "{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}",
            endTime: '13:00',
            needDriver: "{{ old('need_driver', '1') }}",
            isShortNotice: false,
            warningMessage: null,
            loading: false,
            drivers: {!! json_encode($initialDrivers ?? []) !!},
            vehicles: {!! json_encode($initialVehicles ?? []) !!},

            init() {
                this.checkAvailability();
            },

            async checkAvailability() {
                if (!this.startDate || !this.startTime || !this.endTime) return;
                this.loading = true;

                try {
                    const response = await fetch('{{ route('requests.check-availability') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            start_date: this.startDate,
                            start_time: this.startTime,
                            end_date: this.endDate || this.startDate,
                            end_time: this.endTime,
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.isShortNotice = data.is_short_notice;
                        this.warningMessage = data.warning_message;
                        this.drivers = data.drivers;
                        this.vehicles = data.vehicles;
                    }
                } catch (e) {
                    console.error('Availability check failed:', e);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endpush
@endsection
