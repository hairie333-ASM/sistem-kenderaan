<?php $__env->startSection('title', 'Jadual Tugasan Pemandu - Format Excel UPF'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-file-excel mr-1"></i> Format Jadual Excel UPF
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                JADUAL TUGASAN PEMANDU - UPF
            </h1>
            <p class="text-xs text-slate-500">
                Paparan rasmi berstruktur format spreadsheet Excel sedia ada Unit Pengurusan Fasiliti
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <button onclick="window.print()" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak Jadual
            </button>
            <a href="<?php echo e(route('schedules.export', request()->all())); ?>" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-csv"></i> Eksport Excel (.CSV)
            </a>
            <?php if(auth()->user()->isUpf()): ?>
                <a href="<?php echo e(route('schedules.import')); ?>" class="px-3 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-import"></i> Import Excel Lama
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-3 text-xs">
        <form method="GET" action="<?php echo e(route('schedules.excel')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Dari Tarikh</label>
                <input type="date" name="start_date" value="<?php echo e(request('start_date', $startDate)); ?>"
                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs">
            </div>
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Hingga Tarikh</label>
                <input type="date" name="end_date" value="<?php echo e(request('end_date', $endDate)); ?>"
                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs">
            </div>
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Pemandu</label>
                <select name="driver_id" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs">
                    <option value="">Semua Pemandu</option>
                    <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($d->id); ?>" <?php echo e(request('driver_id') == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Kenderaan</label>
                <select name="vehicle_id" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs">
                    <option value="">Semua Kenderaan</option>
                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v->id); ?>" <?php echo e(request('vehicle_id') == $v->id ? 'selected' : ''); ?>><?php echo e($v->plate_number); ?> (<?php echo e($v->model); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Status</label>
                <select name="status" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs">
                    <option value="">Semua Status</option>
                    <option value="assigned" <?php echo e(request('status') === 'assigned' ? 'selected' : ''); ?>>Assigned</option>
                    <option value="driver_accepted" <?php echo e(request('status') === 'driver_accepted' ? 'selected' : ''); ?>>Driver Accepted</option>
                    <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                    <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                </select>
            </div>
            <div class="flex items-end gap-1.5">
                <button type="submit" class="w-full py-1.5 px-3 rounded-lg bg-asm-950 hover:bg-asm-900 text-white font-bold text-xs transition">
                    <i class="fa-solid fa-filter mr-1"></i> Tapis
                </button>
                <a href="<?php echo e(route('schedules.excel')); ?>" class="py-1.5 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Excel Spreadsheet Styled Table (Exact Format as requested) -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-sm overflow-hidden">
        <div class="p-3 bg-emerald-900 text-white flex items-center justify-between text-xs">
            <span class="font-mono font-bold tracking-wider uppercase">JADUAL TUGASAN PEMANDU - UNIT PENGURUSAN FASILITI (UPF)</span>
            <span class="bg-emerald-800 px-2 py-0.5 rounded text-[11px] font-bold">
                <?php echo e($schedules->count()); ?> Rekod Dijumpai
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-slate-200 border-collapse">
                <thead class="bg-slate-100 text-slate-800 font-black uppercase tracking-wider text-[10px] border-b border-slate-300">
                    <tr>
                        <th class="p-2.5 border-r border-slate-200 w-12 text-center">NO.</th>
                        <th class="p-2.5 border-r border-slate-200 whitespace-nowrap">TARIKH</th>
                        <th class="p-2.5 border-r border-slate-200 whitespace-nowrap">MASA AMBIL</th>
                        <th class="p-2.5 border-r border-slate-200">TUGASAN</th>
                        <th class="p-2.5 border-r border-slate-200">LOKASI (DARI → KE)</th>
                        <th class="p-2.5 border-r border-slate-200 whitespace-nowrap">MASA TIBA</th>
                        <th class="p-2.5 border-r border-slate-200 whitespace-nowrap">PEGAWAI MEMOHON</th>
                        <th class="p-2.5 border-r border-slate-200 whitespace-nowrap">PEMANDU BERTUGAS</th>
                        <th class="p-2.5 border-r border-slate-200 whitespace-nowrap">KENDERAAN</th>
                        <th class="p-2.5 text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-mono text-[11px]">
                    <?php $__empty_1 = true; $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-amber-50/50 transition <?php echo e($idx % 2 == 1 ? 'bg-slate-50/50' : 'bg-white'); ?>">
                            <td class="p-2.5 border-r border-slate-200 text-center font-bold text-slate-500">
                                <?php echo e($idx + 1); ?>

                            </td>
                            <td class="p-2.5 border-r border-slate-200 whitespace-nowrap font-bold text-slate-800">
                                <?php echo e($row->start_date->format('d M Y')); ?>

                            </td>
                            <td class="p-2.5 border-r border-slate-200 whitespace-nowrap font-bold text-asm-800">
                                <?php echo e(\Carbon\Carbon::parse($row->start_time)->format('h:i A')); ?>

                            </td>
                            <td class="p-2.5 border-r border-slate-200 font-sans max-w-xs">
                                <a href="<?php echo e(route('requests.show', $row->id)); ?>" class="font-bold text-slate-900 hover:text-asm-600 hover:underline">
                                    <?php echo e($row->purpose); ?>

                                </a>
                                <div class="text-[10px] text-slate-400 font-mono"><?php echo e($row->request_number); ?></div>
                            </td>
                            <td class="p-2.5 border-r border-slate-200 font-sans text-slate-700">
                                <span class="text-slate-500"><?php echo e($row->origin); ?></span>
                                <span class="text-slate-400 mx-1">→</span>
                                <strong class="text-slate-900"><?php echo e($row->destination); ?></strong>
                            </td>
                            <td class="p-2.5 border-r border-slate-200 whitespace-nowrap text-slate-700">
                                <?php echo e($row->arrival_time ? \Carbon\Carbon::parse($row->arrival_time)->format('h:i A') : '-'); ?>

                            </td>
                            <td class="p-2.5 border-r border-slate-200 whitespace-nowrap font-sans font-medium text-slate-800">
                                <?php echo e($row->applicant_name); ?>

                            </td>
                            <td class="p-2.5 border-r border-slate-200 whitespace-nowrap font-sans">
                                <?php if($row->driver): ?>
                                    <strong class="text-slate-900"><?php echo e($row->driver->name); ?></strong>
                                <?php else: ?>
                                    <span class="text-rose-500 italic">Belum Ditetapkan</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-2.5 border-r border-slate-200 whitespace-nowrap font-sans">
                                <?php if($row->vehicle): ?>
                                    <span class="font-bold text-slate-900"><?php echo e($row->vehicle->model); ?></span>
                                    <span class="text-slate-500 font-mono text-[10px]">(<?php echo e($row->vehicle->plate_number); ?>)</span>
                                <?php else: ?>
                                    <span class="text-rose-500 italic">Belum Ditetapkan</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-2.5 text-center whitespace-nowrap font-sans">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($row->status_badge['class']); ?>">
                                    <?php echo e($row->status_badge['label']); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-400 font-sans">
                                Tiada rekod jadual pemandu mengikut kriteria penapisan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/schedules/excel-view.blade.php ENDPATH**/ ?>