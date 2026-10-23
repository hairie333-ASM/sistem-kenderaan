<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Log Masuk Microsoft 365 | Sistem Kenderaan ASM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="min-h-screen py-10 px-4 flex flex-col items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-slate-800">

    <div class="max-w-xl w-full space-y-6 my-auto">
        <!-- Microsoft Brand Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center px-4 py-2 bg-white rounded-2xl shadow-lg border border-slate-200 mb-1">
                <!-- Microsoft 4-color grid logo -->
                <div class="grid grid-cols-2 gap-1 w-6 h-6 mr-3">
                    <div class="bg-[#f25022] rounded-sm"></div>
                    <div class="bg-[#7fba00] rounded-sm"></div>
                    <div class="bg-[#00a4ef] rounded-sm"></div>
                    <div class="bg-[#ffb900] rounded-sm"></div>
                </div>
                <span class="text-base font-bold text-slate-800 tracking-tight">Microsoft 365 • Persekitaran Ujian</span>
            </div>
            <h1 class="text-2xl font-black tracking-wide text-white drop-shadow">
                Simulasi Log Masuk Pemohon ASM
            </h1>
            <p class="text-xs text-sky-300">
                Khusus untuk pemohon berakaun rasmi <span class="font-mono font-bold text-amber-300">@akademisains.gov.my</span>
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 border border-slate-200 space-y-6">
            @if(session('info'))
                <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start">
                    <i class="fa-solid fa-triangle-exclamation mr-2 text-amber-600 mt-0.5"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start">
                    <i class="fa-solid fa-circle-exclamation mr-2 text-rose-600 mt-0.5"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <!-- Section 1: Auto-provisioning Test for New Applicant -->
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                <div class="text-xs font-bold text-emerald-900 uppercase tracking-wider mb-1 flex items-center">
                    <i class="fa-solid fa-user-plus text-emerald-600 mr-2"></i>
                    Uji Auto-Pendaftaran Pemohon Baharu (Auto-Provisioning)
                </div>
                <p class="text-[11px] text-emerald-700 mb-3">
                    Staf ASM baharu yang belum mempunyai rekod akan didaftarkan sebagai <strong>Pemohon</strong> secara automatik.
                </p>
                <form method="POST" action="{{ route('auth.m365.mock.submit') }}" class="grid sm:grid-cols-2 gap-2">
                    @csrf
                    <input type="hidden" name="email" value="dr.farah@akademisains.gov.my">
                    <input type="hidden" name="displayName" value="Dr. Farah Hani">
                    <input type="hidden" name="department" value="Bahagian Dasar Sains & Teknologi">
                    <input type="hidden" name="jobTitle" value="Eksekutif Kanan STI">
                    <input type="hidden" name="mobilePhone" value="012-3456789">
                    <button type="submit" class="sm:col-span-2 py-2.5 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Log Masuk sebagai Dr. Farah Hani (Pemohon Baharu)</span>
                    </button>
                </form>
            </div>

            <!-- Section 2: 1-Click for Existing ASM Staff -->
            <div>
                <div class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center">
                    <i class="fa-solid fa-users text-indigo-600 mr-2"></i>
                    Pilih Akaun Staf ASM Sedia Ada (1-Klik)
                </div>
                <div class="grid sm:grid-cols-2 gap-2">
                    @foreach($existingUsers as $u)
                        <form method="POST" action="{{ route('auth.m365.mock.submit') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $u->email }}">
                            <input type="hidden" name="displayName" value="{{ $u->name }}">
                            <input type="hidden" name="department" value="{{ $u->department ?? 'Akademi Sains Malaysia' }}">
                            <input type="hidden" name="jobTitle" value="{{ $u->position ?? 'Pegawai' }}">
                            <input type="hidden" name="mobilePhone" value="{{ $u->phone ?? '03-8365 0000' }}">
                            <button type="submit" class="w-full text-left p-2.5 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 transition group">
                                <div class="font-bold text-xs text-slate-900 group-hover:text-indigo-700 flex items-center justify-between">
                                    <span>{{ $u->name }}</span>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 uppercase">{{ $u->role }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $u->email }}</div>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

            <!-- Section 3: Test Non-ASM Domain Rejection -->
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200">
                <div class="text-xs font-bold text-rose-900 uppercase tracking-wider mb-1 flex items-center">
                    <i class="fa-solid fa-shield-halved text-rose-600 mr-2"></i>
                    Ujian Keselamatan: Sekatan Domain Luar
                </div>
                <p class="text-[11px] text-rose-700 mb-2">
                    Emel luar (contoh: Gmail, Yahoo) mestilah <strong>disekat serta-merta</strong>.
                </p>
                <form method="POST" action="{{ route('auth.m365.mock.submit') }}">
                    @csrf
                    <input type="hidden" name="email" value="pengguna.luar@gmail.com">
                    <input type="hidden" name="displayName" value="Pengguna Luar Gmail">
                    <input type="hidden" name="department" value="Luar">
                    <button type="submit" class="py-1.5 px-3 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-ban"></i>
                        <span>Cuba Log Masuk guna pengguna.luar@gmail.com (Harus Ditolak)</span>
                    </button>
                </form>
            </div>

            <!-- Section 4: Custom Profile Simulation -->
            <div>
                <details class="text-xs group">
                    <summary class="cursor-pointer font-bold text-slate-600 hover:text-slate-900 flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200">
                        <span><i class="fa-solid fa-sliders mr-1.5 text-slate-500"></i> Borang Ujian Emel M365 Tersuai</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <form method="POST" action="{{ route('auth.m365.mock.submit') }}" class="mt-3 space-y-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Emel M365</label>
                            <input type="email" name="email" value="staf.baru@akademisains.gov.my" required
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Penuh</label>
                            <input type="text" name="displayName" value="Ahmad Faris" required
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Bahagian / Unit</label>
                                <input type="text" name="department" value="Unit Komunikasi Korporat"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Jawatan</label>
                                <input type="text" name="jobTitle" value="Pegawai Tadbir"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
                            </div>
                        </div>
                        <button type="submit" class="w-full py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                            Hantar Simulasi Log Masuk M365
                        </button>
                    </form>
                </details>
            </div>

            <!-- Return to Standard Login -->
            <div class="pt-2 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                    <i class="fa-solid fa-arrow-left mr-1.5"></i>
                    Kembali ke Halaman Log Masuk Biasa
                </a>
            </div>
        </div>
    </div>

</body>
</html>
