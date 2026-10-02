<?php $__env->startSection('title', 'Penugasan UPF: ' . $vehicleRequest->request_number); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="<?php echo e(route('requests.show', $vehicleRequest->id)); ?>" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Butiran Permohonan
            </a>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-2">
                <span>PENUGASAN PEMANDU & KENDERAAN (UPF)</span>
            </h1>
            <p class="text-xs text-slate-500">
                Menetapkan pemandu bertugas, kenderaan armada, dan kelengkapan bagi permohonan <?php echo e($vehicleRequest->request_number); ?>

            </p>
        </div>
    </div>

    <!-- Conflict Warning Alert (From Session) -->
    <?php if(session('conflict_error')): ?>
        <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-400 text-rose-950 shadow-md flex items-start space-x-3 animate-pulse">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-2xl mt-0.5 shrink-0"></i>
            <div class="text-xs space-y-1">
                <div class="font-black text-sm uppercase text-rose-900">AMARAN KONFLIK PENUGASAN (CONFLICT DETECTED)</div>
                <p class="font-bold text-rose-800"><?php echo e(session('conflict_error')['message']); ?></p>
                <p class="text-slate-600">
                    Sistem mengesan pertindihan slot masa dengan tugasan sedia ada. Sekiranya UPF ingin meneruskan juga (contoh: pemandu boleh kembali tepat pada waktu atau urusan dekat), sila tandakan kotak <strong>"Sahkan Override Konflik Penugasan"</strong> di bawah berserta alasan.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Request Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <span class="text-xs font-black uppercase text-slate-700">Ringkasan Permohonan: <?php echo e($vehicleRequest->request_number); ?></span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($vehicleRequest->status_badge['class']); ?>">
                <?php echo e($vehicleRequest->status_badge['label']); ?>

            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px]">Pemohon:</span>
                <div class="font-bold text-slate-900"><?php echo e($vehicleRequest->applicant_name); ?></div>
                <div class="text-slate-500 text-[11px]"><?php echo e($vehicleRequest->applicant_department); ?></div>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px]">Tarikh & Waktu Diperlukan:</span>
                <div class="font-bold text-slate-900"><?php echo e($vehicleRequest->start_date->format('d/m/Y')); ?></div>
                <div class="text-slate-700 font-medium">
                    <?php echo e(\Carbon\Carbon::parse($vehicleRequest->start_time)->format('h:i A')); ?> - <?php echo e(\Carbon\Carbon::parse($vehicleRequest->end_time)->format('h:i A')); ?>

                </div>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase text-[10px]">Destinasi:</span>
                <div class="font-bold text-slate-900"><?php echo e($vehicleRequest->destination); ?></div>
                <div class="text-[11px] text-slate-500 truncate">Ambil di: <?php echo e($vehicleRequest->origin); ?></div>
            </div>
        </div>

        <div class="pt-2 border-t border-slate-100 text-xs">
            <span class="text-slate-400 font-bold uppercase text-[10px]">Tujuan Urusan:</span>
            <div class="font-medium text-slate-800"><?php echo e($vehicleRequest->purpose); ?></div>
        </div>
    </div>

    <!-- Assignment Form -->
    <form method="POST" action="<?php echo e(route('upf.assign', $vehicleRequest->id)); ?>" class="space-y-6"
          x-data="{ 
              driverId: '<?php echo e(old('assigned_driver_id', $vehicleRequest->assigned_driver_id ?? '')); ?>', 
              remarks: '<?php echo e(addslashes(old('upf_remarks', $vehicleRequest->upf_remarks ?? ''))); ?>' 
          }">
        <?php echo csrf_field(); ?>

        <?php if(!$vehicleRequest->need_driver): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 flex items-start gap-3">
                <i class="fa-solid fa-car-side text-emerald-600 text-lg mt-0.5 shrink-0"></i>
                <div class="text-xs">
                    <strong class="font-extrabold text-sm block">Pemohon Memohon Pandu Sendiri (Tanpa Pemandu)</strong>
                    <p class="text-emerald-800 mt-0.5">Pemohon telah menyatakan dalam borang permohonan bahawa kenderaan akan dipandu sendiri oleh pemohon. Sila pilih kenderaan dan kelengkapan.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="border-b border-slate-100 pb-3 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">
                    PENETAPAN PEMANDU, KENDERAAN & KELENGKAPAN
                </h2>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="driverId = ''; remarks = 'Dimaklumkan bahawa atas ketiadaan pemandu bagi tarikh dan masa yang dimohon, pihak UPF meluluskan kenderaan jabatan diberikan kepada pemohon untuk dipandu sendiri.'"
                            class="px-3 py-1 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-extrabold text-[11px] border border-amber-300 transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-user-xmark text-amber-700"></i> Ketiadaan Pemandu (Serah Kereta Kepada Pemohon)
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <!-- Driver Selection -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block font-bold text-slate-800 uppercase tracking-wider">
                            Pilih Pemandu Bertugas
                        </label>
                        <span x-show="driverId === ''" class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                            Pandu Sendiri
                        </span>
                    </div>

                    <select name="assigned_driver_id" x-model="driverId" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600 font-medium">
                        <option value="">-- Ketiadaan Pemandu / Pandu Sendiri (Serah Kenderaan Kepada Pemohon) --</option>
                        <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($d->id); ?>">
                                <?php echo e($d->name); ?> [<?php echo e($d->is_available_for_trip ? 'Boleh Bertugas' : ($d->conflict_task ? 'KONFLIK: ' . $d->conflict_task->request_number : $d->status)); ?>]
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <!-- Alert when no driver is selected -->
                    <div x-show="driverId === ''" x-cloak class="p-3 rounded-xl bg-amber-50 border border-amber-300 text-amber-950 space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-xs">
                            <i class="fa-solid fa-circle-info text-amber-600"></i>
                            <span>Maklum Balas: Kenderaan Diberikan Kepada Pemohon</span>
                        </div>
                        <p class="text-[11px] text-amber-900 leading-relaxed">
                            Tiada pemandu UPF ditugaskan. Kenderaan armada yang dipilih di bawah akan diserahkan terus kepada pemohon (<strong><?php echo e($vehicleRequest->applicant_name); ?></strong>) untuk dipandu sendiri.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1 text-[11px]">
                        <span class="font-bold text-slate-700 uppercase text-[10px] block">Status Ketersediaan Pemandu:</span>
                        <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between py-0.5">
                                <span class="text-slate-700"><?php echo e($d->name); ?></span>
                                <?php if($d->is_available_for_trip): ?>
                                    <span class="text-emerald-700 font-bold">✓ Boleh Bertugas</span>
                                <?php elseif($d->conflict_task): ?>
                                    <span class="text-rose-600 font-bold" title="Tugasan: <?php echo e($d->conflict_task->purpose); ?>">
                                        ⚠ Bertindih (<?php echo e($d->conflict_task->request_number); ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="text-amber-700 font-bold"><?php echo e($d->status); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- Vehicle Selection -->
                <div class="space-y-2">
                    <label class="block font-bold text-slate-800 uppercase tracking-wider">
                        Pilih Kenderaan Armada <span class="text-rose-500">*</span>
                    </label>
                    <select name="assigned_vehicle_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600">
                        <option value="">-- Pilih Kenderaan --</option>
                        <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($v->id); ?>" <?php echo e(old('assigned_vehicle_id', $vehicleRequest->assigned_vehicle_id) == $v->id ? 'selected' : ''); ?>>
                                <?php echo e($v->brand); ?> <?php echo e($v->model); ?> (<?php echo e($v->plate_number); ?>) [<?php echo e($v->is_available_for_trip ? 'Available' : ($v->conflict_task ? 'KONFLIK: ' . $v->conflict_task->request_number : $v->status)); ?>]
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1 text-[11px]">
                        <span class="font-bold text-slate-700 uppercase text-[10px] block">Status Ketersediaan Kenderaan:</span>
                        <div class="max-h-36 overflow-y-auto divide-y divide-slate-100 pr-1">
                            <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between py-1">
                                    <span class="font-medium text-slate-800"><?php echo e($v->model); ?> <span class="font-mono text-slate-500">(<?php echo e($v->plate_number); ?>)</span></span>
                                    <?php if($v->is_available_for_trip): ?>
                                        <span class="text-emerald-700 font-bold">✓ Tersedia</span>
                                    <?php elseif($v->status === 'Maintenance'): ?>
                                        <span class="text-purple-700 font-bold">🔧 Penyelenggaraan</span>
                                    <?php elseif($v->conflict_task): ?>
                                        <span class="text-rose-600 font-bold">⚠ Bertindih (<?php echo e($v->conflict_task->request_number); ?>)</span>
                                    <?php else: ?>
                                        <span class="text-slate-500 font-bold"><?php echo e($v->status); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipment Checklist -->
            <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                <span class="block font-bold text-slate-800 uppercase tracking-wider">
                    Kelengkapan Yang Disediakan Oleh UPF
                </span>
                <div class="grid grid-cols-3 gap-3">
                    <label class="p-3 rounded-xl border border-slate-300 hover:border-asm-500 cursor-pointer transition flex items-center space-x-2">
                        <input type="checkbox" name="assigned_smart_tag" value="1" <?php echo e(old('assigned_smart_tag', $vehicleRequest->assigned_smart_tag ?? $vehicleRequest->need_smart_tag) ? 'checked' : ''); ?> class="rounded border-slate-300 text-asm-600 w-4 h-4">
                        <span class="font-bold text-slate-800">Smart Tag</span>
                    </label>

                    <label class="p-3 rounded-xl border border-slate-300 hover:border-asm-500 cursor-pointer transition flex items-center space-x-2">
                        <input type="checkbox" name="assigned_fuel_card" value="1" <?php echo e(old('assigned_fuel_card', $vehicleRequest->assigned_fuel_card ?? $vehicleRequest->need_fuel_card) ? 'checked' : ''); ?> class="rounded border-slate-300 text-asm-600 w-4 h-4">
                        <span class="font-bold text-slate-800">Kad Inden Petrol</span>
                    </label>

                    <label class="p-3 rounded-xl border border-slate-300 hover:border-asm-500 cursor-pointer transition flex items-center space-x-2">
                        <input type="checkbox" name="assigned_gps" value="1" <?php echo e(old('assigned_gps', $vehicleRequest->assigned_gps ?? $vehicleRequest->need_gps) ? 'checked' : ''); ?> class="rounded border-slate-300 text-asm-600 w-4 h-4">
                        <span class="font-bold text-slate-800">GPS Navigation</span>
                    </label>
                </div>
            </div>

            <!-- Catatan UPF -->
            <div class="text-xs space-y-1">
                <label class="block font-bold text-slate-800 uppercase tracking-wider">Catatan Arahan UPF</label>
                <textarea name="upf_remarks" x-model="remarks" rows="2" placeholder="Cth: Sila ambil pemohon di lobi MATRADE tepat jam 7:15 AM..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-asm-600"></textarea>
            </div>

            <!-- OVERRIDE SECTION (Prompt requirements 15 & 35) -->
            <div class="p-4 rounded-xl bg-amber-50/70 border-2 border-amber-300 space-y-3 text-xs">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="override_conflict" id="override_conflict" value="1" class="rounded border-amber-400 text-amber-600 w-4 h-4">
                    <label for="override_conflict" class="font-bold text-amber-950 uppercase cursor-pointer">
                        Sahkan Override Konflik Penugasan (UPF Override Permission)
                    </label>
                </div>
                <div class="text-[11px] text-slate-600">
                    Tandakan pilihan ini sekiranya anda ingin meluluskan penugasan walaupun terdapat amaran pertindihan masa pemandu atau kenderaan.
                </div>
                <div>
                    <input type="text" name="override_conflict_reason" placeholder="Nyatakan justifikasi pelepasan khas / override..."
                        class="w-full px-3 py-2 rounded-xl border border-amber-300 text-xs focus:ring-2 focus:ring-amber-500 bg-white">
                </div>
            </div>

            <!-- Action Submit Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="<?php echo e(route('requests.show', $vehicleRequest->id)); ?>" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black text-xs shadow-lg hover:shadow-xl transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan & Sahkan Penugasan</span>
                </button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/upf/assign.blade.php ENDPATH**/ ?>