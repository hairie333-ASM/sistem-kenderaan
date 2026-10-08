<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\IncidentReport;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isDriver()) {
            return $this->driverDashboard();
        }

        if ($user->isApplicant()) {
            return $this->pemohonDashboard();
        }

        return $this->upfDashboard();
    }

    public function upfDashboard(): View
    {
        $today = Carbon::today()->format('Y-m-d');

        // Metrics
        $todayTasksCount = VehicleRequest::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->count();

        $activeDriversCount = Driver::whereHas('assignments', function ($q) use ($today) {
            $q->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->whereNotIn('status', ['cancelled', 'rejected', 'completed', 'draft']);
        })->count();

        $vehiclesInUseCount = Vehicle::where('status', 'In Use')
            ->orWhereHas('requests', function ($q) use ($today) {
                $q->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today)
                    ->whereIn('status', ['assigned', 'driver_accepted', 'in_progress']);
            })->distinct()->count('id');

        $pendingRequestsCount = VehicleRequest::whereIn('status', ['submitted', 'under_review'])->count();

        $maintenanceVehiclesCount = Vehicle::where('status', 'Maintenance')->count();

        // Check conflicts count across all active requests
        $allActiveRequests = VehicleRequest::whereNotIn('status', ['cancelled', 'rejected', 'completed', 'draft'])->get();
        $conflictCount = 0;
        foreach ($allActiveRequests as $req) {
            if ($req->checkDriverConflict() || $req->checkVehicleConflict()) {
                $conflictCount++;
            }
        }

        // Today's tasks
        $todayRequests = VehicleRequest::with(['driver', 'vehicle', 'user'])
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->orderBy('start_time')
            ->get();

        // Upcoming requests (next 7 days)
        $upcomingRequests = VehicleRequest::with(['driver', 'vehicle', 'user'])
            ->where('start_date', '>', $today)
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Recent pending applications needing action
        $pendingRequests = VehicleRequest::with(['user'])
            ->whereIn('status', ['submitted', 'under_review'])
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Active incidents needing attention
        $activeIncidents = IncidentReport::with(['vehicle', 'driver'])
            ->whereIn('status', ['Reported', 'Under Review', 'Under Repair'])
            ->orderBy('incident_date', 'desc')
            ->take(5)
            ->get();

        // Vehicles requiring roadtax / insurance attention
        $alertVehicles = Vehicle::needsAttention()->orderBy('roadtax_expiry')->take(6)->get();
        $alertVehiclesCount = Vehicle::needsAttention()->count();
        $expiredVehiclesCount = Vehicle::expiredAlerts()->count();
        $expiringVehiclesCount = Vehicle::expiringAlerts()->count();

        return view('dashboard.upf', compact(
            'todayTasksCount',
            'activeDriversCount',
            'vehiclesInUseCount',
            'pendingRequestsCount',
            'maintenanceVehiclesCount',
            'conflictCount',
            'todayRequests',
            'upcomingRequests',
            'pendingRequests',
            'activeIncidents',
            'alertVehicles',
            'alertVehiclesCount',
            'expiredVehiclesCount',
            'expiringVehiclesCount'
        ));
    }

    public function pemohonDashboard(): View
    {
        $user = Auth::user();

        $myRequests = VehicleRequest::with(['driver', 'vehicle'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $pendingCount = VehicleRequest::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'under_review'])
            ->count();

        $approvedCount = VehicleRequest::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'assigned', 'driver_accepted'])
            ->count();

        $inProgressCount = VehicleRequest::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->count();

        $completedCount = VehicleRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        return view('dashboard.pemohon', compact(
            'myRequests',
            'pendingCount',
            'approvedCount',
            'inProgressCount',
            'completedCount'
        ));
    }

    public function driverDashboard(): View
    {
        $user = Auth::user();
        $driver = $user->driver ?? Driver::where('name', 'like', '%'.$user->name.'%')->first();

        $today = Carbon::today()->format('Y-m-d');

        if (! $driver) {
            return view('dashboard.driver', [
                'driver' => null,
                'todayTasks' => collect(),
                'upcomingTasks' => collect(),
                'todayTripsCount' => 0,
                'thisWeekCount' => 0,
                'thisMonthCount' => 0,
            ]);
        }

        // Today's tasks for this driver
        $todayTasks = VehicleRequest::with(['vehicle', 'user', 'handover', 'returnRecord'])
            ->where('assigned_driver_id', $driver->id)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->orderBy('start_time')
            ->get();

        // Upcoming tasks
        $upcomingTasks = VehicleRequest::with(['vehicle', 'user'])
            ->where('assigned_driver_id', $driver->id)
            ->where('start_date', '>', $today)
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Stats
        $todayTripsCount = $todayTasks->count();

        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');
        $thisWeekCount = VehicleRequest::where('assigned_driver_id', $driver->id)
            ->whereBetween('start_date', [$startOfWeek, $endOfWeek])
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->count();

        $thisMonthCount = VehicleRequest::where('assigned_driver_id', $driver->id)
            ->whereYear('start_date', Carbon::now()->year)
            ->whereMonth('start_date', Carbon::now()->month)
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->count();

        return view('dashboard.driver', compact(
            'driver',
            'todayTasks',
            'upcomingTasks',
            'todayTripsCount',
            'thisWeekCount',
            'thisMonthCount'
        ));
    }
}
