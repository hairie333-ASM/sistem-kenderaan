<?php $__env->startSection('title', 'Log Minyak & Kad Inden Petrol'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-gas-pump mr-1"></i> Pengurusan Kad Inden & Bahan Api
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                LOG MINYAK & RESIT KAD INDEN
            </h1>
            <p class="text-xs text-slate-500">
                Rekod perbelanjaan bahan api kenderaan jabatan dan penyerahan resit fizikal/digital
            </p>
        </div>

        <a href="<?php echo e(route('fuel.create')); ?>" class="px-4 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 self-start sm:self-auto">
            <i class="fa-solid fa-circle-plus"></i> + Rekod Isian Minyak
        </a>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Bahan Api (Liter)</div>
            <div class="text-2xl font-black text-slate-900 mt-1 font-mono"><?php echo e(number_format($stats['total_liters'], 2)); ?> L</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Keseluruhan kenderaan</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Perbelanjaan</div>
            <div class="text-2xl font-black text-emerald-700 mt-1 font-mono">RM <?php echo e(number_format($stats['total_cost'], 2)); ?></div>
            <div class="text-[10px] text-slate-500 mt-0.5">Kad Inden & Tunai</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Transaksi</div>
            <div class="text-2xl font-black text-amber-600 mt-1 font-mono"><?php echo e($stats['total_transactions']); ?></div>
            <div class="text-[10px] text-slate-500 mt-0.5">Resit direkodkan</div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="<?php echo e(route('fuel.index')); ?>" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Kenderaan</label>
                <select name="vehicle_id" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                    <option value="">Semua Kenderaan</option>
                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v->id); ?>" <?php echo e(request('vehicle_id') == $v->id ? 'selected' : ''); ?>><?php echo e($v->plate_number); ?> (<?php echo e($v->model); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Pemandu</label>
                <select name="driver_id" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                    <option value="">Semua Pemandu</option>
                    <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($d->id); ?>" <?php echo e(request('driver_id') == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Dari Tarikh</label>
                <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 text-white font-bold transition">
                    Tapis
                </button>
                <a href="<?php echo e(route('fuel.index')); ?>" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 font-bold transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200">
                <thead class="bg-slate-100/75 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Tarikh & Waktu</th>
                        <th class="p-3">Kenderaan</th>
                        <th class="p-3">Pemandu Bertugas</th>
                        <th class="p-3">Stesen Bahan Api</th>
                        <th class="p-3">Jenis & Liter</th>
                        <th class="p-3">Jumlah (RM)</th>
                        <th class="p-3">Kaedah Bayaran</th>
                        <th class="p-3 text-right">Resit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900"><?php echo e($log->log_date->format('d/m/Y')); ?></div>
                                <div class="text-[10px] text-slate-400"><?php echo e($log->log_time); ?></div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900"><?php echo e($log->vehicle?->brand); ?> <?php echo e($log->vehicle?->model); ?></div>
                                <div class="font-mono text-[10px] text-asm-800 font-bold"><?php echo e($log->vehicle?->plate_number); ?></div>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-medium text-slate-800"><?php echo e($log->driver?->name ?? 'Pegawai ASM'); ?></span>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900"><?php echo e($log->station_name); ?></div>
                                <?php if($log->mileage_at_fill): ?>
                                    <div class="text-[10px] text-slate-400 font-mono">Odometer: <?php echo e(number_format($log->mileage_at_fill)); ?> KM</div>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-bold text-slate-900"><?php echo e($log->liters); ?> L</span>
                                <span class="text-slate-500 text-[10px]">(<?php echo e($log->fuel_type); ?>)</span>
                                <div class="text-[10px] text-slate-400">@ RM <?php echo e(number_format($log->price_per_liter, 2)); ?>/L</div>
                            </td>
                            <td class="p-3 whitespace-nowrap font-mono font-black text-sm text-slate-900">
                                RM <?php echo e(number_format($log->total_amount, 2)); ?>

                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo e($log->payment_method === 'Kad Inden' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-700'); ?>">
                                    <?php echo e($log->payment_method); ?>

                                </span>
                            </td>
                            <td class="p-3 whitespace-nowrap text-right">
                                <?php if($log->receipt_photo_path): ?>
                                    <a href="/storage/<?php echo e($log->receipt_photo_path); ?>" target="_blank" class="px-2.5 py-1 rounded-lg bg-asm-50 hover:bg-asm-100 text-asm-700 font-bold text-xs">
                                        <i class="fa-solid fa-receipt mr-1"></i> Resit
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-400 italic text-[11px]">Tiada Fail</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-gas-pump text-3xl mb-2 text-slate-300"></i>
                                <div>Tiada log isian minyak direkodkan.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($logs->hasPages()): ?>
            <div class="p-4 border-t border-slate-200">
                <?php echo e($logs->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/fuel/index.blade.php ENDPATH**/ ?>