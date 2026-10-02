<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Sistem Pengurusan Kenderaan & Pemandu'); ?> | UPF Akademi Sains Malaysia</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        asm: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cc7fb',
                            400: '#38a8f8',
                            500: '#0e8ce9',
                            600: '#026fc7',
                            700: '#0358a1',
                            800: '#074b85',
                            900: '#0c3f6e',
                            950: '#082849', // Deep ASM Navy
                        },
                        gold: {
                            500: '#d97706',
                            600: '#b45309',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; font-size: 12pt; }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="h-full text-slate-800 antialiased flex flex-col" x-data="{ mobileMenuOpen: false }">

    <!-- Top Demo Role Switcher Bar (Tukar Peranan Ujian) -->
    <div class="no-print bg-slate-900 text-white text-xs px-3 py-1.5 border-b border-slate-700 flex flex-wrap items-center justify-between gap-2 shadow-inner z-50">
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-slate-950 uppercase tracking-wider">
                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Tukar Peranan
            </span>
            <span class="text-slate-300 hidden md:inline text-[11px]">Pilih peranan untuk menguji sistem serta-merta:</span>
        </div>
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="<?php echo e(route('switch.user', 1)); ?>" class="px-2 py-0.5 rounded text-[11px] transition <?php echo e(auth()->id() == 1 ? 'bg-asm-600 text-white font-bold ring-1 ring-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'); ?>" title="Admin Sistem">
                <i class="fa-solid fa-user-shield mr-1"></i> Admin
            </a>
            <a href="<?php echo e(route('switch.user', 2)); ?>" class="px-2 py-0.5 rounded text-[11px] transition <?php echo e(auth()->id() == 2 ? 'bg-asm-600 text-white font-bold ring-1 ring-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'); ?>" title="Pegawai UPF (Aizat)">
                <i class="fa-solid fa-building-user mr-1"></i> UPF (Aizat)
            </a>
            <a href="<?php echo e(route('switch.user', 3)); ?>" class="px-2 py-0.5 rounded text-[11px] transition <?php echo e(auth()->id() == 3 ? 'bg-asm-600 text-white font-bold ring-1 ring-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'); ?>" title="Pegawai UPF (Azwa)">
                <i class="fa-solid fa-user-tie mr-1"></i> UPF (Azwa)
            </a>
            <a href="<?php echo e(route('switch.user', 4)); ?>" class="px-2 py-0.5 rounded text-[11px] transition <?php echo e(auth()->id() == 4 ? 'bg-asm-600 text-white font-bold ring-1 ring-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'); ?>" title="Pemohon / Staff (Mohd Azim)">
                <i class="fa-solid fa-user-pen mr-1"></i> Pemohon (Azim)
            </a>
            <a href="<?php echo e(route('switch.user', 6)); ?>" class="px-2 py-0.5 rounded text-[11px] transition <?php echo e(auth()->id() == 6 ? 'bg-emerald-600 text-white font-bold ring-1 ring-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'); ?>" title="Pemandu (Fahizal)">
                <i class="fa-solid fa-id-card mr-1"></i> Pemandu (Fahizal)
            </a>
            <a href="<?php echo e(route('switch.user', 7)); ?>" class="px-2 py-0.5 rounded text-[11px] transition <?php echo e(auth()->id() == 7 ? 'bg-emerald-600 text-white font-bold ring-1 ring-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'); ?>" title="Pemandu (Izzul)">
                <i class="fa-solid fa-id-card mr-1"></i> Pemandu (Izzul)
            </a>
            <a href="<?php echo e(route('switch.user', 8)); ?>" class="px-2 py-0.5 rounded text-[11px] transition <?php echo e(auth()->id() == 8 ? 'bg-emerald-600 text-white font-bold ring-1 ring-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'); ?>" title="Pemandu (Shareeza)">
                <i class="fa-solid fa-id-card mr-1"></i> Pemandu (Shareeza)
            </a>
        </div>
    </div>

    <!-- Main Header Bar -->
    <header class="no-print bg-asm-950 text-white sticky top-0 z-40 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <!-- Hamburger Button (Mobile) -->
                    <button type="button" @click="mobileMenuOpen = true" class="md:hidden p-2 rounded-xl text-slate-300 hover:text-white hover:bg-asm-900 focus:outline-none" aria-label="Buka Menu">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>

                    <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center space-x-3 group">
                        <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center p-1.5 shadow-md border border-slate-200 flex-shrink-0">
                            <img src="<?php echo e(asset('images/asm-logo-official.png')); ?>" alt="ASM" class="h-8 w-auto object-contain block" onerror="this.src='<?php echo e(asset('images/asm-logo.png')); ?>'">
                        </div>
                        <div>
                            <div class="text-xs sm:text-sm font-extrabold tracking-wide uppercase text-white flex items-center gap-1.5">
                                SISTEM PENGURUSAN KENDERAAN & PEMANDU
                                <span class="bg-amber-400 text-slate-950 text-[10px] font-black px-1.5 py-0.2 rounded">UPF</span>
                            </div>
                            <div class="text-[10px] sm:text-[11px] text-slate-300 tracking-wider">UNIT PENGURUSAN FASILITI • AKADEMI SAINS MALAYSIA</div>
                        </div>
                    </a>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <?php if(auth()->guard()->check()): ?>
                        <!-- Quick Mohon Kenderaan button on Header (Desktop) -->
                        <a href="<?php echo e(route('requests.create')); ?>" class="hidden lg:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-sm transition">
                            <i class="fa-solid fa-circle-plus"></i>
                            <span>Mohon Kenderaan</span>
                        </a>

                        <!-- Notifications Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative p-2 text-slate-300 hover:text-white hover:bg-asm-900 rounded-full transition focus:outline-none" title="Notifikasi">
                                <i class="fa-solid fa-bell text-lg"></i>
                                <?php if(auth()->user()->unreadNotificationsCount() > 0): ?>
                                    <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full animate-bounce">
                                        <?php echo e(auth()->user()->unreadNotificationsCount()); ?>

                                    </span>
                                <?php endif; ?>
                            </button>
                            <!-- Notification Menu -->
                            <div x-show="open" @click.away="open = false" x-cloak class="origin-top-right absolute right-0 mt-2 w-80 rounded-2xl shadow-2xl bg-white text-slate-800 ring-1 ring-black/10 z-50 overflow-hidden border border-slate-200">
                                <div class="p-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Notifikasi</span>
                                    <form method="POST" action="<?php echo e(route('notifications.read-all')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="text-[11px] text-asm-600 font-bold hover:underline">Tanda semua dibaca</button>
                                    </form>
                                </div>
                                <div class="max-h-64 overflow-y-auto divide-y divide-slate-100">
                                    <?php $__empty_1 = true; $__currentLoopData = auth()->user()->notifications()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <a href="<?php echo e(route('notifications.read', $n->id)); ?>" class="block p-3 hover:bg-slate-50 transition <?php echo e(!$n->is_read ? 'bg-blue-50/50' : ''); ?>">
                                            <div class="text-xs font-semibold text-slate-900 <?php echo e(!$n->is_read ? 'text-asm-700' : ''); ?>"><?php echo e($n->title); ?></div>
                                            <div class="text-[11px] text-slate-600 line-clamp-2 mt-0.5"><?php echo e($n->message); ?></div>
                                            <div class="text-[10px] text-slate-400 mt-1"><?php echo e($n->created_at->diffForHumans()); ?></div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="p-4 text-center text-xs text-slate-500">Tiada notifikasi terkini.</div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-2.5 bg-slate-50 text-center border-t border-slate-100">
                                    <a href="<?php echo e(route('notifications.index')); ?>" class="text-xs text-asm-600 font-bold hover:underline">Lihat semua notifikasi</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Pill -->
                        <div class="flex items-center space-x-2 pl-2 border-l border-slate-700">
                            <div class="hidden sm:block text-right">
                                <div class="text-xs font-bold text-white leading-tight"><?php echo e(auth()->user()->name); ?></div>
                                <div class="text-[10px] text-amber-300 uppercase tracking-wider font-semibold"><?php echo e(strtoupper(auth()->user()->role)); ?></div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-asm-700 text-white flex items-center justify-center font-black text-xs ring-2 ring-white/20">
                                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                            </div>
                            <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="p-1.5 text-slate-300 hover:text-rose-400 rounded-md transition" title="Log Keluar">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Horizontal Top Navigation Menu Bar (Desktop) with Stable Click-Based Dropdowns -->
        <nav class="hidden md:block bg-asm-900 border-t border-asm-800 shadow-inner" 
             x-data="{ activeMenu: null }" 
             @click.outside="activeMenu = null" 
             @keydown.escape.window="activeMenu = null">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between text-xs">
                <div class="flex items-center space-x-1 lg:space-x-2 py-1.5">

                    <!-- 1. Dashboard -->
                    <a href="<?php echo e(route('dashboard')); ?>" class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 transition <?php echo e(request()->routeIs('dashboard') || request()->routeIs('home') ? 'bg-asm-700 text-white shadow-sm' : 'text-slate-200 hover:bg-asm-800 hover:text-white'); ?>">
                        <i class="fa-solid fa-gauge-high text-sm <?php echo e(request()->routeIs('dashboard') ? 'text-amber-400' : 'text-slate-300'); ?>"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- 2. Permohonan Kenderaan (Dropdown) -->
                    <?php
                        $pendingCount = \App\Models\VehicleRequest::whereIn('status', ['submitted', 'under_review'])->count();
                        $isRequestsActive = request()->routeIs('requests.*') && !request()->routeIs('requests.create');
                    ?>
                    <div class="relative">
                        <button type="button" 
                                @click="activeMenu = (activeMenu === 'requests' ? null : 'requests')" 
                                @mouseenter="if (activeMenu !== null) { activeMenu = 'requests'; }"
                                class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 transition select-none <?php echo e($isRequestsActive ? 'bg-asm-700 text-white shadow-sm' : 'text-slate-200 hover:bg-asm-800 hover:text-white'); ?>"
                                :class="{ 'bg-asm-800 text-white ring-1 ring-white/20': activeMenu === 'requests' }">
                            <i class="fa-solid fa-file-signature text-sm <?php echo e($isRequestsActive ? 'text-amber-400' : 'text-slate-300'); ?>"></i>
                            <span>Permohonan</span>
                            <?php if($pendingCount > 0 && (auth()->user()->isUpf() || auth()->user()->isAdmin())): ?>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-amber-400 text-slate-950"><?php echo e($pendingCount); ?></span>
                            <?php endif; ?>
                            <i class="fa-solid fa-chevron-down text-[10px] opacity-70 ml-0.5 transition-transform duration-200" :class="{ 'rotate-180': activeMenu === 'requests' }"></i>
                        </button>
                        <!-- Vertical Dropdown Content: Attached seamlessly, stays open firmly -->
                        <div x-show="activeMenu === 'requests'" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             x-cloak 
                             class="absolute left-0 top-full pt-1.5 w-72 z-50">
                            <div class="bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-200 p-1.5 ring-1 ring-black/5 divide-y divide-slate-100">
                                <div class="p-1 space-y-0.5">
                                    <a href="<?php echo e(route('requests.create')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-emerald-900 transition group">
                                        <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                            <i class="fa-solid fa-plus font-black"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-black text-emerald-800">Mohon Kenderaan Baharu</div>
                                            <div class="text-[10px] text-emerald-600 font-normal">Borang permohonan UPFIT</div>
                                        </div>
                                    </a>
                                    <a href="<?php echo e(route('requests.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                            <i class="fa-solid fa-list-check"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-extrabold text-slate-900"><?php echo e(auth()->user()->isApplicant() ? 'Senarai Permohonan Saya' : 'Senarai Semua Permohonan'); ?></div>
                                            <div class="text-[10px] text-slate-400 font-normal"><?php echo e(auth()->user()->isApplicant() ? 'Semak status permohonan anda' : 'Semak status & carian rekod'); ?></div>
                                        </div>
                                    </a>
                                </div>
                                <?php if(auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                                    <div class="p-1">
                                        <a href="<?php echo e(route('requests.index', ['status' => 'submitted'])); ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-amber-50 text-amber-900 transition group">
                                            <div class="flex items-center gap-3">
                                                <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                    <i class="fa-solid fa-clock"></i>
                                                </span>
                                                <div>
                                                    <div class="text-xs font-extrabold text-amber-900">Menunggu Semakan UPF</div>
                                                    <div class="text-[10px] text-amber-700/70 font-normal">Tindakan kelulusan & tugasan</div>
                                                </div>
                                            </div>
                                            <?php if($pendingCount > 0): ?>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900"><?php echo e($pendingCount); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Jadual & Tugasan (Dropdown) -->
                    <?php $isScheduleActive = request()->routeIs('schedules.*') || request()->routeIs('driver.*'); ?>
                    <div class="relative">
                        <button type="button" 
                                @click="activeMenu = (activeMenu === 'schedules' ? null : 'schedules')" 
                                @mouseenter="if (activeMenu !== null) { activeMenu = 'schedules'; }"
                                class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 transition select-none <?php echo e($isScheduleActive ? 'bg-asm-700 text-white shadow-sm' : 'text-slate-200 hover:bg-asm-800 hover:text-white'); ?>"
                                :class="{ 'bg-asm-800 text-white ring-1 ring-white/20': activeMenu === 'schedules' }">
                            <i class="fa-solid fa-calendar-days text-sm <?php echo e($isScheduleActive ? 'text-amber-400' : 'text-slate-300'); ?>"></i>
                            <span><?php echo e(auth()->user()->isDriver() ? 'Jadual & Tugasan' : 'Jadual Perjalanan'); ?></span>
                            <i class="fa-solid fa-chevron-down text-[10px] opacity-70 ml-0.5 transition-transform duration-200" :class="{ 'rotate-180': activeMenu === 'schedules' }"></i>
                        </button>
                        <!-- Vertical Dropdown Content -->
                        <div x-show="activeMenu === 'schedules'" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             x-cloak 
                             class="absolute left-0 top-full pt-1.5 w-72 z-50">
                            <div class="bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-200 p-1.5 ring-1 ring-black/5 divide-y divide-slate-100">
                                <div class="p-1 space-y-0.5">
                                    <?php if(auth()->user()->isDriver()): ?>
                                        <a href="<?php echo e(route('driver.tasks')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-emerald-800 font-bold transition group">
                                            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                <i class="fa-solid fa-clipboard-check font-black"></i>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-black text-emerald-900">Tugasan Saya (Pemandu)</div>
                                                <div class="text-[10px] text-emerald-600 font-normal">Terima tugasan & rekod perjalanan</div>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('schedules.excel')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                        <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                            <i class="fa-solid fa-file-excel"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-extrabold text-slate-900">Jadual Format Excel UPF</div>
                                            <div class="text-[10px] text-slate-400 font-normal">Paparan grid Excel rasmi ASM</div>
                                        </div>
                                    </a>
                                    <a href="<?php echo e(route('schedules.weekly')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                            <i class="fa-solid fa-table-cells"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-extrabold text-slate-900">Jadual Pemandu Mingguan</div>
                                            <div class="text-[10px] text-slate-400 font-normal">Matriks tugasan setiap pemandu</div>
                                        </div>
                                    </a>
                                    <a href="<?php echo e(route('schedules.calendar')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                            <i class="fa-solid fa-calendar"></i>
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-extrabold text-slate-900">Kalendar Tugasan Bulanan</div>
                                            <div class="text-[10px] text-slate-400 font-normal">Paparan kalendar interaktif</div>
                                        </div>
                                    </a>
                                </div>
                                <?php if(auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                                    <div class="p-1">
                                        <a href="<?php echo e(route('schedules.import')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-amber-50 text-amber-900 transition group">
                                            <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                <i class="fa-solid fa-file-import"></i>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-extrabold text-amber-900">Import Jadual Excel Lama</div>
                                                <div class="text-[10px] text-amber-700/70 font-normal">Muat naik fail Excel sedia ada</div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Kenderaan & Operasi (Dropdown - Pemandu, UPF & Admin) -->
                    <?php if(auth()->user()->isDriver() || auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                        <?php $isOpsActive = request()->routeIs('vehicles.*') || request()->routeIs('drivers.*') || request()->routeIs('fuel.*') || request()->routeIs('incidents.*'); ?>
                        <div class="relative">
                            <button type="button" 
                                    @click="activeMenu = (activeMenu === 'operations' ? null : 'operations')" 
                                    @mouseenter="if (activeMenu !== null) { activeMenu = 'operations'; }"
                                    class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 transition select-none <?php echo e($isOpsActive ? 'bg-asm-700 text-white shadow-sm' : 'text-slate-200 hover:bg-asm-800 hover:text-white'); ?>"
                                    :class="{ 'bg-asm-800 text-white ring-1 ring-white/20': activeMenu === 'operations' }">
                                <i class="fa-solid fa-car-side text-sm <?php echo e($isOpsActive ? 'text-amber-400' : 'text-slate-300'); ?>"></i>
                                <span>Kenderaan & Operasi</span>
                                <i class="fa-solid fa-chevron-down text-[10px] opacity-70 ml-0.5 transition-transform duration-200" :class="{ 'rotate-180': activeMenu === 'operations' }"></i>
                            </button>
                            <!-- Vertical Dropdown Content -->
                            <div x-show="activeMenu === 'operations'" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 x-cloak 
                                 class="absolute left-0 top-full pt-1.5 w-72 z-50">
                                <div class="bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-200 p-1.5 ring-1 ring-black/5 divide-y divide-slate-100">
                                    <div class="p-1 space-y-0.5">
                                        <?php if(auth()->user()->isDriver() || auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                                            <a href="<?php echo e(route('vehicles.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                    <i class="fa-solid fa-car"></i>
                                                </span>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-xs font-extrabold text-slate-900">Master Data Kenderaan</div>
                                                    <div class="text-[10px] text-slate-400 font-normal">Maklumat 9 kenderaan jabatan</div>
                                                </div>
                                            </a>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                                            <a href="<?php echo e(route('drivers.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                                <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                    <i class="fa-solid fa-id-badge"></i>
                                                </span>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-xs font-extrabold text-slate-900">Master Data Pemandu</div>
                                                    <div class="text-[10px] text-slate-400 font-normal">Profil pemandu & lesen memandu</div>
                                                </div>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-1 space-y-0.5">
                                        <a href="<?php echo e(route('fuel.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                            <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                <i class="fa-solid fa-gas-pump"></i>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-extrabold text-slate-900">Log Minyak & Kad Inden</div>
                                                <div class="text-[10px] text-slate-400 font-normal">Rekod isian bahan api & resit</div>
                                            </div>
                                        </a>
                                        <a href="<?php echo e(route('incidents.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                            <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                <i class="fa-solid fa-triangle-exclamation"></i>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-extrabold text-slate-900">Kerosakan & Kemalangan</div>
                                                <div class="text-[10px] text-slate-400 font-normal">Laporan isu fizikal kenderaan</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 5. Laporan & Pentadbiran (Dropdown - UPF/Admin) -->
                    <?php if(auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                        <?php $isAdminActive = request()->routeIs('reports.*') || request()->routeIs('audit.*') || request()->routeIs('settings.*') || request()->routeIs('users.*'); ?>
                        <div class="relative">
                            <button type="button" 
                                    @click="activeMenu = (activeMenu === 'admin' ? null : 'admin')" 
                                    @mouseenter="if (activeMenu !== null) { activeMenu = 'admin'; }"
                                    class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 transition select-none <?php echo e($isAdminActive ? 'bg-asm-700 text-white shadow-sm' : 'text-slate-200 hover:bg-asm-800 hover:text-white'); ?>"
                                    :class="{ 'bg-asm-800 text-white ring-1 ring-white/20': activeMenu === 'admin' }">
                                <i class="fa-solid fa-sliders text-sm <?php echo e($isAdminActive ? 'text-amber-400' : 'text-slate-300'); ?>"></i>
                                <span>Laporan & Pentadbiran</span>
                                <i class="fa-solid fa-chevron-down text-[10px] opacity-70 ml-0.5 transition-transform duration-200" :class="{ 'rotate-180': activeMenu === 'admin' }"></i>
                            </button>
                            <!-- Vertical Dropdown Content -->
                            <div x-show="activeMenu === 'admin'" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 x-cloak 
                                 class="absolute left-0 top-full pt-1.5 w-72 z-50">
                                <div class="bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-200 p-1.5 ring-1 ring-black/5 divide-y divide-slate-100">
                                    <div class="p-1 space-y-0.5">
                                        <a href="<?php echo e(route('reports.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                            <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                <i class="fa-solid fa-chart-pie"></i>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-extrabold text-slate-900">Laporan UPF (9 Jenis)</div>
                                                <div class="text-[10px] text-slate-400 font-normal">Analisis penggunaan, mileage & kos</div>
                                            </div>
                                        </a>
                                        <a href="<?php echo e(route('audit.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-extrabold text-slate-900">Jejak Audit (Audit Trail)</div>
                                                <div class="text-[10px] text-slate-400 font-normal">Log aktiviti pengguna dan rekod masa</div>
                                            </div>
                                        </a>
                                        <a href="<?php echo e(route('settings.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group">
                                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                <i class="fa-solid fa-gear"></i>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-extrabold text-slate-900">Tetapan Sistem</div>
                                                <div class="text-[10px] text-slate-400 font-normal">Konfigurasi notis singkat & operasi</div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php if(auth()->user()->isAdmin()): ?>
                                        <div class="p-1">
                                            <a href="<?php echo e(route('users.index')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 text-indigo-900 transition group">
                                                <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition">
                                                    <i class="fa-solid fa-users-gear font-bold"></i>
                                                </span>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-xs font-extrabold text-indigo-900">Pengurusan Pengguna</div>
                                                    <div class="text-[10px] text-indigo-600 font-normal">Daftar pengguna & peranan sistem</div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Right Quick Status Note -->
                <div class="hidden lg:flex items-center gap-2 text-slate-300 text-[11px]">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sistem Aktif • UPF ASM</span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Versi Mesra Mobile: Slide-Over Off-Canvas Navigation Drawer -->
    <div x-show="mobileMenuOpen" x-cloak class="no-print fixed inset-0 z-50 overflow-hidden md:hidden" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" x-transition.opacity.duration.300ms class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>

        <div class="fixed inset-y-0 left-0 max-w-full flex">
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="w-screen max-w-xs bg-white shadow-2xl flex flex-col">

                <!-- Drawer Header -->
                <div class="p-4 bg-asm-950 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-lg bg-white p-1 flex items-center justify-center shadow">
                            <img src="<?php echo e(asset('images/asm-logo-official.png')); ?>" alt="ASM" class="h-6 w-auto object-contain" onerror="this.src='<?php echo e(asset('images/asm-logo.png')); ?>'">
                        </div>
                        <div>
                            <div class="text-xs font-black uppercase text-white leading-tight">UPF KENDERAAN</div>
                            <div class="text-[9px] text-slate-300">AKADEMI SAINS MALAYSIA</div>
                        </div>
                    </div>
                    <button type="button" @click="mobileMenuOpen = false" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-asm-900 focus:outline-none" aria-label="Tutup Menu">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- User Profile Card in Drawer -->
                <?php if(auth()->guard()->check()): ?>
                    <div class="p-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-asm-700 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                            </div>
                            <div>
                                <div class="font-bold text-slate-900 leading-tight"><?php echo e(auth()->user()->name); ?></div>
                                <div class="text-[10px] text-asm-700 font-semibold"><?php echo e(auth()->user()->role_label); ?></div>
                            </div>
                        </div>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 rounded text-xs font-bold flex items-center gap-1" title="Log Keluar">
                                <i class="fa-solid fa-power-off text-rose-500"></i>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Mobile Quick Action Button -->
                <div class="p-3 bg-white border-b border-slate-100">
                    <a href="<?php echo e(route('requests.create')); ?>" @click="mobileMenuOpen = false" class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow flex items-center justify-center gap-2 transition">
                        <i class="fa-solid fa-circle-plus"></i>
                        <span>+ Mohon Kenderaan Baharu</span>
                    </a>
                </div>

                <!-- Drawer Scrollable Links -->
                <div class="flex-1 overflow-y-auto p-3 space-y-4 text-xs">

                    <!-- Dashboard -->
                    <div>
                        <a href="<?php echo e(route('dashboard')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('dashboard') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                            <i class="fa-solid fa-gauge-high w-5 text-center text-sm <?php echo e(request()->routeIs('dashboard') ? 'text-asm-600' : 'text-slate-400'); ?>"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>

                    <!-- Permohonan Section -->
                    <div class="space-y-1">
                        <div class="text-[10px] font-black uppercase text-slate-400 tracking-wider px-2">Permohonan Kenderaan</div>
                        <a href="<?php echo e(route('requests.index')); ?>" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('requests.index') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-list-check w-5 text-center text-slate-400"></i>
                                <span>Semua Permohonan</span>
                            </div>
                            <?php if($pendingCount > 0 && (auth()->user()->isUpf() || auth()->user()->isAdmin())): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900"><?php echo e($pendingCount); ?></span>
                            <?php endif; ?>
                        </a>
                    </div>

                    <!-- Jadual & Tugasan Section -->
                    <div class="space-y-1">
                        <div class="text-[10px] font-black uppercase text-slate-400 tracking-wider px-2">Jadual & Kalendar</div>
                        <?php if(auth()->user()->isDriver()): ?>
                            <a href="<?php echo e(route('driver.tasks')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('driver.tasks') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                                <i class="fa-solid fa-clipboard-check w-5 text-center text-emerald-600"></i>
                                <span>Tugasan Saya (Pemandu)</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('schedules.excel')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('schedules.excel') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                            <i class="fa-solid fa-file-excel w-5 text-center text-emerald-600"></i>
                            <span>Jadual Format Excel UPF</span>
                        </a>
                        <a href="<?php echo e(route('schedules.weekly')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('schedules.weekly') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                            <i class="fa-solid fa-table-cells w-5 text-center text-indigo-500"></i>
                            <span>Jadual Pemandu Mingguan</span>
                        </a>
                        <a href="<?php echo e(route('schedules.calendar')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('schedules.calendar') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                            <i class="fa-solid fa-calendar w-5 text-center text-sky-500"></i>
                            <span>Kalendar Tugasan</span>
                        </a>
                    </div>

                    <!-- Operasi & Kenderaan Section (Pemandu, UPF & Admin) -->
                    <?php if(auth()->user()->isDriver() || auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                        <div class="space-y-1">
                            <div class="text-[10px] font-black uppercase text-slate-400 tracking-wider px-2">Operasi & Kenderaan</div>
                            <?php if(auth()->user()->isDriver() || auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                                <a href="<?php echo e(route('vehicles.index')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('vehicles.*') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                                    <i class="fa-solid fa-car w-5 text-center text-blue-500"></i>
                                    <span>Master Data Kenderaan</span>
                                </a>
                            <?php endif; ?>
                            <?php if(auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                                <a href="<?php echo e(route('drivers.index')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('drivers.*') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                                    <i class="fa-solid fa-id-badge w-5 text-center text-teal-600"></i>
                                    <span>Master Data Pemandu</span>
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('fuel.index')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('fuel.*') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                                <i class="fa-solid fa-gas-pump w-5 text-center text-amber-500"></i>
                                <span>Log Minyak & Kad Inden</span>
                            </a>
                            <a href="<?php echo e(route('incidents.index')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('incidents.*') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                                <i class="fa-solid fa-triangle-exclamation w-5 text-center text-rose-500"></i>
                                <span>Kerosakan & Kemalangan</span>
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Laporan & Pentadbiran (UPF/Admin) -->
                    <?php if(auth()->user()->isUpf() || auth()->user()->isAdmin()): ?>
                        <div class="space-y-1">
                            <div class="text-[10px] font-black uppercase text-slate-400 tracking-wider px-2">Laporan & Pentadbiran</div>
                            <a href="<?php echo e(route('reports.index')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('reports.*') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                                <i class="fa-solid fa-chart-pie w-5 text-center text-purple-600"></i>
                                <span>Laporan UPF (9 Jenis)</span>
                            </a>
                            <a href="<?php echo e(route('audit.index')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('audit.*') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                                <i class="fa-solid fa-clock-rotate-left w-5 text-center text-slate-500"></i>
                                <span>Jejak Audit Sistem</span>
                            </a>
                            <a href="<?php echo e(route('settings.index')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('settings.*') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                                <i class="fa-solid fa-gear w-5 text-center text-slate-500"></i>
                                <span>Tetapan Sistem</span>
                            </a>
                            <?php if(auth()->user()->isAdmin()): ?>
                                <a href="<?php echo e(route('users.index')); ?>" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold <?php echo e(request()->routeIs('users.*') ? 'bg-asm-50 text-asm-800 border-l-4 border-asm-600' : 'text-slate-700 hover:bg-slate-50'); ?>">
                                    <i class="fa-solid fa-users-gear w-5 text-center text-indigo-600"></i>
                                    <span>Pengurusan Pengguna</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Drawer Footer -->
                <div class="p-3 bg-slate-50 border-t border-slate-200 text-center">
                    <div class="text-[10px] font-bold text-slate-700">AKADEMI SAINS MALAYSIA</div>
                    <div class="text-[9px] text-slate-400">Unit Pengurusan Fasiliti (UPF)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area (Spacious Full Width without Cramped Sidebar) -->
    <main class="flex-1 overflow-y-auto bg-slate-50 p-3 sm:p-6 lg:p-8 pb-24 md:pb-10">
        <div class="max-w-7xl mx-auto">
            <!-- Flash Alerts -->
            <?php if(session('success')): ?>
                <div class="no-print mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start space-x-3 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg mt-0.5 shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold"><?php echo e(session('success')); ?></div>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="no-print mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start space-x-3 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-xmark text-rose-500 text-lg mt-0.5 shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold"><?php echo e(session('error')); ?></div>
                </div>
            <?php endif; ?>

            <?php if(session('warning')): ?>
                <div class="no-print mb-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start space-x-3 shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg mt-0.5 shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold"><?php echo e(session('warning')); ?></div>
                </div>
            <?php endif; ?>

            <?php if(session('info')): ?>
                <div class="no-print mb-4 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-800 flex items-start space-x-3 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-info text-sky-500 text-lg mt-0.5 shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold"><?php echo e(session('info')); ?></div>
                </div>
            <?php endif; ?>

            <!-- Yield Main Page Content -->
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- Versi Mesra Mobile: Sticky Bottom Navigation Bar on Mobile Devices -->
    <nav class="no-print md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] px-2 py-1.5 flex justify-around items-center">
        <!-- 1. Dashboard -->
        <a href="<?php echo e(route('dashboard')); ?>" class="flex flex-col items-center justify-center py-1 px-2 rounded-xl transition <?php echo e(request()->routeIs('dashboard') || request()->routeIs('home') ? 'text-asm-700 font-black' : 'text-slate-500 hover:text-slate-800'); ?>">
            <i class="fa-solid fa-gauge-high text-lg"></i>
            <span class="text-[10px] mt-0.5">Dashboard</span>
        </a>

        <!-- 2. Permohonan -->
        <a href="<?php echo e(route('requests.index')); ?>" class="flex flex-col items-center justify-center py-1 px-2 rounded-xl transition relative <?php echo e(request()->routeIs('requests.index') ? 'text-asm-700 font-black' : 'text-slate-500 hover:text-slate-800'); ?>">
            <i class="fa-solid fa-file-signature text-lg"></i>
            <span class="text-[10px] mt-0.5">Permohonan</span>
            <?php if(isset($pendingCount) && $pendingCount > 0 && (auth()->user()->isUpf() || auth()->user()->isAdmin())): ?>
                <span class="absolute top-0 right-1 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
            <?php endif; ?>
        </a>

        <!-- 3. Prominent Add Button (+ Mohon) -->
        <a href="<?php echo e(route('requests.create')); ?>" class="flex flex-col items-center justify-center -mt-5">
            <div class="w-12 h-12 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center shadow-lg ring-4 ring-white transition transform active:scale-95">
                <i class="fa-solid fa-plus text-xl"></i>
            </div>
            <span class="text-[9px] font-black text-emerald-800 mt-0.5">Mohon</span>
        </a>

        <!-- 4. Jadual -->
        <a href="<?php echo e(route('schedules.excel')); ?>" class="flex flex-col items-center justify-center py-1 px-2 rounded-xl transition <?php echo e(request()->routeIs('schedules.*') ? 'text-asm-700 font-black' : 'text-slate-500 hover:text-slate-800'); ?>">
            <i class="fa-solid fa-calendar-days text-lg"></i>
            <span class="text-[10px] mt-0.5">Jadual</span>
        </a>

        <!-- 5. Menu Penuh -->
        <button type="button" @click="mobileMenuOpen = true" class="flex flex-col items-center justify-center py-1 px-2 rounded-xl text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-bars text-lg"></i>
            <span class="text-[10px] mt-0.5">Menu</span>
        </button>
    </nav>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/layouts/app.blade.php ENDPATH**/ ?>