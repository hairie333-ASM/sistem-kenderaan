<?php $__env->startSection('title', 'Pemulangan Kenderaan (Pulang) - ' . $vehicleRequest->request_number); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6" x-data="returnCalculation()">
    <div>
        <a href="<?php echo e(route('requests.show', $vehicleRequest->id)); ?>" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Permohonan
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            REKOD PEMULANGAN KENDERAAN (PULANG)
        </h1>
        <p class="text-xs text-slate-500">
            Merekod bacaan meter akhir, pengiraan automatik jumlah kilometer, dan semakan pemulangan kelengkapan
        </p>
    </div>

    <!-- Summary Box -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-xs grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Permohonan</span>
            <div class="font-bold text-slate-900"><?php echo e($vehicleRequest->request_number); ?></div>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Kenderaan</span>
            <div class="font-bold text-slate-900"><?php echo e($vehicleRequest->vehicle?->plate_number); ?></div>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Pemandu</span>
            <div class="font-bold text-slate-900"><?php echo e($vehicleRequest->driver?->name ?? 'Pandu Sendiri'); ?></div>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Meter Semasa Ambil</span>
            <div class="font-mono font-bold text-slate-900"><?php echo e(number_format($vehicleRequest->handover?->start_mileage ?? 0)); ?> KM</div>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('handovers.return.store', $vehicleRequest->id)); ?>" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        <?php echo csrf_field(); ?>

        <!-- Tarikh, Masa & Pemulang -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Pemulangan <span class="text-rose-500">*</span></label>
                <input type="date" name="return_date" value="<?php echo e(old('return_date', \Carbon\Carbon::today()->format('Y-m-d'))); ?>" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Masa Pemulangan <span class="text-rose-500">*</span></label>
                <input type="time" name="return_time" value="<?php echo e(old('return_time', \Carbon\Carbon::now()->format('H:i'))); ?>" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Yang Memulangkan <span class="text-rose-500">*</span></label>
                <input type="text" name="returned_by_name" value="<?php echo e(old('returned_by_name', $vehicleRequest->driver?->name ?: $vehicleRequest->applicant_name)); ?>" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
            </div>
        </div>

        <!-- Meter Akhir & Auto Calculate Total KM (Section 17 requirement) -->
        <div class="p-5 rounded-2xl bg-emerald-50/70 border-2 border-emerald-300 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 uppercase text-[10px] mb-1">Meter Awal (KM)</label>
                    <div class="font-mono font-black text-lg text-slate-700 p-2.5 bg-white rounded-xl border border-emerald-200">
                        <?php echo e(number_format($vehicleRequest->handover?->start_mileage ?? 0)); ?>

                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase text-[10px] mb-1">
                        Meter Akhir (Odometer) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="return_mileage" x-model.number="returnMileage" @input="calculateKm()" required
                        class="w-full p-2.5 rounded-xl border border-emerald-400 font-mono font-black text-lg text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-black text-emerald-950 uppercase text-[10px] mb-1">
                        JUMLAH PERJALANAN (AUTO CALCULATE)
                    </label>
                    <div class="font-mono font-black text-xl text-emerald-800 p-2 bg-emerald-100/80 rounded-xl border border-emerald-300 flex items-center justify-between">
                        <span x-text="totalKm.toLocaleString()"></span>
                        <span class="text-xs uppercase font-sans">KM</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block font-bold text-slate-700 uppercase text-[10px] mb-1">
                        Aras Bahan Api Semasa Pulang <span class="text-rose-500">*</span>
                    </label>
                    <select name="fuel_level" required class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white font-bold">
                        <option value="Full (Penuh)">Full (Penuh 100%)</option>
                        <option value="3/4 (Tiga Suku)" selected>3/4 (Tiga Suku 75%)</option>
                        <option value="1/2 (Separuh)">1/2 (Separuh 50%)</option>
                        <option value="1/4 (Suku)">1/4 (Suku 25%)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Checklist Pemulangan Kelengkapan -->
        <div class="space-y-3">
            <label class="block font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                Semakan Pemulangan Kelengkapan
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="return_keys" value="1" checked class="rounded border-slate-300 text-emerald-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Kunci Dipulangkan</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="return_smart_tag" value="1" checked class="rounded border-slate-300 text-emerald-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Smart Tag Dipulangkan</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="return_fuel_card" value="1" checked class="rounded border-slate-300 text-emerald-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Kad Inden Dipulangkan</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="return_gps" value="1" checked class="rounded border-slate-300 text-emerald-600 w-4 h-4">
                    <span class="font-bold text-slate-800">GPS Dipulangkan</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Keadaan Kenderaan Semasa Pemulangan</label>
            <textarea name="condition_notes" rows="2" placeholder="Cth: Dipulangkan dalam keadaan baik dan tiada calar baharu..."
                class="w-full px-3 py-2 rounded-xl border border-slate-300"></textarea>
        </div>

        <!-- Issue / Damage Prompt Checkbox -->
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center space-x-3">
            <input type="checkbox" name="has_damage_incident" id="has_damage" value="1" class="rounded border-rose-300 text-rose-600 w-5 h-5">
            <label for="has_damage" class="font-bold text-rose-900 cursor-pointer">
                Terdapat Kerosakan / Isu Mekanikal / Kemalangan (Sistem akan membuka borang Lapor Isu selepas ini)
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="<?php echo e(route('requests.show', $vehicleRequest->id)); ?>" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-black shadow transition">
                Sahkan Pemulangan Kenderaan
            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function returnCalculation() {
        const start = <?php echo e($vehicleRequest->handover?->start_mileage ?? 0); ?>;
        return {
            startMileage: start,
            returnMileage: start + 75,
            totalKm: 75,
            calculateKm() {
                this.totalKm = Math.max(0, this.returnMileage - this.startMileage);
            }
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/handovers/return.blade.php ENDPATH**/ ?>