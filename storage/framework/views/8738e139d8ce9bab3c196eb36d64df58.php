<?php $__env->startSection('title', 'Import Jadual Excel Lama (Migrasi Data)'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="<?php echo e(route('schedules.excel')); ?>" class="text-xs font-bold text-asm-600 hover:text-asm-800 flex items-center mb-1">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Jadual
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">
            MIGRASI DATA & IMPORT JADUAL EXCEL
        </h1>
        <p class="text-xs text-slate-500">
            Muat naik fail jadual pemandu sedia ada dalam format CSV/Excel untuk dipetakan secara automatik ke dalam sistem digital UPF.
        </p>
    </div>

    <!-- Upload Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div class="font-bold text-slate-900 uppercase flex items-center">
                <i class="fa-solid fa-circle-info text-asm-600 mr-1.5"></i> Format Lajur Yang Diperlukan (Susunan Lajur Fail CSV/Excel):
            </div>
            <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-600 font-mono">
                <div>1. Tarikh (Cth: 2026-09-01 atau 01/09/2026)</div>
                <div>2. Masa Ambil (Cth: 07:15 AM)</div>
                <div>3. Tugasan / Tujuan</div>
                <div>4. Lokasi / Destinasi</div>
                <div>5. Masa Tiba (Cth: 08:30 AM)</div>
                <div>6. Pegawai Memohon</div>
                <div>7. Pemandu Bertugas (Cth: Fahizal)</div>
                <div>8. Kenderaan (Cth: W 4949 M / Accord)</div>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('schedules.import.preview')); ?>" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Fail Jadual (.CSV / .TXT) <span class="text-rose-500">*</span>
                </label>
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-asm-500 transition cursor-pointer bg-slate-50/50">
                    <i class="fa-solid fa-file-excel text-4xl text-emerald-600 mb-2"></i>
                    <div class="text-xs font-bold text-slate-800">Klik untuk pilih fail daripada komputer</div>
                    <div class="text-[11px] text-slate-400 mt-1">Sokongan fail CSV (Maksimum 5MB)</div>
                    <input type="file" name="excel_file" required accept=".csv,.txt" class="mt-3 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-asm-950 file:text-white hover:file:bg-asm-900">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <a href="<?php echo e(route('schedules.export')); ?>" class="text-xs font-bold text-asm-600 hover:underline flex items-center">
                    <i class="fa-solid fa-download mr-1"></i> Muat Turun Contoh Templat CSV
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-asm-950 hover:bg-asm-900 text-white font-black text-xs shadow-md transition flex items-center space-x-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Semak & Pratonton Data</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/schedules/import.blade.php ENDPATH**/ ?>