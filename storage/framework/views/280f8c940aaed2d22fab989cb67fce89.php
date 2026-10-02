<?php $__env->startSection('title', 'Laporan UPF'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-asm-100 text-asm-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-chart-pie mr-1"></i> Pusat Analisis & Laporan UPF
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                LAPORAN PENGGUNAAN KENDERAAN & PEMANDU
            </h1>
            <p class="text-xs text-slate-500">
                Penyata rasmi penggunaan kenderaan pejabat, tugasan pemandu, jarak perjalanan, dan kos bahan api ASM
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </button>
            <a href="<?php echo e(route('reports.export', request()->all())); ?>" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i> Eksport Excel (.CSV)
            </a>
        </div>
    </div>

    <!-- 9 Report Selector Tabs (Prompt Section 27) -->
    <div class="no-print bg-white p-2.5 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap gap-1.5 text-xs">
        <a href="<?php echo e(route('reports.index', ['type' => 'vehicle_usage'])); ?>" class="px-3 py-1.5 rounded-xl font-bold transition <?php echo e($type === 'vehicle_usage' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>">
            1. Penggunaan Kenderaan
        </a>
        <a href="<?php echo e(route('reports.index', ['type' => 'driver_tasks'])); ?>" class="px-3 py-1.5 rounded-xl font-bold transition <?php echo e($type === 'driver_tasks' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>">
            2. Tugasan Pemandu
        </a>
        <a href="<?php echo e(route('reports.index', ['type' => 'monthly'])); ?>" class="px-3 py-1.5 rounded-xl font-bold transition <?php echo e($type === 'monthly' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>">
            3. Analisis Bulanan
        </a>
        <a href="<?php echo e(route('reports.index', ['type' => 'mileage'])); ?>" class="px-3 py-1.5 rounded-xl font-bold transition <?php echo e($type === 'mileage' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>">
            4. Laporan Mileage (KM)
        </a>
        <a href="<?php echo e(route('reports.index', ['type' => 'fuel'])); ?>" class="px-3 py-1.5 rounded-xl font-bold transition <?php echo e($type === 'fuel' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>">
            5. Minyak & Kad Inden
        </a>
        <a href="<?php echo e(route('reports.index', ['type' => 'maintenance'])); ?>" class="px-3 py-1.5 rounded-xl font-bold transition <?php echo e($type === 'maintenance' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>">
            6. Penyelenggaraan
        </a>
        <a href="<?php echo e(route('reports.index', ['type' => 'incidents'])); ?>" class="px-3 py-1.5 rounded-xl font-bold transition <?php echo e($type === 'incidents' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>">
            7. Kerosakan & Kemalangan
        </a>
        <a href="<?php echo e(route('reports.index', ['type' => 'cancellations'])); ?>" class="px-3 py-1.5 rounded-xl font-bold transition <?php echo e($type === 'cancellations' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>">
            8. Pembatalan Permohonan
        </a>
        <a href="<?php echo e(route('reports.index', ['type' => 'officers'])); ?>" class="px-3 py-1.5 rounded-xl font-bold transition <?php echo e($type === 'officers' ? 'bg-asm-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>">
            9. Mengikut Pegawai
        </a>
    </div>

    <!-- Filters Box -->
    <div class="no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-xs">
        <form method="GET" action="<?php echo e(route('reports.index')); ?>" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input type="hidden" name="type" value="<?php echo e($type); ?>">
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Dari Tarikh</label>
                <input type="date" name="start_date" value="<?php echo e($startDate); ?>" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs">
            </div>
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Hingga Tarikh</label>
                <input type="date" name="end_date" value="<?php echo e($endDate); ?>" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs">
            </div>
            <div>
                <label class="block font-bold text-slate-600 uppercase text-[10px] mb-1">Pilih Kenderaan</label>
                <select name="vehicle_id" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs">
                    <option value="">Semua Kenderaan</option>
                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v->id); ?>" <?php echo e(request('vehicle_id') == $v->id ? 'selected' : ''); ?>><?php echo e($v->plate_number); ?> (<?php echo e($v->model); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-1.5 px-3 rounded-xl bg-slate-900 text-white font-bold transition">
                    Tapis Laporan
                </button>
                <a href="<?php echo e(route('reports.index', ['type' => $type])); ?>" class="py-1.5 px-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-center transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Report Table Display Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-4">
        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-slate-900 uppercase">
                    <?php if($type === 'vehicle_usage'): ?> LAPORAN PENGGUNAAN KENDERAAN
                    <?php elseif($type === 'driver_tasks'): ?> LAPORAN TUGASAN PEMANDU
                    <?php elseif($type === 'monthly'): ?> LAPORAN PENGGUNAAN MENGIKUT BULAN
                    <?php elseif($type === 'mileage'): ?> LAPORAN BACAAN METER & MILEAGE (TOTAL KM)
                    <?php elseif($type === 'fuel'): ?> LAPORAN MINYAK & KAD INDEN PETROL
                    <?php elseif($type === 'maintenance'): ?> LAPORAN PENYELENGGARAAN & SERVIS KENDERAAN
                    <?php elseif($type === 'incidents'): ?> LAPORAN KEMALANGAN & KEROSAKAN
                    <?php elseif($type === 'cancellations'): ?> LAPORAN PEMBATALAN PERMOHONAN
                    <?php elseif($type === 'officers'): ?> LAPORAN PENGGUNAAN MENGIKUT PEGAWAI / BAHAGIAN
                    <?php endif; ?>
                </h2>
                <div class="text-xs text-slate-500">Tempoh: <?php echo e($startDate); ?> hingga <?php echo e($endDate); ?></div>
            </div>
            <span class="text-xs font-mono font-bold bg-slate-100 px-2.5 py-1 rounded-lg">
                <?php echo e(is_countable($data) ? count($data) : 0); ?> Rekod
            </span>
        </div>

        <div class="overflow-x-auto text-xs">
            <?php if($type === 'vehicle_usage'): ?>
                <table class="w-full text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 font-bold uppercase text-[10px] text-slate-600">
                        <tr>
                            <th class="p-2.5">Kenderaan</th>
                            <th class="p-2.5">No. Plat</th>
                            <th class="p-2.5">Jenis</th>
                            <th class="p-2.5">Status</th>
                            <th class="p-2.5 text-center">Jumlah Perjalanan</th>
                            <th class="p-2.5 text-right">Jumlah KM</th>
                            <th class="p-2.5 text-right">Kos Minyak (RM)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 font-bold font-sans text-slate-900"><?php echo e($v->brand); ?> <?php echo e($v->model); ?></td>
                                <td class="p-2.5 font-black text-asm-800"><?php echo e($v->plate_number); ?></td>
                                <td class="p-2.5 font-sans"><?php echo e($v->type); ?></td>
                                <td class="p-2.5 font-sans">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($v->status_badge['class']); ?>"><?php echo e($v->status); ?></span>
                                </td>
                                <td class="p-2.5 text-center font-bold"><?php echo e($v->requests_count); ?></td>
                                <td class="p-2.5 text-right font-black text-emerald-700"><?php echo e(number_format($v->total_km)); ?> KM</td>
                                <td class="p-2.5 text-right font-bold text-slate-900">RM <?php echo e(number_format($v->fuel_cost, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php elseif($type === 'driver_tasks'): ?>
                <table class="w-full text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 font-bold uppercase text-[10px] text-slate-600">
                        <tr>
                            <th class="p-2.5">Nama Pemandu</th>
                            <th class="p-2.5">Jawatan</th>
                            <th class="p-2.5">Telefon</th>
                            <th class="p-2.5">Status</th>
                            <th class="p-2.5 text-center">Jumlah Tugasan</th>
                            <th class="p-2.5 text-center">Tugasan Selesai</th>
                            <th class="p-2.5 text-right">Jumlah KM Dipandu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 font-bold font-sans text-slate-900"><?php echo e($d->name); ?></td>
                                <td class="p-2.5 font-sans text-slate-600"><?php echo e($d->position); ?></td>
                                <td class="p-2.5 font-sans"><?php echo e($d->phone); ?></td>
                                <td class="p-2.5 font-sans">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($d->status_badge['class']); ?>"><?php echo e($d->status); ?></span>
                                </td>
                                <td class="p-2.5 text-center font-bold text-slate-900"><?php echo e($d->assignments_count); ?></td>
                                <td class="p-2.5 text-center font-bold text-emerald-700"><?php echo e($d->completed_tasks); ?></td>
                                <td class="p-2.5 text-right font-black text-asm-800"><?php echo e(number_format($d->total_km)); ?> KM</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php elseif($type === 'monthly'): ?>
                <table class="w-full text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 font-bold uppercase text-[10px] text-slate-600">
                        <tr>
                            <th class="p-2.5">Bulan</th>
                            <th class="p-2.5 text-center">Jumlah Permohonan</th>
                            <th class="p-2.5 text-center">Tugasan Selesai</th>
                            <th class="p-2.5 text-right">Jumlah KM Perjalanan</th>
                            <th class="p-2.5 text-right">Perbelanjaan Minyak (RM)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 font-bold font-sans text-slate-900"><?php echo e($m['month_name']); ?></td>
                                <td class="p-2.5 text-center font-bold"><?php echo e($m['requests_count']); ?></td>
                                <td class="p-2.5 text-center font-bold text-emerald-700"><?php echo e($m['completed_count']); ?></td>
                                <td class="p-2.5 text-right font-black text-slate-800"><?php echo e(number_format($m['total_km'])); ?> KM</td>
                                <td class="p-2.5 text-right font-bold text-slate-900">RM <?php echo e(number_format($m['fuel_cost'], 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php elseif($type === 'officers'): ?>
                <table class="w-full text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 font-bold uppercase text-[10px] text-slate-600">
                        <tr>
                            <th class="p-2.5">Nama Pegawai Memohon</th>
                            <th class="p-2.5">Bahagian / Unit</th>
                            <th class="p-2.5 text-center">Jumlah Permohonan</th>
                            <th class="p-2.5 text-center">Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans text-xs">
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 font-bold text-slate-900"><?php echo e($o->applicant_name); ?></td>
                                <td class="p-2.5 text-slate-600"><?php echo e($o->applicant_department); ?></td>
                                <td class="p-2.5 text-center font-mono font-bold"><?php echo e($o->total_requests); ?></td>
                                <td class="p-2.5 text-center font-mono font-bold text-emerald-700"><?php echo e($o->completed_requests); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="p-8 text-center text-slate-400">
                    Sila gunakan penapis di atas untuk menjana maklumat laporan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/reports/index.blade.php ENDPATH**/ ?>