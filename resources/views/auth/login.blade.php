<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk | Sistem Pengurusan Kenderaan & Pemandu UPF ASM</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        asm: {
                            500: '#0e8ce9',
                            600: '#026fc7',
                            900: '#0c3f6e',
                            950: '#082849',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="min-h-screen py-8 px-4 flex flex-col items-center justify-center bg-gradient-to-br from-slate-950 via-asm-950 to-slate-900 text-slate-800">

    <div class="max-w-md w-full space-y-6 my-auto">
        <!-- Logo & Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center px-6 py-3.5 bg-white rounded-3xl shadow-2xl border-2 border-white mb-2 ring-4 ring-white/15">
                <img src="{{ asset('images/asm-logo-official.png') }}" alt="Akademi Sains Malaysia" class="h-16 sm:h-20 w-auto object-contain block mx-auto" onerror="this.src='{{ asset('images/asm-logo.png') }}'">
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-wide text-white uppercase drop-shadow">
                VEHICLE & DRIVER MANAGEMENT
            </h1>
            <p class="text-xs font-bold text-amber-400 tracking-wider uppercase">
                UNIT PENGURUSAN FASILITI (UPF) • AKADEMI SAINS MALAYSIA
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 border border-slate-200">
            @if(session('info'))
                <div class="mb-4 p-3 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-xs flex items-center">
                    <i class="fa-solid fa-circle-info mr-2 text-sky-600"></i>
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start">
                    <i class="fa-solid fa-circle-exclamation mr-2 text-rose-600 mt-0.5"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Emel Rasmi ASM</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email', 'upf@akademisains.gov.my') }}" required autofocus
                            class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-asm-600 focus:border-asm-600 transition"
                            placeholder="nama@akademisains.gov.my">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Laluan</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" value="password" required
                            class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-asm-600 focus:border-asm-600 transition"
                            placeholder="••••••••">
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">Kata laluan demo lalai: <span class="font-mono text-slate-600">password</span></div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-asm-600 focus:ring-asm-500">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-bold text-sm shadow-lg hover:shadow-xl transition flex items-center justify-center space-x-2">
                    <span>Log Masuk Sistem</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Quick Demo 1-Click Login Section -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <div class="text-center mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-white px-2">
                        Pintas Pantas Demonstrasi (1-Klik)
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('switch.user', 2) }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-asm-50 border border-slate-200 hover:border-asm-300 transition text-left group">
                        <div class="font-bold text-slate-900 group-hover:text-asm-700 flex items-center">
                            <i class="fa-solid fa-building-user text-asm-600 mr-1.5 text-xs"></i> Aizat
                        </div>
                        <div class="text-[10px] text-slate-500">Pegawai UPF (Admin)</div>
                    </a>

                    <a href="{{ route('switch.user', 4) }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-asm-50 border border-slate-200 hover:border-asm-300 transition text-left group">
                        <div class="font-bold text-slate-900 group-hover:text-asm-700 flex items-center">
                            <i class="fa-solid fa-user-pen text-indigo-600 mr-1.5 text-xs"></i> Mohd Azim
                        </div>
                        <div class="text-[10px] text-slate-500">Pemohon (Staff Dasar)</div>
                    </a>

                    <a href="{{ route('switch.user', 6) }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 transition text-left group">
                        <div class="font-bold text-slate-900 group-hover:text-emerald-700 flex items-center">
                            <i class="fa-solid fa-id-card text-emerald-600 mr-1.5 text-xs"></i> Fahizal
                        </div>
                        <div class="text-[10px] text-slate-500">Pemandu Kenderaan</div>
                    </a>

                    <a href="{{ route('switch.user', 7) }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 transition text-left group">
                        <div class="font-bold text-slate-900 group-hover:text-emerald-700 flex items-center">
                            <i class="fa-solid fa-id-card text-emerald-600 mr-1.5 text-xs"></i> Izzul
                        </div>
                        <div class="text-[10px] text-slate-500">Pemandu Kenderaan</div>
                    </a>

                    <a href="{{ route('switch.user', 8) }}" class="col-span-2 p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 transition text-left group flex items-center justify-between">
                        <div class="font-bold text-slate-900 group-hover:text-emerald-700 flex items-center">
                            <i class="fa-solid fa-id-card text-emerald-600 mr-1.5 text-xs"></i> Shareeza
                        </div>
                        <div class="text-[10px] text-slate-500">Pemandu Kenderaan</div>
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center text-xs text-slate-400">
            Hak Cipta Terpelihara &copy; {{ date('Y') }} Unit Pengurusan Fasiliti (UPF), Akademi Sains Malaysia.
        </div>
    </div>

</body>
</html>
