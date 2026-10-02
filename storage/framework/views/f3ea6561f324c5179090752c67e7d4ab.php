<?php $__env->startSection('title', 'Dashboard UPF'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-2">
                <span>DASHBOARD OPERASI UPF</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-asm-100 text-asm-800 border border-asm-300">
                    <?php echo e(\Carbon\Carbon::now()->translatedFormat('l, d F Y')); ?>

                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pusat Kawalan Kenderaan Rasmi & Penugasan Pemandu Unit Pengurusan Fasiliti (UPF)
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('schedules.excel')); ?>" class="inline-flex items-center px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                <i class="fa-solid fa-file-excel mr-1.5"></i> Jadual Format Excel
            </a>
            <a href="<?php echo e(route('requests.create')); ?>" class="inline-flex items-center px-3 py-2 rounded-xl bg-asm-950 hover:bg-asm-900 text-white text-xs font-bold shadow-sm transition">
                <i class="fa-solid fa-circle-plus mr-1.5"></i> Mohon Kenderaan
            </a>
        </div>
    </div>

    <!-- Metric Stat Cards (Matching prompt section 23) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Tugasan Hari Ini -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-asm-300 transition">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tugasan Hari Ini</div>
            <div class="text-2xl font-black text-slate-900 mt-1"><?php echo e($todayTasksCount); ?></div>
            <div class="text-[10px] text-emerald-600 mt-1 flex items-center font-medium">
                <i class="fa-solid fa-calendar-check mr-1"></i> Berjadual hari ini
            </div>
            <div class="absolute -right-2 -bottom-2 text-slate-100 group-hover:text-asm-50 transition pointer-events-none text-4xl">
                <i class="fa-solid fa-route"></i>
            </div>
        </div>

        <!-- Pemandu Bertugas -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-emerald-300 transition">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pemandu Bertugas</div>
            <div class="text-2xl font-black text-emerald-700 mt-1"><?php echo e($activeDriversCount); ?></div>
            <div class="text-[10px] text-slate-500 mt-1 flex items-center">
                <i class="fa-solid fa-id-badge mr-1 text-emerald-500"></i> Daripada 4 pemandu
            </div>
            <div class="absolute -right-2 -bottom-2 text-slate-100 group-hover:text-emerald-50 transition pointer-events-none text-4xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <!-- Kenderaan Digunakan -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-blue-300 transition">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kenderaan Bergerak</div>
            <div class="text-2xl font-black text-blue-700 mt-1"><?php echo e($vehiclesInUseCount); ?></div>
            <div class="text-[10px] text-slate-500 mt-1 flex items-center">
                <i class="fa-solid fa-car mr-1 text-blue-500"></i> Kenderaan bertugas
            </div>
            <div class="absolute -right-2 -bottom-2 text-slate-100 group-hover:text-blue-50 transition pointer-events-none text-4xl">
                <i class="fa-solid fa-car-side"></i>
            </div>
        </div>

        <!-- Permohonan Baru / Pending -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-amber-300 transition <?php echo e($pendingRequestsCount > 0 ? 'ring-2 ring-amber-400' : ''); ?>">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Permohonan Baru</div>
            <div class="text-2xl font-black text-amber-700 mt-1"><?php echo e($pendingRequestsCount); ?></div>
            <div class="text-[10px] text-amber-600 mt-1 flex items-center font-bold">
                <i class="fa-solid fa-clock mr-1"></i> Perlu tindakan UPF
            </div>
            <div class="absolute -right-2 -bottom-2 text-slate-100 group-hover:text-amber-50 transition pointer-events-none text-4xl">
                <i class="fa-solid fa-inbox"></i>
            </div>
        </div>

        <!-- Konflik Penugasan -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-rose-300 transition <?php echo e($conflictCount > 0 ? 'bg-rose-50/50 border-rose-300' : ''); ?>">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Konflik Waktu</div>
            <div class="text-2xl font-black <?php echo e($conflictCount > 0 ? 'text-rose-600' : 'text-slate-900'); ?> mt-1"><?php echo e($conflictCount); ?></div>
            <div class="text-[10px] <?php echo e($conflictCount > 0 ? 'text-rose-600 font-bold' : 'text-slate-400'); ?> mt-1 flex items-center">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i> <?php echo e($conflictCount > 0 ? 'Perlu semakan bertindih' : 'Tiada konflik'); ?>

            </div>
            <div class="absolute -right-2 -bottom-2 text-slate-100 group-hover:text-rose-50 transition pointer-events-none text-4xl">
                <i class="fa-solid fa-code-merge"></i>
            </div>
        </div>

        <!-- Kenderaan Penyelenggaraan -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-purple-300 transition">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Penyelenggaraan</div>
            <div class="text-2xl font-black text-purple-700 mt-1"><?php echo e($maintenanceVehiclesCount); ?></div>
            <div class="text-[10px] text-purple-600 mt-1 flex items-center font-medium">
                <i class="fa-solid fa-wrench mr-1"></i> Servis / Baiki
            </div>
            <div class="absolute -right-2 -bottom-2 text-slate-100 group-hover:text-purple-50 transition pointer-events-none text-4xl">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
        </div>
    </div>

    <!-- Main Operational Grid: Today's Tasks & Pending Applications -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Jadual Tugasan Hari Ini (Excel Styled Preview) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                        <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-900">
                            Jadual Tugasan Hari Ini (<?php echo e(\Carbon\Carbon::now()->translatedFormat('d M Y')); ?>)
                        </h2>
                    </div>
                    <a href="<?php echo e(route('schedules.excel')); ?>" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center">
                        Format Penuh Excel <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-200">
                        <thead class="bg-slate-100/75 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="p-3">Masa</th>
                                <th class="p-3">Tugasan & Lokasi</th>
                                <th class="p-3">Pegawai</th>
                                <th class="p-3">Pemandu</th>
                                <th class="p-3">Kenderaan</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php $__empty_1 = true; $__currentLoopData = $todayRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-3 whitespace-nowrap font-bold text-slate-900">
                                        <div class="flex items-center text-asm-700">
                                            <i class="fa-regular fa-clock mr-1 text-xs"></i>
                                            <?php echo e(\Carbon\Carbon::parse($task->start_time)->format('h:i A')); ?>

                                        </div>
                                        <div class="text-[10px] text-slate-400">hingga <?php echo e(\Carbon\Carbon::parse($task->end_time)->format('h:i A')); ?></div>
                                    </td>
                                    <td class="p-3 max-w-xs">
                                        <div class="font-bold text-slate-900 line-clamp-1"><?php echo e($task->purpose); ?></div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                            <span class="truncate"><?php echo e($task->origin); ?> → <?php echo e($task->destination); ?></span>
                                        </div>
                                    </td>
                                    <td class="p-3 whitespace-nowrap text-slate-700 font-medium">
                                        <?php echo e($task->applicant_name); ?>

                                        <div class="text-[10px] text-slate-400"><?php echo e($task->applicant_department); ?></div>
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        <?php if($task->driver): ?>
                                            <span class="font-bold text-slate-900 flex items-center">
                                                <i class="fa-solid fa-id-card text-emerald-600 mr-1 text-xs"></i>
                                                <?php echo e($task->driver->name); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-rose-600 font-bold text-[11px] bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                                Belum Ditetapkan
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        <?php if($task->vehicle): ?>
                                            <span class="font-bold text-slate-900"><?php echo e($task->vehicle->model); ?></span>
                                            <div class="text-[10px] font-mono text-slate-500"><?php echo e($task->vehicle->plate_number); ?></div>
                                        <?php else: ?>
                                            <span class="text-rose-600 font-bold text-[11px] bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                                Belum Ditetapkan
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($task->status_badge['class']); ?>">
                                            <?php echo e($task->status_badge['label']); ?>

                                        </span>
                                    </td>
                                    <td class="p-3 whitespace-nowrap text-right space-x-1">
                                        <a href="<?php echo e(route('requests.show', $task->id)); ?>" class="p-1.5 rounded-lg bg-slate-100 hover:bg-asm-100 text-slate-700 hover:text-asm-800 transition" title="Lihat Butiran">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <?php if(!$task->assigned_driver_id || !$task->assigned_vehicle_id): ?>
                                            <a href="<?php echo e(route('upf.assign.show', $task->id)); ?>" class="p-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold transition" title="Tetapkan Penugasan">
                                                <i class="fa-solid fa-user-plus text-xs"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-400">
                                        <i class="fa-solid fa-calendar-xmark text-3xl mb-2 text-slate-300"></i>
                                        <div>Tiada tugasan kenderaan berjadual untuk hari ini.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Upcoming Tasks (Next 7 Days) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-900 flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar-days text-asm-600"></i>
                        Tugasan Akan Datang (Minggu Ini)
                    </h2>
                    <a href="<?php echo e(route('schedules.weekly')); ?>" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center">
                        Jadual Mingguan <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $upcomingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3.5 hover:bg-slate-50 transition flex items-center justify-between gap-4">
                            <div class="flex items-start space-x-3">
                                <div class="bg-asm-50 border border-asm-200 text-asm-800 rounded-xl p-2 text-center min-w-[55px] shrink-0">
                                    <div class="text-[10px] uppercase font-bold text-asm-600"><?php echo e($task->start_date->translatedFormat('M')); ?></div>
                                    <div class="text-lg font-black leading-none"><?php echo e($task->start_date->format('d')); ?></div>
                                </div>
                                <div>
                                    <div class="font-bold text-xs text-slate-900"><?php echo e($task->purpose); ?></div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
                                        <span><i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i> <?php echo e($task->destination); ?></span>
                                        <span>•</span>
                                        <span><i class="fa-regular fa-clock text-slate-400 text-[10px]"></i> <?php echo e(\Carbon\Carbon::parse($task->start_time)->format('h:i A')); ?></span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 mt-1">
                                        Pemandu: <span class="font-bold text-slate-800"><?php echo e($task->driver?->name ?? 'Belum Ditugaskan'); ?></span>
                                        | Kenderaan: <span class="font-bold text-slate-800"><?php echo e($task->vehicle?->plate_number ?? 'Belum Ditugaskan'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($task->status_badge['class']); ?>">
                                    <?php echo e($task->status_badge['label']); ?>

                                </span>
                                <div class="mt-2">
                                    <a href="<?php echo e(route('requests.show', $task->id)); ?>" class="text-xs font-bold text-asm-600 hover:underline">Butiran</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-6 text-center text-xs text-slate-400">Tiada tugasan akan datang.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Permohonan Menunggu Tindakan UPF & Insiden -->
        <div class="space-y-4">
            <!-- Permohonan Menunggu Penugasan -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-amber-50/70 border-b border-amber-200 flex items-center justify-between">
                    <h2 class="text-xs font-black uppercase tracking-wider text-amber-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-bell text-amber-600"></i>
                        Permohonan Perlu Semakan
                    </h2>
                    <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-bold text-[10px]">
                        <?php echo e($pendingRequests->count()); ?> Permohonan
                    </span>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3.5 hover:bg-slate-50 transition space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-asm-800"><?php echo e($req->request_number); ?></span>
                                <span class="text-[10px] text-slate-400"><?php echo e($req->created_at->diffForHumans()); ?></span>
                            </div>
                            <div class="text-xs font-bold text-slate-900 leading-snug"><?php echo e($req->purpose); ?></div>
                            <div class="text-[11px] text-slate-500 space-y-0.5">
                                <div><i class="fa-solid fa-user text-slate-400 w-4"></i> <?php echo e($req->applicant_name); ?> (<?php echo e($req->applicant_department); ?>)</div>
                                <div><i class="fa-solid fa-calendar text-slate-400 w-4"></i> <?php echo e($req->start_date->format('d/m/Y')); ?> (<?php echo e(\Carbon\Carbon::parse($req->start_time)->format('h:i A')); ?>)</div>
                                <div><i class="fa-solid fa-location-dot text-rose-500 w-4"></i> <?php echo e($req->destination); ?></div>
                            </div>
                            <?php if($req->is_short_notice): ?>
                                <div class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded border border-amber-200">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Notis Singkat (Kurang 1 hari)
                                </div>
                            <?php endif; ?>
                            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                                <a href="<?php echo e(route('requests.show', $req->id)); ?>" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                    Semak
                                </a>
                                <a href="<?php echo e(route('upf.assign.show', $req->id)); ?>" class="px-2.5 py-1 rounded-lg bg-asm-950 hover:bg-asm-900 text-white text-xs font-bold transition">
                                    Tetapkan Pemandu & Kereta
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-6 text-center text-xs text-slate-400">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-1"></i>
                            <div>Semua permohonan telah selesai disemak!</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Isu Kerosakan & Kemalangan Terkini -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                        Status Kerosakan & Isu Kenderaan
                    </h2>
                    <a href="<?php echo e(route('incidents.index')); ?>" class="text-[11px] font-bold text-asm-600 hover:underline">Semua</a>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $activeIncidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 hover:bg-slate-50 transition space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-900"><?php echo e($inc->vehicle?->plate_number); ?> (<?php echo e($inc->vehicle?->model); ?>)</span>
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold <?php echo e($inc->status_badge['class']); ?>">
                                    <?php echo e($inc->status_badge['label']); ?>

                                </span>
                            </div>
                            <div class="text-xs text-slate-600 line-clamp-2"><?php echo e($inc->description); ?></div>
                            <div class="text-[10px] text-slate-400 flex items-center justify-between pt-1">
                                <span>Jenis: <strong class="text-slate-700"><?php echo e($inc->incident_type); ?></strong></span>
                                <a href="<?php echo e(route('incidents.show', $inc->id)); ?>" class="text-asm-600 font-bold hover:underline">Kemaskini</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-4 text-center text-xs text-slate-400">Tiada kerosakan atau kemalangan aktif dilaporkan.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Master Shortcuts -->
            <div class="bg-slate-900 text-white rounded-2xl p-4 shadow-sm space-y-3">
                <div class="text-xs font-bold uppercase tracking-wider text-amber-400">Akses Pantas UPF</div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="<?php echo e(route('vehicles.create')); ?>" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition text-center font-bold">
                        <i class="fa-solid fa-car mb-1 block text-base text-sky-400"></i> + Kenderaan
                    </a>
                    <a href="<?php echo e(route('drivers.create')); ?>" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition text-center font-bold">
                        <i class="fa-solid fa-id-card mb-1 block text-base text-emerald-400"></i> + Pemandu
                    </a>
                    <a href="<?php echo e(route('schedules.import')); ?>" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition text-center font-bold">
                        <i class="fa-solid fa-file-import mb-1 block text-base text-amber-400"></i> Import Excel
                    </a>
                    <a href="<?php echo e(route('reports.index')); ?>" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition text-center font-bold">
                        <i class="fa-solid fa-file-lines mb-1 block text-base text-rose-400"></i> Laporan (9)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/dashboard/upf.blade.php ENDPATH**/ ?>