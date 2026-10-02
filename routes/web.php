<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverTaskController;
use App\Http\Controllers\FuelLogController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UpfAssignmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleRequestController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Diagnostics & Status
Route::get('/status', function () {
    $status = [
        'app' => [
            'name' => config('app.name'),
            'env' => config('app.env'),
            'debug' => config('app.debug'),
            'has_app_key' => ! empty(config('app.key')),
            'key_length' => strlen((string) config('app.key')),
            'encrypter_ok' => false,
        ],
        'database' => [
            'default' => config('database.default'),
            'has_database_url' => ! empty(env('DATABASE_URL')),
            'connected' => false,
            'tables_count' => 0,
            'users_count' => 0,
            'error' => null,
        ],
    ];

    try {
        app('encrypter');
        $status['app']['encrypter_ok'] = true;
    } catch (Throwable $e) {
        $status['app']['encrypter_error'] = $e->getMessage();
    }

    try {
        DB::connection()->getPdo();
        $status['database']['connected'] = true;
        $driver = config('database.default');
        if ($driver === 'pgsql') {
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        } else {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
        }
        $status['database']['tables_count'] = count($tables);
        $status['database']['users_count'] = User::count();
    } catch (Throwable $e) {
        $status['database']['error'] = $e->getMessage();
    }

    $allOk = $status['app']['encrypter_ok'] && $status['database']['connected'];

    return response()->json($status, $allOk ? 200 : 503);
});

// Guest Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Fast 1-Click Role Switcher for instant testing
Route::get('/switch-user/{id}', [AuthController::class, 'switchUser'])->name('switch.user');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Vehicle Requests (All roles can browse / view details / print)
    Route::get('/permohonan', [VehicleRequestController::class, 'index'])->name('requests.index');
    Route::get('/permohonan/baru', [VehicleRequestController::class, 'create'])->name('requests.create');
    Route::post('/permohonan', [VehicleRequestController::class, 'store'])->name('requests.store');
    Route::get('/permohonan/{id}', [VehicleRequestController::class, 'show'])->name('requests.show');
    Route::post('/permohonan/{id}/batal', [VehicleRequestController::class, 'cancel'])->name('requests.cancel');
    Route::get('/permohonan/{id}/cetak', [VehicleRequestController::class, 'printForm'])->name('requests.print');
    Route::post('/api/check-availability', [VehicleRequestController::class, 'checkAvailability'])->name('requests.check-availability');

    // Driver Specific Actions
    Route::prefix('pemandu')->name('driver.')->group(function () {
        Route::get('/tugasan', [DriverTaskController::class, 'myTasks'])->name('tasks');
        Route::post('/tugasan/{id}/terima', [DriverTaskController::class, 'acceptTask'])->name('tasks.accept');
        Route::post('/tugasan/{id}/mula', [DriverTaskController::class, 'startTrip'])->name('tasks.start');
        Route::post('/tugasan/{id}/selesai', [DriverTaskController::class, 'completeTrip'])->name('tasks.complete');
    });

    // UPF Workflow & Assignments
    Route::middleware(['role:upf,admin'])->prefix('upf')->name('upf.')->group(function () {
        Route::get('/permohonan/{id}/tetapkan', [UpfAssignmentController::class, 'show'])->name('assign.show');
        Route::post('/permohonan/{id}/tetapkan', [UpfAssignmentController::class, 'assign'])->name('assign');
        Route::post('/permohonan/{id}/semakan', [UpfAssignmentController::class, 'review'])->name('review');
    });

    // Schedules & Excel Compatibility
    Route::prefix('jadual')->name('schedules.')->group(function () {
        Route::get('/excel', [ScheduleController::class, 'excelView'])->name('excel');
        Route::get('/excel/export', [ScheduleController::class, 'exportExcel'])->name('export');
        Route::get('/mingguan', [ScheduleController::class, 'weeklyGrid'])->name('weekly');
        Route::get('/kalendar', [ScheduleController::class, 'calendarView'])->name('calendar');

        Route::middleware(['role:upf,admin'])->group(function () {
            Route::get('/import', [ScheduleController::class, 'importExcelView'])->name('import');
            Route::post('/import/preview', [ScheduleController::class, 'importExcelPreview'])->name('import.preview');
            Route::post('/import/process', [ScheduleController::class, 'importExcelProcess'])->name('import.process');
        });
    });

    // Handover & Return (Ambil & Pulang Kenderaan)
    Route::prefix('operasi')->name('handovers.')->group(function () {
        Route::get('/serahan/{requestId}', [HandoverController::class, 'createHandover'])->name('create');
        Route::post('/serahan/{requestId}', [HandoverController::class, 'storeHandover'])->name('store');
        Route::get('/pemulangan/{requestId}', [HandoverController::class, 'createReturn'])->name('return.create');
        Route::post('/pemulangan/{requestId}', [HandoverController::class, 'storeReturn'])->name('return.store');
        Route::post('/pemulangan/{requestId}/sahkan-upf', [HandoverController::class, 'verifyReturn'])->name('return.verify')->middleware('role:upf,admin');
    });

    // Fuel Logs (Kad Inden Petrol & Minyak - Pemandu, UPF & Admin)
    Route::middleware(['role:pemandu,upf,admin'])->prefix('minyak')->name('fuel.')->group(function () {
        Route::get('/', [FuelLogController::class, 'index'])->name('index');
        Route::get('/tambah', [FuelLogController::class, 'create'])->name('create');
        Route::post('/', [FuelLogController::class, 'store'])->name('store');
    });

    // Incidents & Damage Reports (Laporan Kerosakan - Pemandu & UPF/Admin)
    Route::middleware(['role:pemandu,upf,admin'])->prefix('insiden')->name('incidents.')->group(function () {
        Route::get('/', [IncidentController::class, 'index'])->name('index');
        Route::get('/lapor', [IncidentController::class, 'create'])->name('create');
        Route::post('/', [IncidentController::class, 'store'])->name('store');
        Route::get('/{id}', [IncidentController::class, 'show'])->name('show');
        Route::post('/{id}/kemaskini', [IncidentController::class, 'updateStatus'])->name('update-status');
    });

    // Master Data Kenderaan (Vehicle Master - Pemandu, UPF & Admin)
    Route::middleware(['role:pemandu,upf,admin'])->resource('vehicles', VehicleController::class);

    // Master Data Pemandu (Driver Master - UPF & Admin)
    Route::middleware(['role:upf,admin'])->resource('drivers', DriverController::class);

    // Reports (UPF & Admin)
    Route::middleware(['role:upf,admin'])->prefix('laporan')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export', [ReportController::class, 'exportExcel'])->name('export');
    });

    // Audit Trail
    Route::middleware(['role:admin,upf'])->get('/audit', [AuditLogController::class, 'index'])->name('audit.index');

    // System Settings (Admin / UPF)
    Route::middleware(['role:admin,upf'])->prefix('tetapan')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->name('update');
    });

    // Notifications
    Route::prefix('notifikasi')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{id}/baca', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    });

    // User Management (Admin Only)
    Route::middleware(['role:admin'])->resource('users', UserController::class);
});
