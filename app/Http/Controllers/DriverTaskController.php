<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\Notification;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DriverTaskController extends Controller
{
    protected function getDriver(): ?Driver
    {
        $user = Auth::user();
        if ($user->driver) {
            return $user->driver;
        }

        // Fallback matching by name
        return Driver::where('name', 'like', '%'.$user->name.'%')->first();
    }

    public function myTasks(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // Pegawai UPF dan pengguna bukan pemandu tidak mempunyai tugasan pemandu peribadi
        if (! $user->isDriver()) {
            return redirect()->route('schedules.excel')->with('info', 'Halaman tugasan pemandu khusus untuk pemandu kenderaan. Pegawai UPF boleh menyemak jadual kenderaan di Jadual Format Excel atau Jadual Mingguan.');
        }

        $driver = $this->getDriver();

        if (! $driver) {
            return view('driver.tasks', [
                'driver' => null,
                'todayTasks' => collect(),
                'upcomingTasks' => collect(),
                'completedTasks' => collect(),
            ]);
        }

        $today = Carbon::today()->format('Y-m-d');

        $todayTasks = VehicleRequest::with(['vehicle', 'user', 'handover', 'returnRecord'])
            ->where('assigned_driver_id', $driver->id)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->orderBy('start_time')
            ->get();

        $upcomingTasks = VehicleRequest::with(['vehicle', 'user'])
            ->where('assigned_driver_id', $driver->id)
            ->where('start_date', '>', $today)
            ->whereNotIn('status', ['cancelled', 'rejected', 'draft'])
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get();

        $completedTasks = VehicleRequest::with(['vehicle', 'user', 'returnRecord'])
            ->where('assigned_driver_id', $driver->id)
            ->where('status', 'completed')
            ->orderBy('trip_completed_at', 'desc')
            ->take(10)
            ->get();

        return view('driver.tasks', compact('driver', 'todayTasks', 'upcomingTasks', 'completedTasks'));
    }

    public function acceptTask(int $id): RedirectResponse
    {
        $driver = $this->getDriver();
        $vehicleRequest = VehicleRequest::findOrFail($id);

        if ($driver && $vehicleRequest->assigned_driver_id !== $driver->id && ! Auth::user()->isAdmin()) {
            abort(403, 'Tugasan ini bukan ditugaskan kepada anda.');
        }

        $vehicleRequest->update([
            'status' => 'driver_accepted',
            'driver_accepted_at' => Carbon::now(),
        ]);

        // Notify UPF officers
        $upfOfficers = User::whereIn('role', ['upf', 'admin'])->get();
        foreach ($upfOfficers as $officer) {
            Notification::create([
                'user_id' => $officer->id,
                'title' => 'Pemandu Menerima Tugasan: '.$vehicleRequest->request_number,
                'message' => "Pemandu {$vehicleRequest->driver?->name} telah mengesahkan penerimaan tugasan {$vehicleRequest->request_number} ke {$vehicleRequest->destination}.",
                'type' => 'success',
                'link' => route('requests.show', $vehicleRequest->id),
            ]);
        }

        AuditLog::record(
            'Terima Tugasan',
            'Pemandu',
            "Pemandu {$vehicleRequest->driver?->name} telah menerima dan mengesahkan tugasan {$vehicleRequest->request_number}."
        );

        return redirect()->back()->with('success', 'Anda telah mengesahkan penerimaan tugasan ini.');
    }

    public function startTrip(int $id): RedirectResponse
    {
        $driver = $this->getDriver();
        $vehicleRequest = VehicleRequest::findOrFail($id);

        if ($driver && $vehicleRequest->assigned_driver_id !== $driver->id && ! Auth::user()->isAdmin()) {
            abort(403, 'Tugasan ini bukan ditugaskan kepada anda.');
        }

        $vehicleRequest->update([
            'status' => 'in_progress',
            'trip_started_at' => Carbon::now(),
        ]);

        if ($vehicleRequest->assigned_vehicle_id) {
            $vehicle = Vehicle::find($vehicleRequest->assigned_vehicle_id);
            if ($vehicle) {
                $vehicle->update(['status' => 'In Use']);
            }
        }

        AuditLog::record(
            'Mula Perjalanan',
            'Pemandu',
            "Pemandu memulakan perjalanan untuk tugasan {$vehicleRequest->request_number} ke {$vehicleRequest->destination}."
        );

        return redirect()->back()->with('success', 'Status perjalanan kini: DALAM PERJALANAN. Pandu cermat!');
    }

    public function completeTrip(int $id): RedirectResponse
    {
        $driver = $this->getDriver();
        $vehicleRequest = VehicleRequest::findOrFail($id);

        if ($driver && $vehicleRequest->assigned_driver_id !== $driver->id && ! Auth::user()->isAdmin()) {
            abort(403, 'Tugasan ini bukan ditugaskan kepada anda.');
        }

        $vehicleRequest->update([
            'status' => 'completed',
            'trip_completed_at' => Carbon::now(),
        ]);

        if ($vehicleRequest->assigned_vehicle_id) {
            $vehicle = Vehicle::find($vehicleRequest->assigned_vehicle_id);
            if ($vehicle && $vehicle->status === 'In Use') {
                $vehicle->update(['status' => 'Available']);
            }
        }

        AuditLog::record(
            'Selesai Tugasan',
            'Pemandu',
            "Pemandu telah menandakan tugasan {$vehicleRequest->request_number} sebagai SELESAI."
        );

        // Check if return record is needed
        if (! $vehicleRequest->returnRecord) {
            return redirect()->route('handovers.return.create', $vehicleRequest->id)->with('success', 'Tugasan selesai! Sila lengkapkan rekod pemulangan dan bacaan meter akhir.');
        }

        return redirect()->back()->with('success', 'Tugasan telah diselesaikan.');
    }
}
