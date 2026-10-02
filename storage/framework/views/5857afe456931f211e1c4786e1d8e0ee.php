<?php $__env->startSection('title', 'Kalendar Tugasan Pemandu'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-calendar-days mr-1"></i> Paparan Kalendar
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">
                KALENDAR TUGASAN & PERJALANAN
            </h1>
            <p class="text-xs text-slate-500">
                Gambaran keseluruhan penugasan bulanan pemandu dan pergerakan kenderaan UPF
            </p>
        </div>

        <!-- Month Navigation -->
        <form method="GET" action="<?php echo e(route('schedules.calendar')); ?>" class="flex items-center gap-2 text-xs">
            <select name="month" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold" onchange="this.form.submit()">
                <?php for($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo e($m); ?>" <?php echo e($month == $m ? 'selected' : ''); ?>>
                        <?php echo e(\Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F')); ?>

                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold" onchange="this.form.submit()">
                <option value="2026" selected>2026</option>
                <option value="2027">2027</option>
            </select>
        </form>
    </div>

    <!-- Calendar Month Grid -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-4">
        <?php
            $currentMonthStart = \Carbon\Carbon::create($year, $month, 1);
            $daysInMonth = $currentMonthStart->daysInMonth;
            $startDayOfWeek = $currentMonthStart->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
        ?>

        <!-- Day of Week Headers -->
        <div class="grid grid-cols-7 gap-1 text-center font-bold text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100 pb-2">
            <div>Isnin</div>
            <div>Selasa</div>
            <div>Rabu</div>
            <div>Khamis</div>
            <div>Jumaat</div>
            <div class="text-rose-600">Sabtu</div>
            <div class="text-rose-600">Ahad</div>
        </div>

        <!-- Days Cells -->
        <div class="grid grid-cols-7 gap-1 text-xs">
            <!-- Blank padding days -->
            <?php for($b = 1; $b < $startDayOfWeek; $b++): ?>
                <div class="min-h-[100px] p-1.5 rounded-xl bg-slate-50/40 text-slate-300"></div>
            <?php endfor; ?>

            <!-- Month Days -->
            <?php for($dayNum = 1; $dayNum <= $daysInMonth; $dayNum++): ?>
                <?php
                    $thisDate = \Carbon\Carbon::create($year, $month, $dayNum)->format('Y-m-d');
                    $isToday = $thisDate === \Carbon\Carbon::today()->format('Y-m-d');
                    $dayRequests = $requests->filter(function($r) use ($thisDate) {
                        return $r->start_date->format('Y-m-d') <= $thisDate && $r->end_date->format('Y-m-d') >= $thisDate;
                    });
                ?>
                <div class="min-h-[110px] p-1.5 rounded-xl border <?php echo e($isToday ? 'border-asm-500 bg-asm-50/30 ring-2 ring-asm-400' : 'border-slate-100 bg-white'); ?> flex flex-col justify-between hover:border-slate-300 transition">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-extrabold text-xs <?php echo e($isToday ? 'text-asm-800' : 'text-slate-800'); ?>"><?php echo e($dayNum); ?></span>
                            <?php if($dayRequests->count() > 0): ?>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <?php endif; ?>
                        </div>

                        <!-- Events for this day -->
                        <div class="space-y-1">
                            <?php $__currentLoopData = $dayRequests->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('requests.show', $r->id)); ?>" class="block p-1 rounded bg-slate-100 hover:bg-asm-100 text-[10px] text-slate-800 transition truncate" title="<?php echo e($r->purpose); ?> (<?php echo e($r->driver?->name); ?>)">
                                    <strong class="text-asm-700"><?php echo e(\Carbon\Carbon::parse($r->start_time)->format('h:i A')); ?></strong>
                                    <?php echo e($r->purpose); ?>

                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php if($dayRequests->count() > 2): ?>
                                <div class="text-[9px] font-bold text-slate-400 pl-1">
                                    + <?php echo e($dayRequests->count() - 2); ?> lagi
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($dayRequests->count() > 0): ?>
                        <div class="text-[9px] text-slate-400 font-mono text-right pt-1">
                            <?php echo e($dayRequests->count()); ?> urusan
                        </div>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan/resources/views/schedules/calendar.blade.php ENDPATH**/ ?>