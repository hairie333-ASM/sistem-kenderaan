<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASM/UPFIT/PERMOHONAN KENDERAAN PEJABAT - <?php echo e($vehicleRequest->request_number); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        @media print {
            body { font-size: 10.5pt; color: black; background: white; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        body { font-family: 'Arial', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 flex justify-center text-slate-900">

    <div class="no-print fixed top-4 right-4 z-50 flex items-center gap-2">
        <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs shadow-lg transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Borang Rasmi (A4)
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
            Tutup
        </button>
    </div>

    <!-- Official A4 Paper Container -->
    <div class="bg-white w-[210mm] min-h-[297mm] p-8 shadow-2xl border border-slate-300 text-xs leading-relaxed space-y-4">
        
        <!-- Header Document Info -->
        <div class="border-b-2 border-slate-900 pb-3 flex items-start justify-between">
            <div class="flex items-center space-x-3">
                <img src="<?php echo e(asset('images/asm-logo-official.png')); ?>" alt="ASM" class="h-12 w-auto object-contain" onerror="this.src='<?php echo e(asset('images/asm-logo.png')); ?>'">
                <div>
                    <div class="font-extrabold text-sm uppercase tracking-wide">AKADEMI SAINS MALAYSIA</div>
                    <div class="text-[11px] text-slate-700 font-bold">UNIT PENGURUSAN FASILITI (UPF)</div>
                    <div class="text-[9px] text-slate-500">Tingkat 20, Sayap Barat, Menara MATRADE, Jalan Sultan Haji Ahmad Shah, 50480 Kuala Lumpur</div>
                </div>
            </div>
            <div class="text-right">
                <div class="font-mono font-bold text-xs bg-slate-100 border border-slate-300 px-2 py-1 inline-block">
                    ASM/UPFIT/PERMOHONAN KENDERAAN PEJABAT
                </div>
                <div class="text-[11px] font-bold text-slate-800 mt-1">No. Permohonan: <span class="font-mono text-blue-900"><?php echo e($vehicleRequest->request_number); ?></span></div>
                <div class="text-[10px] text-slate-500">Tarikh Daftar: <?php echo e($vehicleRequest->created_at->format('d/m/Y')); ?></div>
            </div>
        </div>

        <div class="text-center py-1">
            <h1 class="text-sm font-black tracking-wide uppercase underline decoration-2 underline-offset-4">
                BORANG PERMOHONAN PENGGUNAAN KENDERAAN PEJABAT
            </h1>
        </div>

        <!-- ================= BAHAGIAN A ================= -->
        <div class="border border-slate-900">
            <div class="bg-slate-200 px-3 py-1 font-bold text-[11px] uppercase border-b border-slate-900">
                BAHAGIAN A - DIISI OLEH PEMOHON
            </div>
            <div class="p-3 space-y-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                    <div><strong>Nama Pemohon:</strong> <?php echo e($vehicleRequest->applicant_name); ?></div>
                    <div><strong>Jawatan:</strong> <?php echo e($vehicleRequest->applicant_position); ?></div>
                    <div><strong>Bahagian / Unit:</strong> <?php echo e($vehicleRequest->applicant_department); ?></div>
                    <div><strong>No. Telefon:</strong> <?php echo e($vehicleRequest->applicant_phone); ?></div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-1 pt-1 border-t border-slate-200">
                    <div>
                        <strong>Tarikh Diperlukan:</strong> <?php echo e($vehicleRequest->start_date->format('d/m/Y')); ?>

                    </div>
                    <div>
                        <strong>Sehingga Bila:</strong> <?php echo e($vehicleRequest->end_date->format('d/m/Y')); ?>

                    </div>
                    <div>
                        <strong>Masa Diperlukan:</strong> <?php echo e(\Carbon\Carbon::parse($vehicleRequest->start_time)->format('h:i A')); ?>

                    </div>
                    <div>
                        <strong>Masa Tamat (Jangka):</strong> <?php echo e(\Carbon\Carbon::parse($vehicleRequest->end_time)->format('h:i A')); ?>

                    </div>
                    <?php if($vehicleRequest->arrival_time): ?>
                        <div>
                            <strong>Masa Tiba di Destinasi:</strong> <?php echo e(\Carbon\Carbon::parse($vehicleRequest->arrival_time)->format('h:i A')); ?>

                        </div>
                    <?php endif; ?>
                </div>

                <div class="pt-1 border-t border-slate-200 space-y-1">
                    <div><strong>Lokasi Ambil (Pickup):</strong> <?php echo e($vehicleRequest->origin); ?></div>
                    <div><strong>Destinasi / Lokasi Urusan:</strong> <?php echo e($vehicleRequest->destination); ?></div>
                    <div><strong>Tujuan Penggunaan:</strong> <?php echo e($vehicleRequest->purpose); ?></div>
                    <div><strong>Pegawai Lain yang Turut Serta:</strong> <?php echo e($vehicleRequest->other_passengers ?: 'Tiada'); ?></div>
                </div>

                <!-- Keperluan Dimohon -->
                <div class="pt-1.5 border-t border-slate-200 flex items-center justify-between text-[11px]">
                    <span class="font-bold">Keperluan Diperlukan:</span>
                    <div class="flex items-center space-x-4">
                        <span>[<?php echo e($vehicleRequest->need_driver ? ' X ' : '   '); ?>] Pemandu</span>
                        <span>[<?php echo e($vehicleRequest->need_smart_tag ? ' X ' : '   '); ?>] Smart Tag</span>
                        <span>[<?php echo e($vehicleRequest->need_fuel_card ? ' X ' : '   '); ?>] Kad Inden Petrol</span>
                        <span>[<?php echo e($vehicleRequest->need_gps ? ' X ' : '   '); ?>] GPS</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= BAHAGIAN B ================= -->
        <div class="border border-slate-900">
            <div class="bg-slate-200 px-3 py-1 font-bold text-[11px] uppercase border-b border-slate-900">
                BAHAGIAN B - DIISI OLEH UNIT PENGURUSAN FASILITI (UPF)
            </div>
            <div class="p-3 space-y-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                    <div>
                        <strong>Pemandu Diperuntukkan:</strong>
                        <?php echo e($vehicleRequest->assigned_driver_id ? 'YA' : 'TIDAK'); ?>

                    </div>
                    <div>
                        <strong>Nama Pemandu:</strong>
                        <span class="font-bold uppercase"><?php echo e($vehicleRequest->driver?->name ?? ($vehicleRequest->assigned_vehicle_id ? 'PANDU SENDIRI (OLEH PEMOHON)' : 'BELUM DITETAPKAN')); ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-1 pt-1 border-t border-slate-200">
                    <div>
                        <strong>Kenderaan Ditetapkan:</strong>
                        <span class="font-bold uppercase"><?php echo e($vehicleRequest->vehicle ? "{$vehicleRequest->vehicle->brand} {$vehicleRequest->vehicle->model}" : '-'); ?></span>
                    </div>
                    <div>
                        <strong>No. Pendaftaran:</strong>
                        <span class="font-mono font-bold"><?php echo e($vehicleRequest->vehicle?->plate_number ?? '-'); ?></span>
                    </div>
                </div>

                <!-- Kelengkapan yang dibekalkan -->
                <div class="pt-1.5 border-t border-slate-200 flex items-center justify-between text-[11px]">
                    <span class="font-bold">Kelengkapan Dibekalkan:</span>
                    <div class="flex items-center space-x-4">
                        <span>[<?php echo e($vehicleRequest->assigned_smart_tag ? ' X ' : '   '); ?>] Smart Tag</span>
                        <span>[<?php echo e($vehicleRequest->assigned_fuel_card ? ' X ' : '   '); ?>] Kad Inden Petrol</span>
                        <span>[<?php echo e($vehicleRequest->assigned_gps ? ' X ' : '   '); ?>] GPS</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200 grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] text-slate-500">Catatan UPF:</div>
                        <div class="italic text-[11px]"><?php echo e($vehicleRequest->upf_remarks ?: 'Tiada catatan khas.'); ?></div>
                    </div>
                    <div class="text-right">
                        <div class="h-10"></div>
                        <div class="border-t border-slate-400 pt-1 font-bold inline-block min-w-[180px] text-center text-[10px]">
                            (<?php echo e($vehicleRequest->assignedBy?->name ?? 'Pegawai Pengesah UPF'); ?>)<br>
                            Tarikh: <?php echo e($vehicleRequest->assigned_at ? $vehicleRequest->assigned_at->format('d/m/Y') : date('d/m/Y')); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= BAHAGIAN C ================= -->
        <div class="border border-slate-900">
            <div class="bg-slate-200 px-3 py-1 font-bold text-[11px] uppercase border-b border-slate-900">
                BAHAGIAN C - PERAKUAN & PENGESAHAN PEMOHON / PEMANDU
            </div>
            <div class="p-3 grid grid-cols-2 gap-6">
                <!-- Ambil -->
                <div class="border border-slate-300 p-2.5 rounded space-y-1">
                    <div class="font-bold underline text-[11px]">PENGAMBILAN KENDERAAN</div>
                    <div>Nama: <?php echo e($vehicleRequest->handover?->received_by_name ?? '......................................................'); ?></div>
                    <div>Tarikh: <?php echo e($vehicleRequest->handover ? $vehicleRequest->handover->handover_date->format('d/m/Y') : '........................'); ?></div>
                    <div>Masa: <?php echo e($vehicleRequest->handover ? $vehicleRequest->handover->handover_time : '........................'); ?></div>
                    <div class="pt-6 text-center text-[10px]">
                        <div class="border-t border-slate-400 pt-1">Tandatangan Pemohon / Pemandu</div>
                    </div>
                </div>

                <!-- Pulang -->
                <div class="border border-slate-300 p-2.5 rounded space-y-1">
                    <div class="font-bold underline text-[11px]">PEMULANGAN KENDERAAN</div>
                    <div>Nama: <?php echo e($vehicleRequest->returnRecord?->returned_by_name ?? '......................................................'); ?></div>
                    <div>Tarikh: <?php echo e($vehicleRequest->returnRecord ? $vehicleRequest->returnRecord->return_date->format('d/m/Y') : '........................'); ?></div>
                    <div>Masa: <?php echo e($vehicleRequest->returnRecord ? $vehicleRequest->returnRecord->return_time : '........................'); ?></div>
                    <div class="pt-6 text-center text-[10px]">
                        <div class="border-t border-slate-400 pt-1">Tandatangan Pemohon / Pemandu</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= BAHAGIAN D ================= -->
        <div class="border border-slate-900">
            <div class="bg-slate-200 px-3 py-1 font-bold text-[11px] uppercase border-b border-slate-900">
                BAHAGIAN D - PENGESAHAN PEGAWAI UNIT PENGURUSAN FASILITI (UPF)
            </div>
            <div class="p-3 grid grid-cols-2 gap-6">
                <!-- Ambil -->
                <div class="border border-slate-300 p-2.5 rounded space-y-1">
                    <div class="font-bold underline text-[11px]">SERAHAN KENDERAAN (AMBIL)</div>
                    <div>Nama Pegawai: <?php echo e($vehicleRequest->handover?->handoverBy?->name ?? '......................................................'); ?></div>
                    <div>Tarikh: <?php echo e($vehicleRequest->handover ? $vehicleRequest->handover->handover_date->format('d/m/Y') : '........................'); ?></div>
                    <div>Masa: <?php echo e($vehicleRequest->handover ? $vehicleRequest->handover->handover_time : '........................'); ?></div>
                    <div>Meter Awal: <strong class="font-mono"><?php echo e($vehicleRequest->handover ? number_format($vehicleRequest->handover->start_mileage) . ' KM' : '........................'); ?></strong></div>
                    <div class="pt-4 text-center text-[10px]">
                        <div class="border-t border-slate-400 pt-1">Tandatangan & Cop Pegawai UPF</div>
                    </div>
                </div>

                <!-- Pulang -->
                <div class="border border-slate-300 p-2.5 rounded space-y-1">
                    <div class="font-bold underline text-[11px]">PENERIMAAN & PEMERIKSAAN KENDERAAN (PULANG)</div>
                    <?php if($vehicleRequest->returnRecord && $vehicleRequest->returnRecord->is_upf_verified): ?>
                        <div>Pegawai Pemeriksa UPF: <strong><?php echo e($vehicleRequest->returnRecord->verifiedBy?->name ?? $vehicleRequest->returnRecord->receivedBy?->name ?? 'Pegawai UPF'); ?></strong></div>
                        <div>Tarikh Pengesahan: <strong><?php echo e($vehicleRequest->returnRecord->upf_verified_at ? $vehicleRequest->returnRecord->upf_verified_at->format('d/m/Y') : $vehicleRequest->returnRecord->return_date->format('d/m/Y')); ?></strong></div>
                        <div>Masa Pengesahan: <strong><?php echo e($vehicleRequest->returnRecord->upf_verified_at ? $vehicleRequest->returnRecord->upf_verified_at->format('h:i A') : $vehicleRequest->returnRecord->return_time); ?></strong></div>
                        <div>Status Keadaan: <strong class="text-emerald-800"><?php echo e($vehicleRequest->returnRecord->upf_condition_status ?? 'Baik & Sempurna'); ?></strong></div>
                        <div>Catatan: <span class="italic text-[10px]"><?php echo e($vehicleRequest->returnRecord->upf_verification_notes ?: 'Disahkan kenderaan dalam keadaan baik & sempurna.'); ?></span></div>
                    <?php elseif($vehicleRequest->returnRecord): ?>
                        <div>Pegawai Pemeriksa UPF: <em class="text-amber-800 font-bold">(Menunggu Pengesahan Fizikal UPF)</em></div>
                        <div>Tarikh Pemulangan: <?php echo e($vehicleRequest->returnRecord->return_date->format('d/m/Y')); ?></div>
                        <div>Masa Pemulangan: <?php echo e($vehicleRequest->returnRecord->return_time); ?></div>
                        <div>Status Keadaan: <span class="text-amber-700 italic">Pemeriksaan belum disahkan</span></div>
                    <?php else: ?>
                        <div>Pegawai Pemeriksa UPF: ......................................................</div>
                        <div>Tarikh: ........................</div>
                        <div>Masa: ........................</div>
                        <div>Status Keadaan: ......................................................</div>
                    <?php endif; ?>
                    <div>Meter Akhir: <strong class="font-mono"><?php echo e($vehicleRequest->returnRecord ? number_format($vehicleRequest->returnRecord->return_mileage) . ' KM' : '........................'); ?></strong></div>
                    <div>Jumlah Jarak: <strong class="font-mono text-blue-900"><?php echo e($vehicleRequest->returnRecord ? number_format($vehicleRequest->returnRecord->total_km) . ' KM' : '........................'); ?></strong></div>
                    <div class="pt-2 text-center text-[10px]">
                        <div class="border-t border-slate-400 pt-1">Tandatangan & Cop Pegawai UPF</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-[9px] text-slate-400 text-center pt-2">
            Dokumen ini dijana secara elektronik oleh Vehicle & Driver Management System - Unit Pengurusan Fasiliti (UPF), Akademi Sains Malaysia.
        </div>
    </div>

</body>
</html>
<?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/requests/print.blade.php ENDPATH**/ ?>