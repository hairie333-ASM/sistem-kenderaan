<?php $__env->startSection('title', 'Laporan Kerosakan & Kemalangan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Pengurusan Kerosakan & Kemalangan Kenderaan
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                LAPORAN KEROSAKAN & KEMALANGAN
            </h1>
            <p class="text-xs text-slate-500">
                Pemantauan isu mekanikal kenderaan, kemalangan jalan raya, dan tindakan penyelenggaraan UPF
            </p>
        </div>

        <a href="<?php echo e(route('incidents.create')); ?>" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 self-start sm:self-auto">
            <i class="fa-solid fa-circle-plus"></i> + Lapor Isu Baharu
        </a>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Laporan</div>
            <div class="text-xl font-black text-slate-900 mt-0.5"><?php echo e($stats['total']); ?></div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Menunggu Tindakan</div>
            <div class="text-xl font-black text-amber-600 mt-0.5"><?php echo e($stats['reported']); ?></div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Sedang Dibaiki</div>
            <div class="text-xl font-black text-purple-600 mt-0.5"><?php echo e($stats['under_repair']); ?></div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Selesai Dibaik Pulih</div>
            <div class="text-xl font-black text-emerald-600 mt-0.5"><?php echo e($stats['resolved']); ?></div>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-100/75 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">No. Laporan</th>
                        <th class="p-3">Tarikh & Masa</th>
                        <th class="p-3">Kenderaan</th>
                        <th class="p-3">Jenis & Keterukan</th>
                        <th class="p-3">Keterangan Isu & Lokasi</th>
                        <th class="p-3">Pelapor</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 whitespace-nowrap font-bold text-rose-700">
                                <a href="<?php echo e(route('incidents.show', $inc->id)); ?>" class="hover:underline">
                                    <?php echo e($inc->report_number); ?>

                                </a>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900"><?php echo e($inc->incident_date->format('d/m/Y')); ?></div>
                                <div class="text-[10px] text-slate-400"><?php echo e($inc->incident_time); ?></div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900"><?php echo e($inc->vehicle?->brand); ?> <?php echo e($inc->vehicle?->model); ?></div>
                                <div class="font-mono text-[10px] text-slate-500 font-bold"><?php echo e($inc->vehicle?->plate_number); ?></div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-bold text-slate-900"><?php echo e($inc->incident_type); ?></span>
                                <div>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold <?php echo e($inc->severity === 'Kritikal' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800'); ?>">
                                        <?php echo e($inc->severity); ?>

                                    </span>
                                </div>
                            </td>
                            <td class="p-3 max-w-xs">
                                <div class="font-medium text-slate-900 line-clamp-1"><?php echo e($inc->description); ?></div>
                                <div class="text-[10px] text-slate-400 truncate">
                                    <i class="fa-solid fa-location-dot text-rose-500 mr-0.5"></i><?php echo e($inc->location); ?>

                                </div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-medium text-slate-800"><?php echo e($inc->reportedBy?->name ?? 'Pemandu'); ?></span>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($inc->status_badge['class']); ?>">
                                    <?php echo e($inc->status_badge['label']); ?>

                                </span>
                            </td>
                            <td class="p-3 whitespace-nowrap text-right">
                                <a href="<?php echo e(route('incidents.show', $inc->id)); ?>" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-asm-100 text-slate-700 hover:text-asm-800 font-bold transition">
                                    Butiran & Tindakan
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-circle-check text-3xl mb-2 text-emerald-500"></i>
                                <div>Tiada laporan kerosakan atau kemalangan aktif.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/incidents/index.blade.php ENDPATH**/ ?>