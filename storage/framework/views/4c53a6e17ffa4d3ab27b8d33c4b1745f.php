<?php $__env->startSection('title', 'Senarai Tugasan Pemandu'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl mx-auto" x-data="{ activeTab: 'today' }">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-asm-950 rounded-3xl p-6 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500 text-slate-950 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-clipboard-list mr-1.5"></i> Tugasan Pemandu UPF
                </span>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                    TUGASAN & JADUAL: <?php echo e(strtoupper($driver ? $driver->name : auth()->user()->name)); ?>

                </h1>
                <p class="text-xs text-slate-300 mt-1">
                    No. Kakitangan: <span class="font-mono text-emerald-400 font-bold"><?php echo e($driver?->staff_number ?? '-'); ?></span> |
                    No. Telefon: <span class="font-bold text-white"><?php echo e($driver?->phone ?? '-'); ?></span> |
                    Lesen: <span class="font-bold text-amber-300"><?php echo e($driver?->license_class ?? ($driver?->license_type ?? '-')); ?> <?php echo e($driver?->license_expiry ? '(Tamat: '.\Carbon\Carbon::parse($driver->license_expiry)->format('d/m/Y').')' : ''); ?></span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('schedules.calendar')); ?>" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition flex items-center">
                    <i class="fa-regular fa-calendar-days mr-2"></i> Kalendar Tugasan
                </a>
                <a href="<?php echo e(route('dashboard')); ?>" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black transition flex items-center shadow-lg shadow-emerald-500/30">
                    <i class="fa-solid fa-gauge mr-1.5"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="grid grid-cols-3 gap-2 mt-6 pt-5 border-t border-white/10">
            <button @click="activeTab = 'today'"
                    :class="activeTab === 'today' ? 'bg-white text-slate-950 shadow-md font-black' : 'bg-white/10 text-slate-200 hover:bg-white/15 font-semibold'"
                    class="py-2.5 px-3 rounded-2xl text-xs sm:text-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-calendar-day text-amber-500"></i>
                <span>Hari Ini</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800"><?php echo e($todayTasks->count()); ?></span>
            </button>
            <button @click="activeTab = 'upcoming'"
                    :class="activeTab === 'upcoming' ? 'bg-white text-slate-950 shadow-md font-black' : 'bg-white/10 text-slate-200 hover:bg-white/15 font-semibold'"
                    class="py-2.5 px-3 rounded-2xl text-xs sm:text-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-calendar-week text-emerald-500"></i>
                <span>Akan Datang</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800"><?php echo e($upcomingTasks->count()); ?></span>
            </button>
            <button @click="activeTab = 'completed'"
                    :class="activeTab === 'completed' ? 'bg-white text-slate-950 shadow-md font-black' : 'bg-white/10 text-slate-200 hover:bg-white/15 font-semibold'"
                    class="py-2.5 px-3 rounded-2xl text-xs sm:text-sm transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check text-sky-500"></i>
                <span>Selesai</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800"><?php echo e($completedTasks->count()); ?></span>
            </button>
        </div>
    </div>

    <!-- TAB 1: TUGASAN HARI INI -->
    <div x-show="activeTab === 'today'" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                Tugasan Perjalanan Hari Ini (<?php echo e(\Carbon\Carbon::today()->translatedFormat('l, d M Y')); ?>)
            </h2>
            <span class="text-xs text-slate-500 font-semibold"><?php echo e($todayTasks->count()); ?> tugasan dijadualkan</span>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $todayTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-3xl border-2 <?php echo e(in_array($task->status, ['assigned', 'driver_accepted', 'in_progress']) ? 'border-amber-400 ring-4 ring-amber-50' : 'border-slate-200'); ?> shadow-md overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-xl bg-slate-900 text-amber-400 font-black text-xs font-mono">
                            <i class="fa-regular fa-clock mr-1"></i>
                            <?php echo e(\Carbon\Carbon::parse($task->start_time)->format('h:i A')); ?>

                        </span>
                        <span class="text-xs font-bold text-slate-600 font-mono"><?php echo e($task->request_number); ?></span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold border <?php echo e($task->status_badge['class']); ?>">
                        <?php echo e(strtoupper($task->status_badge['label'])); ?>

                    </span>
                </div>

                <div class="p-5 sm:p-6 space-y-4">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Tujuan Perjalanan</div>
                        <div class="text-base sm:text-lg font-black text-slate-900 mt-0.5 leading-snug">
                            <?php echo e($task->purpose); ?>

                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-[10px] font-bold uppercase text-slate-400 flex items-center">
                                <i class="fa-solid fa-circle-dot text-emerald-600 mr-1.5"></i> Lokasi Ambil (Pickup)
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900 mt-1">
                                <?php echo e($task->origin); ?>

                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Masa Ambil: <strong class="text-slate-700"><?php echo e(\Carbon\Carbon::parse($task->start_time)->format('h:i A')); ?></strong>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="text-[10px] font-bold uppercase text-slate-400 flex items-center">
                                <i class="fa-solid fa-location-dot text-rose-600 mr-1.5"></i> Destinasi
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900 mt-1">
                                <?php echo e($task->destination); ?>

                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Anggaran Selesai: <strong class="text-slate-700"><?php echo e(\Carbon\Carbon::parse($task->end_time)->format('h:i A')); ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger & Vehicle info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-base flex-shrink-0">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Pegawai / Pemohon</div>
                                <div class="text-xs font-black text-slate-900"><?php echo e($task->user?->name); ?></div>
                                <div class="text-[11px] text-slate-500"><?php echo e($task->user?->department ?? 'ASM'); ?> | <?php echo e($task->user?->phone ?? '-'); ?></div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-base flex-shrink-0">
                                <i class="fa-solid fa-car-side"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Kenderaan Ditetapkan</div>
                                <div class="text-xs font-black text-slate-900">
                                    <?php echo e($task->vehicle ? $task->vehicle->brand . ' ' . $task->vehicle->model : 'Belum Ditetapkan'); ?>

                                </div>
                                <div class="text-[11px] font-mono font-bold text-indigo-600">
                                    <?php echo e($task->vehicle?->plate_number ?? '-'); ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center gap-2">
                        <?php if($task->status === 'assigned'): ?>
                            <form action="<?php echo e(route('driver.tasks.accept', $task->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition shadow-md flex items-center">
                                    <i class="fa-solid fa-check mr-1.5"></i> Terima Tugasan
                                </button>
                            </form>
                        <?php elseif($task->status === 'driver_accepted'): ?>
                            <form action="<?php echo e(route('driver.tasks.start', $task->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs transition shadow-md flex items-center">
                                    <i class="fa-solid fa-play mr-1.5"></i> Mula Perjalanan
                                </button>
                            </form>
                            <?php if(!$task->handover): ?>
                                <a href="<?php echo e(route('handovers.create', $task->id)); ?>" class="px-4 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition flex items-center">
                                    <i class="fa-solid fa-key mr-1.5"></i> Rekod Ambil Kenderaan
                                </a>
                            <?php endif; ?>
                        <?php elseif($task->status === 'in_progress'): ?>
                            <form action="<?php echo e(route('driver.tasks.complete', $task->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs transition shadow-md flex items-center">
                                    <i class="fa-solid fa-flag-checkered mr-1.5"></i> Selesai Tugasan
                                </button>
                            </form>
                            <?php if(!$task->returnRecord): ?>
                                <a href="<?php echo e(route('handovers.return.create', $task->id)); ?>" class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition flex items-center">
                                    <i class="fa-solid fa-rotate-left mr-1.5"></i> Rekod Pulang & Meter
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <a href="<?php echo e(route('requests.show', $task->id)); ?>" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center">
                            <i class="fa-regular fa-eye mr-1.5"></i> Butiran Penuh
                        </a>
                        <a href="<?php echo e(route('incidents.create', ['request_id' => $task->id])); ?>" class="px-4 py-2.5 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition flex items-center border border-rose-200">
                            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Lapor Isu
                        </a>
                        <?php if($task->vehicle_id): ?>
                            <a href="<?php echo e(route('fuel.create', ['vehicle_id' => $task->vehicle_id, 'request_id' => $task->id])); ?>" class="px-4 py-2.5 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs transition flex items-center border border-amber-200">
                                <i class="fa-solid fa-gas-pump mr-1.5"></i> Rekod Minyak
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tiada tugasan dijadualkan hari ini</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Anda tiada sebarang penugasan kenderaan untuk hari ini. Sila semak tab 'Akan Datang' untuk jadual masa hadapan.
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB 2: TUGASAN AKAN DATANG -->
    <div x-show="activeTab === 'upcoming'" class="space-y-4" style="display: none;">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="fa-regular fa-calendar-days text-emerald-600"></i>
                Jadual Tugasan Masa Hadapan
            </h2>
            <span class="text-xs text-slate-500 font-semibold"><?php echo e($upcomingTasks->count()); ?> tugasan akan datang</span>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $upcomingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition p-5 sm:p-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-3">
                        <div class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 font-bold text-xs border border-emerald-200">
                            <i class="fa-regular fa-calendar mr-1"></i>
                            <?php echo e(\Carbon\Carbon::parse($task->start_date)->translatedFormat('d M Y')); ?>

                        </div>
                        <span class="text-xs font-mono font-bold text-slate-700">
                            <?php echo e(\Carbon\Carbon::parse($task->start_time)->format('h:i A')); ?> - <?php echo e(\Carbon\Carbon::parse($task->end_time)->format('h:i A')); ?>

                        </span>
                        <span class="text-xs text-slate-400 font-mono">(<?php echo e($task->request_number); ?>)</span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold border <?php echo e($task->status_badge['class']); ?>">
                        <?php echo e(strtoupper($task->status_badge['label'])); ?>

                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Tujuan & Destinasi</div>
                        <div class="text-sm font-black text-slate-900 mt-0.5"><?php echo e($task->purpose); ?></div>
                        <div class="text-xs text-slate-600 mt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-rose-500"></i> <?php echo e($task->origin); ?> → <?php echo e($task->destination); ?>

                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Pegawai & Kenderaan</div>
                        <div class="text-xs font-bold text-slate-800 mt-0.5">
                            <i class="fa-regular fa-user text-slate-400 mr-1"></i> <?php echo e($task->user?->name); ?> (<?php echo e($task->user?->department ?? 'ASM'); ?>)
                        </div>
                        <div class="text-xs font-mono font-bold text-indigo-700 mt-1">
                            <i class="fa-solid fa-car text-slate-400 mr-1"></i> <?php echo e($task->vehicle ? $task->vehicle->brand . ' ' . $task->vehicle->model . ' (' . $task->vehicle->plate_number . ')' : 'Belum ditetapkan'); ?>

                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Keperluan:
                        <?php if($task->need_smart_tag): ?><span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-700 mr-1">Smart Tag</span><?php endif; ?>
                        <?php if($task->need_fuel_card): ?><span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-700 mr-1">Kad Petrol</span><?php endif; ?>
                        <?php if($task->need_gps): ?><span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-700">GPS</span><?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if($task->status === 'assigned'): ?>
                            <form action="<?php echo e(route('driver.tasks.accept', $task->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition shadow-sm">
                                    <i class="fa-solid fa-check mr-1"></i> Terima Sekarang
                                </button>
                            </form>
                        <?php endif; ?>
                        <a href="<?php echo e(route('requests.show', $task->id)); ?>" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            Lihat Butiran
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tiada tugasan masa hadapan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Tiada jadual tugasan baharu yang ditugaskan kepada anda pada masa ini.
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB 3: SEJARAH TUGASAN SELESAI -->
    <div x-show="activeTab === 'completed'" class="space-y-4" style="display: none;">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-sky-600"></i>
                Sejarah Tugasan Selesai (10 Terkini)
            </h2>
            <span class="text-xs text-slate-500 font-semibold"><?php echo e($completedTasks->count()); ?> rekod</span>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-black border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">No. Permohonan</th>
                            <th class="py-3.5 px-4">Tarikh & Masa</th>
                            <th class="py-3.5 px-4">Tujuan & Destinasi</th>
                            <th class="py-3.5 px-4">Kenderaan</th>
                            <th class="py-3.5 px-4">Jarak (KM)</th>
                            <th class="py-3.5 px-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php $__empty_1 = true; $__currentLoopData = $completedTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    <?php echo e($task->request_number); ?>

                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800"><?php echo e(\Carbon\Carbon::parse($task->start_date)->format('d/m/Y')); ?></div>
                                    <div class="text-[11px] text-slate-500"><?php echo e(\Carbon\Carbon::parse($task->start_time)->format('h:i A')); ?></div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900"><?php echo e($task->purpose); ?></div>
                                    <div class="text-[11px] text-slate-500"><?php echo e($task->destination); ?></div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800"><?php echo e($task->vehicle?->model); ?></div>
                                    <div class="font-mono text-[11px] text-indigo-600 font-semibold"><?php echo e($task->vehicle?->plate_number ?? '-'); ?></div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php if($task->returnRecord): ?>
                                        <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                                            <?php echo e(number_format($task->returnRecord->total_km, 1)); ?> KM
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <a href="<?php echo e(route('requests.show', $task->id)); ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                        Butiran
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Tiada rekod tugasan selesai dijumpai.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/driver/tasks.blade.php ENDPATH**/ ?>