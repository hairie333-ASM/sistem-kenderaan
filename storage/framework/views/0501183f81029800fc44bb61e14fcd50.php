<?php $__env->startSection('title', 'Serahan Kenderaan (Ambil) - ' . $vehicleRequest->request_number); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="<?php echo e(route('requests.show', $vehicleRequest->id)); ?>" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Permohonan
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            REKOD SERAHAN KENDERAAN (AMBIL)
        </h1>
        <p class="text-xs text-slate-500">
            Rujukan Format: ASM/UPFIT/PERMOHONAN KENDERAAN PEJABAT (BAHAGIAN C & D)
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
            <div class="font-bold text-slate-900"><?php echo e($vehicleRequest->vehicle?->plate_number); ?> (<?php echo e($vehicleRequest->vehicle?->model); ?>)</div>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Pemandu</span>
            <div class="font-bold text-slate-900"><?php echo e($vehicleRequest->driver?->name ?? 'Pandu Sendiri'); ?></div>
        </div>
        <div>
            <span class="text-slate-400 font-bold uppercase text-[9px]">Pegawai Pemohon</span>
            <div class="font-bold text-slate-900"><?php echo e($vehicleRequest->applicant_name); ?></div>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('handovers.store', $vehicleRequest->id)); ?>" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6 text-xs">
        <?php echo csrf_field(); ?>

        <!-- Tarikh, Masa & Penerima -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Ambil <span class="text-rose-500">*</span></label>
                <input type="date" name="handover_date" value="<?php echo e(old('handover_date', $vehicleRequest->start_date->format('Y-m-d'))); ?>" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Masa Ambil <span class="text-rose-500">*</span></label>
                <input type="time" name="handover_time" value="<?php echo e(old('handover_time', \Carbon\Carbon::parse($vehicleRequest->start_time)->format('H:i'))); ?>" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penerima Kenderaan <span class="text-rose-500">*</span></label>
                <input type="text" name="received_by_name" value="<?php echo e(old('received_by_name', $vehicleRequest->driver?->name ?: $vehicleRequest->applicant_name)); ?>" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
            </div>
        </div>

        <!-- Meter Awal & Aras Minyak -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">
                    Bacaan Meter Awal (Odometer KM) <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="start_mileage" value="<?php echo e(old('start_mileage', $vehicleRequest->vehicle?->current_mileage ?? 0)); ?>" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-mono font-bold text-sm">
                <div class="text-[10px] text-slate-400 mt-1">Meter semasa dalam sistem: <?php echo e(number_format($vehicleRequest->vehicle?->current_mileage ?? 0)); ?> KM</div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">
                    Aras Bahan Api (Fuel Level) <span class="text-rose-500">*</span>
                </label>
                <select name="fuel_level" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-bold">
                    <option value="Full (Penuh)" selected>Full (Penuh 100%)</option>
                    <option value="3/4 (Tiga Suku)">3/4 (Tiga Suku 75%)</option>
                    <option value="1/2 (Separuh)">1/2 (Separuh 50%)</option>
                    <option value="1/4 (Suku)">1/4 (Suku 25%)</option>
                </select>
            </div>
        </div>

        <!-- Checklist Pemeriksaan Fizikal & Kelengkapan (Section 16 requirement) -->
        <div class="space-y-3">
            <label class="block font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                Senarai Semak Serahan (Checklist Serahan UPF)
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <label class="p-3 rounded-xl border border-slate-200 hover:border-asm-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="check_body" value="1" checked class="rounded border-slate-300 text-asm-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Keadaan Body / Fizikal Baik</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-asm-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="check_tyre" value="1" checked class="rounded border-slate-300 text-asm-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Tekanan & Bunga Tayar Baik</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-asm-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="check_keys" value="1" checked class="rounded border-slate-300 text-asm-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Kunci Kenderaan Diserahkan</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-asm-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="check_smart_tag" value="1" <?php echo e($vehicleRequest->assigned_smart_tag ? 'checked' : ''); ?> class="rounded border-slate-300 text-asm-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Smart Tag Diserahkan</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-asm-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="check_fuel_card" value="1" <?php echo e($vehicleRequest->assigned_fuel_card ? 'checked' : ''); ?> class="rounded border-slate-300 text-asm-600 w-4 h-4">
                    <span class="font-bold text-slate-800">Kad Inden Petrol Diserahkan</span>
                </label>

                <label class="p-3 rounded-xl border border-slate-200 hover:border-asm-500 cursor-pointer flex items-center space-x-2">
                    <input type="checkbox" name="check_gps" value="1" <?php echo e($vehicleRequest->assigned_gps ? 'checked' : ''); ?> class="rounded border-slate-300 text-asm-600 w-4 h-4">
                    <span class="font-bold text-slate-800">GPS Navigation Diserahkan</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Keadaan Kenderaan Semasa Serahan</label>
            <textarea name="condition_notes" rows="2" placeholder="Cth: Kenderaan telah dicuci bersih, tiada calar baharu..."
                class="w-full px-3 py-2 rounded-xl border border-slate-300"></textarea>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="<?php echo e(route('requests.show', $vehicleRequest->id)); ?>" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black shadow transition">
                Sahkan & Simpan Rekod Serahan
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/handovers/create.blade.php ENDPATH**/ ?>