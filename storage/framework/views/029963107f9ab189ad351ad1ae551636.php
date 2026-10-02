<?php $__env->startSection('title', 'Master Data Kenderaan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-asm-100 text-asm-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-car mr-1"></i> Pengurusan Kenderaan Jabatan UPF
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                MASTER DATA KENDERAAN
            </h1>
            <p class="text-xs text-slate-500">
                Pendaftaran rasmi kenderaan pejabat Akademi Sains Malaysia (ASM)
            </p>
        </div>

        <?php if(auth()->user()->isUpf()): ?>
            <a href="<?php echo e(route('vehicles.create')); ?>" class="px-4 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 self-start sm:self-auto">
                <i class="fa-solid fa-circle-plus"></i> + Tambah Kenderaan Baharu
            </a>
        <?php endif; ?>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Kenderaan</div>
            <div class="text-xl font-black text-slate-900 mt-0.5"><?php echo e($stats['total']); ?></div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Boleh Digunakan</div>
            <div class="text-xl font-black text-emerald-600 mt-0.5"><?php echo e($stats['available']); ?></div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Telah Ditetapkan</div>
            <div class="text-xl font-black text-blue-600 mt-0.5"><?php echo e($stats['assigned']); ?></div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Sedang Digunakan</div>
            <div class="text-xl font-black text-amber-600 mt-0.5"><?php echo e($stats['in_use']); ?></div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Penyelenggaraan</div>
            <div class="text-xl font-black text-purple-600 mt-0.5"><?php echo e($stats['maintenance']); ?></div>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Keluar Operasi</div>
            <div class="text-xl font-black text-rose-600 mt-0.5"><?php echo e($stats['out_of_service']); ?></div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="<?php echo e(route('vehicles.index')); ?>" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari No. Plat, Jenama, Model atau Kod..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <option value="">Semua Status</option>
                    <option value="Available" <?php echo e(request('status') === 'Available' ? 'selected' : ''); ?>>Available</option>
                    <option value="Assigned" <?php echo e(request('status') === 'Assigned' ? 'selected' : ''); ?>>Assigned</option>
                    <option value="In Use" <?php echo e(request('status') === 'In Use' ? 'selected' : ''); ?>>In Use</option>
                    <option value="Maintenance" <?php echo e(request('status') === 'Maintenance' ? 'selected' : ''); ?>>Maintenance</option>
                    <option value="Out of Service" <?php echo e(request('status') === 'Out of Service' ? 'selected' : ''); ?>>Out of Service</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs transition">
                    Tapis
                </button>
                <a href="<?php echo e(route('vehicles.index')); ?>" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs text-center transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Vehicles Cards Grid (Prompt Section 4 & 24) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-3xl border border-slate-200 hover:border-asm-300 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col justify-between group">
                <div>
                    <!-- Vehicle Card Header -->
                    <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider"><?php echo e($vehicle->vehicle_code); ?></span>
                            <div class="text-sm font-black text-slate-900"><?php echo e($vehicle->brand); ?> <?php echo e($vehicle->model); ?></div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($vehicle->status_badge['class']); ?>">
                            <?php echo e($vehicle->status_badge['label']); ?>

                        </span>
                    </div>

                    <!-- Vehicle Plate Banner -->
                    <div class="p-4 space-y-3">
                        <div class="text-center py-2 px-4 rounded-xl bg-slate-950 text-white font-mono font-black text-lg tracking-widest border-2 border-slate-800 shadow-inner">
                            <?php echo e($vehicle->plate_number); ?>

                        </div>

                        <!-- Specs List -->
                        <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                            <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[9px] font-bold uppercase text-slate-400 block">Jenis / Warna</span>
                                <span class="font-bold text-slate-800"><?php echo e($vehicle->type); ?> (<?php echo e($vehicle->year ?: '-'); ?>)</span>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[9px] font-bold uppercase text-slate-400 block">Bacaan Meter</span>
                                <span class="font-bold text-slate-900 font-mono"><?php echo e(number_format($vehicle->current_mileage)); ?> KM</span>
                            </div>
                        </div>

                        <!-- Roadtax & Servis -->
                        <div class="text-[11px] text-slate-500 space-y-1 pt-1">
                            <div class="flex items-center justify-between">
                                <span>Roadtax Tamat:</span>
                                <strong class="text-slate-700"><?php echo e($vehicle->roadtax_expiry ? $vehicle->roadtax_expiry->format('d/m/Y') : '-'); ?></strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Servis Seterusnya:</span>
                                <strong class="text-slate-700"><?php echo e($vehicle->next_service_mileage ? number_format($vehicle->next_service_mileage) . ' KM' : '-'); ?></strong>
                            </div>
                        </div>

                        <?php if($vehicle->notes): ?>
                            <div class="text-[11px] text-slate-600 bg-amber-50/60 p-2 rounded-xl border border-amber-200 italic line-clamp-2">
                                <?php echo e($vehicle->notes); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2 text-xs">
                    <a href="<?php echo e(route('vehicles.show', $vehicle->id)); ?>" class="flex-1 py-1.5 px-3 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-bold text-center transition">
                        Lihat Profil Penuh
                    </a>
                    <?php if(auth()->user()->isUpf()): ?>
                        <a href="<?php echo e(route('vehicles.edit', $vehicle->id)); ?>" class="p-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 transition" title="Kemaskini">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-3 p-12 text-center text-slate-400 bg-white rounded-3xl border border-slate-200">
                <i class="fa-solid fa-car text-4xl mb-2 text-slate-300"></i>
                <div class="text-sm font-bold text-slate-700">Tiada kenderaan dijumpai.</div>
            </div>
        <?php endif; ?>
    </div>

    <?php if($vehicles->hasPages()): ?>
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            <?php echo e($vehicles->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/vehicles/index.blade.php ENDPATH**/ ?>