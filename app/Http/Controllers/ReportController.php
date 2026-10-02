<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\IncidentReport;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\VehicleRequest;
use App\Models\VehicleReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->input('type', 'vehicle_usage');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $drivers = Driver::orderBy('name')->get();
        $vehicles = Vehicle::orderBy('brand')->get();

        $data = match ($type) {
            'vehicle_usage' => $this->getVehicleUsageData($request, $startDate, $endDate),
            'driver_tasks' => $this->getDriverTasksData($request, $startDate, $endDate),
            'monthly' => $this->getMonthlyData($request),
            'mileage' => $this->getMileageData($request, $startDate, $endDate),
            'fuel' => $this->getFuelData($request, $startDate, $endDate),
            'maintenance' => $this->getMaintenanceData($request, $startDate, $endDate),
            'incidents' => $this->getIncidentData($request, $startDate, $endDate),
            'cancellations' => $this->getCancellationsData($request, $startDate, $endDate),
            'officers' => $this->getOfficersData($request, $startDate, $endDate),
            default => $this->getVehicleUsageData($request, $startDate, $endDate),
        };

        return view('reports.index', compact('type', 'startDate', 'endDate', 'drivers', 'vehicles', 'data'));
    }

    protected function getVehicleUsageData(Request $request, $startDate, $endDate)
    {
        $query = Vehicle::withCount(['requests' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->whereNotIn('status', ['cancelled', 'rejected', 'draft']);
        }]);

        if ($request->filled('vehicle_id')) {
            $query->where('id', $request->vehicle_id);
        }

        return $query->get()->map(function ($v) use ($startDate, $endDate) {
            $v->total_km = VehicleReturn::where('vehicle_id', $v->id)
                ->whereBetween('return_date', [$startDate, $endDate])
                ->sum('total_km');
            $v->fuel_cost = FuelLog::where('vehicle_id', $v->id)
                ->whereBetween('log_date', [$startDate, $endDate])
                ->sum('total_amount');

            return $v;
        });
    }

    protected function getDriverTasksData(Request $request, $startDate, $endDate)
    {
        $query = Driver::withCount(['assignments' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->whereNotIn('status', ['cancelled', 'rejected', 'draft']);
        }]);

        if ($request->filled('driver_id')) {
            $query->where('id', $request->driver_id);
        }

        return $query->get()->map(function ($d) use ($startDate, $endDate) {
            $d->completed_tasks = VehicleRequest::where('assigned_driver_id', $d->id)
                ->whereBetween('start_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count();
            $d->total_km = VehicleReturn::where('driver_id', $d->id)
                ->whereBetween('return_date', [$startDate, $endDate])
                ->sum('total_km');

            return $d;
        });
    }

    protected function getMonthlyData(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = Carbon::create($year, $m, 1)->startOfMonth()->format('Y-m-d');
            $monthEnd = Carbon::create($year, $m, 1)->endOfMonth()->format('Y-m-d');

            $months[] = [
                'month_name' => Carbon::create($year, $m, 1)->translatedFormat('F'),
                'requests_count' => VehicleRequest::whereBetween('start_date', [$monthStart, $monthEnd])->whereNotIn('status', ['cancelled', 'rejected', 'draft'])->count(),
                'completed_count' => VehicleRequest::whereBetween('start_date', [$monthStart, $monthEnd])->where('status', 'completed')->count(),
                'total_km' => VehicleReturn::whereBetween('return_date', [$monthStart, $monthEnd])->sum('total_km'),
                'fuel_cost' => FuelLog::whereBetween('log_date', [$monthStart, $monthEnd])->sum('total_amount'),
            ];
        }

        return collect($months);
    }

    protected function getMileageData(Request $request, $startDate, $endDate)
    {
        $query = VehicleReturn::with(['vehicle', 'driver', 'request'])
            ->whereBetween('return_date', [$startDate, $endDate]);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        return $query->orderBy('return_date', 'desc')->get();
    }

    protected function getFuelData(Request $request, $startDate, $endDate)
    {
        $query = FuelLog::with(['vehicle', 'driver', 'request'])
            ->whereBetween('log_date', [$startDate, $endDate]);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        return $query->orderBy('log_date', 'desc')->get();
    }

    protected function getMaintenanceData(Request $request, $startDate, $endDate)
    {
        $query = VehicleMaintenance::with('vehicle')
            ->whereBetween('service_date', [$startDate, $endDate]);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        return $query->orderBy('service_date', 'desc')->get();
    }

    protected function getIncidentData(Request $request, $startDate, $endDate)
    {
        $query = IncidentReport::with(['vehicle', 'driver', 'reportedBy'])
            ->whereBetween('incident_date', [$startDate, $endDate]);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        return $query->orderBy('incident_date', 'desc')->get();
    }

    protected function getCancellationsData(Request $request, $startDate, $endDate)
    {
        return VehicleRequest::with(['driver', 'vehicle', 'user', 'cancelledBy'])
            ->where('status', 'cancelled')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orderBy('cancelled_at', 'desc')
            ->get();
    }

    protected function getOfficersData(Request $request, $startDate, $endDate)
    {
        return VehicleRequest::selectRaw('applicant_name, applicant_department, count(*) as total_requests, sum(case when status = "completed" then 1 else 0 end) as completed_requests')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->groupBy('applicant_name', 'applicant_department')
            ->orderByDesc('total_requests')
            ->get();
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'vehicle_usage');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $fileName = 'Laporan_'.ucfirst($type).'_'.date('Ymd_His').'.csv';

        AuditLog::record('Eksport Laporan', 'Laporan', "Mengeksport laporan jenis {$type} ({$startDate} hingga {$endDate}) ke fail CSV/Excel.");

        return response()->streamDownload(function () use ($type, $request, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM

            if ($type === 'vehicle_usage') {
                fputcsv($handle, ['NO', 'KENDERAAN', 'NO PENDAFTARAN', 'JENIS', 'STATUS', 'JUMLAH PERJALANAN', 'JUMLAH KM', 'KOS MINYAK (RM)']);
                $data = $this->getVehicleUsageData($request, $startDate, $endDate);
                $i = 1;
                foreach ($data as $v) {
                    fputcsv($handle, [$i++, "{$v->brand} {$v->model}", $v->plate_number, $v->type, $v->status, $v->requests_count, $v->total_km, number_format($v->fuel_cost, 2)]);
                }
            } elseif ($type === 'driver_tasks') {
                fputcsv($handle, ['NO', 'NAMA PEMANDU', 'JAWATAN', 'NO TELEFON', 'STATUS', 'JUMLAH PENUGASAN', 'SELESAI', 'JUMLAH KM']);
                $data = $this->getDriverTasksData($request, $startDate, $endDate);
                $i = 1;
                foreach ($data as $d) {
                    fputcsv($handle, [$i++, $d->name, $d->position, $d->phone, $d->status, $d->assignments_count, $d->completed_tasks, $d->total_km]);
                }
            } elseif ($type === 'fuel') {
                fputcsv($handle, ['NO', 'TARIKH', 'KENDERAAN', 'PEMANDU', 'STESEN', 'LITER', 'HARGA/LITER', 'JUMLAH (RM)', 'KAEDAH BAYARAN']);
                $data = $this->getFuelData($request, $startDate, $endDate);
                $i = 1;
                foreach ($data as $f) {
                    fputcsv($handle, [$i++, $f->log_date->format('d/m/Y'), $f->vehicle?->plate_number, $f->driver?->name ?? '-', $f->station_name, $f->liters, $f->price_per_liter, $f->total_amount, $f->payment_method]);
                }
            } else {
                fputcsv($handle, ['NO', 'MAKLUMAT', 'TARIKH']);
                fputcsv($handle, [1, 'Laporan UPF Dijana', date('d/m/Y')]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
