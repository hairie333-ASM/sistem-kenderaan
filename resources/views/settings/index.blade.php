@extends('layouts.app')

@section('title', 'Tetapan Sistem UPF')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-slate-950 uppercase tracking-wider mb-2">
                <i class="fa-solid fa-sliders mr-1.5"></i> Konfigurasi Sistem
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Tetapan Pengurusan Kenderaan
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Urus parameter permohonan, dasar penugasan bertindih, dan maklumat rasmi Unit Pengurusan Fasiliti (UPF).
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                <i class="fa-solid fa-lock text-slate-400 mr-1"></i> Admin & UPF Sahaja
            </span>
        </div>
    </div>

    <!-- Settings Form -->
    <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Policy & Workflow Settings -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-business-time text-emerald-600"></i> Dasar Permohonan & Penugasan
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Tetapkan tempoh notis minimum dan peraturan penugasan jadual.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Minimum Notice Days -->
                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                        Tempoh Notis Minimum (Hari) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="min_notice_days" min="0" max="30"
                               value="{{ old('min_notice_days', $settings['min_notice_days']->value ?? '1') }}"
                               required
                               class="w-full pl-4 pr-12 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-bold bg-slate-50 focus:bg-white transition">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-xs text-slate-400 font-bold">
                            Hari
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed">
                        Permohonan kurang daripada tempoh ini akan memaparkan amaran tempoh singkat kepada pemohon.
                    </p>
                </div>

                <!-- Conflict Override Permission -->
                <div class="flex flex-col justify-between">
                    <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                        Kebenaran Override Konflik
                    </label>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-900">Benarkan Override Pegawai UPF</div>
                            <div class="text-[11px] text-slate-500">Membolehkan UPF meneruskan penugasan walaupun ada amaran pertindihan masa.</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-3">
                            <input type="checkbox" name="allow_conflict_override" value="1" class="sr-only peer"
                                   {{ old('allow_conflict_override', $settings['allow_conflict_override']->value ?? '1') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Organization & Contact Settings -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-sky-600"></i> Profil Organisasi & Pejabat UPF
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Maklumat yang akan dicetak di atas borang permohonan dan surat iringan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Org Name -->
                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                        Nama Organisasi / Agensi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="organization_name"
                           value="{{ old('organization_name', $settings['organization_name']->value ?? 'Akademi Sains Malaysia') }}"
                           required
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-semibold bg-slate-50 focus:bg-white transition">
                </div>

                <!-- Unit Name -->
                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                        Nama Unit Pengendali <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="unit_name"
                           value="{{ old('unit_name', $settings['unit_name']->value ?? 'Unit Pengurusan Fasiliti (UPF)') }}"
                           required
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-semibold bg-slate-50 focus:bg-white transition">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                        No. Telefon UPF <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="upf_phone"
                           value="{{ old('upf_phone', $settings['upf_phone']->value ?? '03-6203 0633') }}"
                           required
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-semibold bg-slate-50 focus:bg-white transition">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                        Emel Rasmi UPF <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="upf_email"
                           value="{{ old('upf_email', $settings['upf_email']->value ?? 'upf@akademisains.gov.my') }}"
                           required
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-semibold bg-slate-50 focus:bg-white transition">
                </div>

                <!-- Address -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-black uppercase text-slate-700 mb-2">
                        Alamat Pejabat <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="office_address" rows="3" required
                              class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm font-medium bg-slate-50 focus:bg-white transition">{{ old('office_address', $settings['office_address']->value ?? 'Tingkat 20, Menara MATRADE, Jalan Sultan Haji Ahmad Shah, 50480 Kuala Lumpur') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-black text-sm transition shadow-xl shadow-slate-900/10 flex items-center">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Tetapan
            </button>
        </div>
    </form>
</div>
@endsection
