<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScheduleController extends Controller
{
    /**
     * Jadual Tugasan Pemandu - Format Excel UPF (Table View)
     */
    public function excelView(Request $request): View
    {
        $query = VehicleRequest::with(['driver', 'vehicle', 'user']);

        // Default or filtered date range
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $request->end_date);
        }

        if ($request->filled('driver_id')) {
            $query->where('assigned_driver_id', $request->driver_id);
        }

        if ($request->filled('vehicle_id')) {
            $query->where('assigned_vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('purpose', 'like', "%{$s}%")
                    ->orWhere('origin', 'like', "%{$s}%")
                    ->orWhere('destination', 'like', "%{$s}%")
                    ->orWhere('applicant_name', 'like', "%{$s}%")
                    ->orWhere('request_number', 'like', "%{$s}%");
            });
        }

        $schedules = $query->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $drivers = Driver::orderBy('name')->get();
        $vehicles = Vehicle::orderBy('brand')->get();

        return view('schedules.excel-view', compact('schedules', 'drivers', 'vehicles', 'startDate', 'endDate'));
    }

    /**
     * Export to CSV / Excel spreadsheet format
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $query = VehicleRequest::with(['driver', 'vehicle', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $request->end_date);
        }
        if ($request->filled('driver_id')) {
            $query->where('assigned_driver_id', $request->driver_id);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('assigned_vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('start_date', 'asc')->orderBy('start_time', 'asc')->get();

        $fileName = 'Jadual_Tugasan_Pemandu_UPF_'.date('Ymd_His').'.csv';

        AuditLog::record('Eksport Jadual', 'Jadual', "Pengguna mengeksport {$records->count()} rekod jadual ke fail CSV/Excel.");

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility with special characters
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Row
            fputcsv($handle, [
                'NO',
                'NO PERMOHONAN',
                'TARIKH',
                'MASA AMBIL',
                'TUGASAN',
                'LOKASI (DARI -> KE)',
                'MASA TIBA',
                'PEGAWAI MEMOHON',
                'PEMANDU BERTUGAS',
                'KENDERAAN',
                'NO PENDAFTARAN',
                'STATUS',
            ]);

            $index = 1;
            foreach ($records as $item) {
                fputcsv($handle, [
                    $index++,
                    $item->request_number,
                    $item->start_date->format('d/m/Y'),
                    Carbon::parse($item->start_time)->format('h:i A'),
                    $item->purpose,
                    "{$item->origin} -> {$item->destination}",
                    $item->arrival_time ? Carbon::parse($item->arrival_time)->format('h:i A') : '-',
                    $item->applicant_name,
                    $item->driver?->name ?? 'Belum Ditetapkan',
                    $item->vehicle ? "{$item->vehicle->brand} {$item->vehicle->model}" : 'Belum Ditetapkan',
                    $item->vehicle?->plate_number ?? '-',
                    $item->status_badge['label'],
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Jadual Pemandu Mingguan (Driver Matrix Grid View)
     */
    public function weeklyGrid(Request $request): View
    {
        $referenceDate = $request->filled('week')
            ? Carbon::parse($request->week)
            : Carbon::now();

        $startOfWeek = $referenceDate->copy()->startOfWeek(); // Monday
        $endOfWeek = $referenceDate->copy()->endOfWeek();     // Sunday

        $days = [];
        $curr = $startOfWeek->copy();
        while ($curr->lte($endOfWeek)) {
            $days[] = [
                'date' => $curr->format('Y-m-d'),
                'day_name' => match ($curr->dayOfWeek) {
                    1 => 'Isnin',
                    2 => 'Selasa',
                    3 => 'Rabu',
                    4 => 'Khamis',
                    5 => 'Jumaat',
                    6 => 'Sabtu',
                    0 => 'Ahad',
                },
                'formatted' => $curr->format('d M'),
                'is_today' => $curr->isToday(),
            ];
            $curr->addDay();
        }

        $drivers = Driver::with(['assignments' => function ($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('start_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
                ->with('vehicle');
        }])->orderBy('name')->get();

        $prevWeek = $startOfWeek->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $startOfWeek->copy()->addWeek()->format('Y-m-d');

        return view('schedules.weekly-grid', compact('drivers', 'days', 'startOfWeek', 'endOfWeek', 'prevWeek', 'nextWeek', 'referenceDate'));
    }

    /**
     * Interactive Calendar View (Day, Week, Month)
     */
    public function calendarView(Request $request): View
    {
        $viewType = $request->input('view', 'week'); // month, week, day
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $requests = VehicleRequest::with(['driver', 'vehicle'])
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->get();

        $drivers = Driver::orderBy('name')->get();
        $vehicles = Vehicle::orderBy('brand')->get();

        return view('schedules.calendar', compact('requests', 'drivers', 'vehicles', 'viewType', 'month', 'year'));
    }

    /**
     * Excel/CSV Import View
     */
    public function importExcelView(): View
    {
        return view('schedules.import');
    }

    /**
     * Parse and Preview Excel/CSV schedule before committing
     */
    public function importExcelPreview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('excel_file');
        $rows = [];
        $warnings = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle); // First row header

            $rowIdx = 2;
            while (($data = fgetcsv($handle)) !== false) {
                if (empty(array_filter($data))) {
                    continue;
                }

                // Extract fields by position
                // Expected order: Tarikh, Masa Ambil, Tugasan, Lokasi, Masa Tiba, Pegawai, Pemandu, Kenderaan
                $dateStr = trim($data[0] ?? '');
                $startTimeStr = trim($data[1] ?? '08:00');
                $purpose = trim($data[2] ?? '');
                $location = trim($data[3] ?? '');
                $arrivalTimeStr = trim($data[4] ?? '');
                $officer = trim($data[5] ?? 'Pegawai ASM');
                $driverName = trim($data[6] ?? '');
                $vehicleStr = trim($data[7] ?? '');

                // Validate driver
                $matchedDriver = null;
                if (! empty($driverName)) {
                    $matchedDriver = Driver::where('name', 'like', "%{$driverName}%")->first();
                    if (! $matchedDriver) {
                        $warnings[] = "Baris {$rowIdx}: Pemandu '{$driverName}' tidak dijumpai dalam pangkalan data.";
                    }
                }

                // Validate vehicle
                $matchedVehicle = null;
                if (! empty($vehicleStr)) {
                    $matchedVehicle = Vehicle::where('plate_number', 'like', "%{$vehicleStr}%")
                        ->orWhere('model', 'like', "%{$vehicleStr}%")
                        ->first();
                    if (! $matchedVehicle) {
                        $warnings[] = "Baris {$rowIdx}: Kenderaan '{$vehicleStr}' tidak dijumpai dalam Vehicle Master.";
                    }
                }

                // Parse date
                try {
                    $parsedDate = Carbon::parse($dateStr)->format('Y-m-d');
                } catch (\Exception $e) {
                    $parsedDate = Carbon::today()->format('Y-m-d');
                    $warnings[] = "Baris {$rowIdx}: Format tarikh '{$dateStr}' tidak dapat diproses, menggunakan tarikh hari ini.";
                }

                $rows[] = [
                    'row_idx' => $rowIdx,
                    'date' => $parsedDate,
                    'start_time' => $startTimeStr,
                    'arrival_time' => $arrivalTimeStr,
                    'purpose' => $purpose ?: 'Urusan Rasmi ASM',
                    'location' => $location ?: 'Pejabat ASM MATRADE',
                    'officer' => $officer,
                    'driver_name' => $driverName,
                    'driver_id' => $matchedDriver?->id,
                    'vehicle_str' => $vehicleStr,
                    'vehicle_id' => $matchedVehicle?->id,
                ];

                $rowIdx++;
            }
            fclose($handle);
        }

        // Store rows temporarily in session for process step
        session(['import_preview_rows' => $rows]);

        return view('schedules.import-preview', compact('rows', 'warnings'));
    }

    /**
     * Process confirmed rows into database
     */
    public function importExcelProcess(Request $request): RedirectResponse
    {
        $rows = session('import_preview_rows');
        if (empty($rows)) {
            return redirect()->route('schedules.import')->with('error', 'Tiada data untuk diimport. Sila muat naik fail semula.');
        }

        $user = Auth::user();
        $importedCount = 0;
        $year = Carbon::now()->year;

        foreach ($rows as $r) {
            $count = VehicleRequest::whereYear('created_at', $year)->count() + 1;
            $reqNum = sprintf('REQ-%d-%04d', $year, $count);

            VehicleRequest::create([
                'request_number' => $reqNum,
                'user_id' => $user->id,
                'applicant_name' => $r['officer'],
                'applicant_position' => 'Pegawai ASM',
                'applicant_phone' => '03-8319 3200',
                'applicant_email' => 'info@akademisains.gov.my',
                'start_date' => $r['date'],
                'start_time' => Carbon::parse($r['start_time'])->format('H:i:s'),
                'end_date' => $r['date'],
                'end_time' => Carbon::parse($r['start_time'])->addHours(4)->format('H:i:s'),
                'arrival_time' => ! empty($r['arrival_time']) ? Carbon::parse($r['arrival_time'])->format('H:i:s') : null,
                'origin' => 'Pejabat ASM, Menara MATRADE',
                'destination' => $r['location'],
                'purpose' => $r['purpose'],
                'status' => $r['driver_id'] && $r['vehicle_id'] ? 'assigned' : 'submitted',
                'assigned_driver_id' => $r['driver_id'],
                'assigned_vehicle_id' => $r['vehicle_id'],
                'assigned_by_user_id' => $user->id,
                'assigned_at' => Carbon::now(),
            ]);

            $importedCount++;
        }

        session()->forget('import_preview_rows');

        AuditLog::record('Import Jadual Excel', 'Jadual', "Berjaya mengimport {$importedCount} rekod jadual daripada fail Excel/CSV.");

        return redirect()->route('schedules.excel')->with('success', "Berjaya mengimport {$importedCount} rekod jadual ke dalam sistem!");
    }
}
