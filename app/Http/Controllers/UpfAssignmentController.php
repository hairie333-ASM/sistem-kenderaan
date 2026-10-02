<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\Notification;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UpfAssignmentController extends Controller
{
    public function show(int $id): View
    {
        $vehicleRequest = VehicleRequest::with(['driver', 'vehicle', 'user'])->findOrFail($id);

        $startDate = $vehicleRequest->start_date->format('Y-m-d');
        $startTime = $vehicleRequest->start_time;
        $endDate = $vehicleRequest->end_date->format('Y-m-d');
        $endTime = $vehicleRequest->end_time;

        $drivers = Driver::all()->map(function ($d) use ($startDate, $startTime, $endDate, $endTime, $vehicleRequest) {
            $conflict = $d->getConflict($startDate, $startTime, $endDate, $endTime, $vehicleRequest->id);
            $d->conflict_task = $conflict;
            $d->is_available_for_trip = $d->isAvailableOn($startDate, $startTime, $endDate, $endTime, $vehicleRequest->id);

            return $d;
        });

        $vehicles = Vehicle::all()->map(function ($v) use ($startDate, $startTime, $endDate, $endTime, $vehicleRequest) {
            $conflict = $v->getConflict($startDate, $startTime, $endDate, $endTime, $vehicleRequest->id);
            $v->conflict_task = $conflict;
            $v->is_available_for_trip = $v->isAvailableOn($startDate, $startTime, $endDate, $endTime, $vehicleRequest->id);

            return $v;
        });

        return view('upf.assign', compact('vehicleRequest', 'drivers', 'vehicles'));
    }

    public function review(Request $request, int $id): RedirectResponse
    {
        $vehicleRequest = VehicleRequest::findOrFail($id);
        $user = Auth::user();

        $action = $request->input('action'); // approve, reject, under_review

        if ($action === 'reject') {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            $vehicleRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            Notification::create([
                'user_id' => $vehicleRequest->user_id,
                'title' => 'Permohonan Ditolak: '.$vehicleRequest->request_number,
                'message' => "Permohonan anda bagi {$vehicleRequest->destination} telah ditolak. Sebab: {$validated['rejection_reason']}",
                'type' => 'alert',
                'link' => route('requests.show', $vehicleRequest->id),
            ]);

            AuditLog::record('Tolak Permohonan', 'UPF', "Permohonan {$vehicleRequest->request_number} ditolak oleh {$user->name}. Sebab: {$validated['rejection_reason']}");

            return redirect()->route('requests.show', $vehicleRequest->id)->with('info', 'Permohonan telah ditolak.');
        }

        if ($action === 'approve') {
            $vehicleRequest->update([
                'status' => 'approved',
                'upf_remarks' => $request->input('upf_remarks'),
            ]);

            Notification::create([
                'user_id' => $vehicleRequest->user_id,
                'title' => 'Permohonan Diluluskan: '.$vehicleRequest->request_number,
                'message' => "Permohonan anda bagi {$vehicleRequest->destination} telah diluluskan oleh UPF. Penugasan pemandu/kenderaan akan dimaklumkan.",
                'type' => 'success',
                'link' => route('requests.show', $vehicleRequest->id),
            ]);

            AuditLog::record('Lulus Permohonan', 'UPF', "Permohonan {$vehicleRequest->request_number} diluluskan oleh {$user->name}.");

            return redirect()->route('requests.show', $vehicleRequest->id)->with('success', 'Permohonan telah diluluskan. Sila lengkapkan penugasan.');
        }

        if ($action === 'under_review') {
            $vehicleRequest->update(['status' => 'under_review']);
            AuditLog::record('Semakan Permohonan', 'UPF', "Permohonan {$vehicleRequest->request_number} ditukar status kepada sedang disemak.");

            return redirect()->route('requests.show', $vehicleRequest->id)->with('info', 'Status permohonan dikemaskini kepada Dalam Semakan.');
        }

        return redirect()->back();
    }

    public function assign(Request $request, int $id): RedirectResponse
    {
        $vehicleRequest = VehicleRequest::findOrFail($id);
        $user = Auth::user();

        $validated = $request->validate([
            'assigned_driver_id' => 'nullable|exists:drivers,id',
            'assigned_vehicle_id' => 'required|exists:vehicles,id',
            'assigned_smart_tag' => 'nullable|boolean',
            'assigned_fuel_card' => 'nullable|boolean',
            'assigned_gps' => 'nullable|boolean',
            'upf_remarks' => 'nullable|string',
            'override_conflict' => 'nullable|boolean',
            'override_conflict_reason' => 'nullable|string|max:500',
        ]);

        $override = $request->boolean('override_conflict');
        $overrideReason = $request->input('override_conflict_reason');

        // Check Driver Conflict
        $driver = null;
        if (! empty($validated['assigned_driver_id'])) {
            $driver = Driver::find($validated['assigned_driver_id']);
            $conflict = $vehicleRequest->checkDriverConflict($driver->id);

            if ($conflict && ! $override) {
                return redirect()->back()->withInput()->with('conflict_error', [
                    'type' => 'driver',
                    'message' => "⚠ KONFLIK PENUGASAN PEMANDU: {$driver->name} telah mempunyai tugasan yang bertindih pada waktu tersebut ({$conflict->request_number}: {$conflict->purpose} dari {$conflict->start_time} hingga {$conflict->end_time}).",
                ]);
            }
        }

        // Check Vehicle Conflict
        $vehicle = Vehicle::find($validated['assigned_vehicle_id']);
        $vehicleConflict = $vehicleRequest->checkVehicleConflict($vehicle->id);

        if ($vehicleConflict && ! $override) {
            return redirect()->back()->withInput()->with('conflict_error', [
                'type' => 'vehicle',
                'message' => "⚠ KONFLIK PENUGASAN KENDERAAN: Kenderaan {$vehicle->brand} {$vehicle->model} ({$vehicle->plate_number}) telah ditugaskan untuk permohonan lain ({$vehicleConflict->request_number}: {$vehicleConflict->purpose}).",
            ]);
        }

        // Check if vehicle is in maintenance
        if ($vehicle->status === 'Maintenance' && ! $override) {
            return redirect()->back()->withInput()->with('conflict_error', [
                'type' => 'vehicle_maintenance',
                'message' => "⚠ PERHATIAN: Kenderaan {$vehicle->plate_number} sedang dalam status PENYELENGGARAAN. Sila pilih kenderaan lain atau sahkan override jika kenderaan telah siap sedia.",
            ]);
        }

        // Perform assignment
        $vehicleRequest->update([
            'assigned_driver_id' => $validated['assigned_driver_id'] ?? null,
            'assigned_vehicle_id' => $validated['assigned_vehicle_id'],
            'assigned_smart_tag' => $request->boolean('assigned_smart_tag'),
            'assigned_fuel_card' => $request->boolean('assigned_fuel_card'),
            'assigned_gps' => $request->boolean('assigned_gps'),
            'assigned_by_user_id' => $user->id,
            'assigned_at' => Carbon::now(),
            'upf_remarks' => $validated['upf_remarks'] ?? null,
            'status' => 'assigned',
            'override_conflict' => $override,
            'override_conflict_reason' => $override ? $overrideReason : null,
        ]);

        // Update vehicle status
        if ($vehicle->status === 'Available') {
            $vehicle->update(['status' => 'Assigned']);
        }

        // Update driver status
        if ($driver && $driver->status === 'Available') {
            $driver->update(['status' => 'Assigned']);
        }

        // Notify Driver
        if ($driver && $driver->user_id) {
            Notification::create([
                'user_id' => $driver->user_id,
                'title' => 'Tugasan Baharu: '.$vehicleRequest->request_number,
                'message' => "Anda telah ditugaskan untuk permohonan {$vehicleRequest->request_number} ke {$vehicleRequest->destination} pada {$vehicleRequest->start_date->format('d/m/Y')} jam {$vehicleRequest->start_time}.",
                'type' => 'info',
                'link' => route('driver.tasks'),
            ]);
        }

        // Notify Applicant
        $isDriverAssigned = ! empty($driver);
        $applicantNoticeTitle = $isDriverAssigned ? 'Pemandu & Kenderaan Ditetapkan' : 'Kenderaan Diluluskan (Pandu Sendiri)';

        if ($isDriverAssigned) {
            $applicantNoticeMessage = "Permohonan {$vehicleRequest->request_number} telah ditetapkan. Kenderaan: {$vehicle->brand} {$vehicle->model} ({$vehicle->plate_number}), Pemandu: {$driver->name}.";
        } elseif ($vehicleRequest->need_driver) {
            $applicantNoticeMessage = "Permohonan {$vehicleRequest->request_number} telah diluluskan. Dimaklumkan bahawa atas ketiadaan pemandu, kenderaan {$vehicle->brand} {$vehicle->model} ({$vehicle->plate_number}) diberikan kepada anda untuk dipandu sendiri.";
        } else {
            $applicantNoticeMessage = "Permohonan {$vehicleRequest->request_number} telah diluluskan. Kenderaan {$vehicle->brand} {$vehicle->model} ({$vehicle->plate_number}) telah ditetapkan untuk dipandu sendiri mengikut permohonan anda.";
        }

        Notification::create([
            'user_id' => $vehicleRequest->user_id,
            'title' => $applicantNoticeTitle,
            'message' => $applicantNoticeMessage,
            'type' => 'success',
            'link' => route('requests.show', $vehicleRequest->id),
        ]);

        $auditDetail = "Menetapkan Kenderaan: {$vehicle->plate_number}".($driver ? ", Pemandu: {$driver->name}" : ' (Kenderaan diberikan kepada pemohon untuk dipandu sendiri / Ketiadaan Pemandu)')." untuk {$vehicleRequest->request_number}.";
        if ($override) {
            $auditDetail .= " [OVERRIDE DIBENARKAN: {$overrideReason}]";
        }

        AuditLog::record('Penugasan UPF', 'Penugasan', $auditDetail);

        $flashMessage = $driver
            ? 'Penugasan pemandu, kenderaan dan kelengkapan berjaya disimpan.'
            : 'Kenderaan berjaya diluluskan dan diserahkan kepada pemohon untuk dipandu sendiri.';

        return redirect()->route('requests.show', $vehicleRequest->id)->with('success', $flashMessage);
    }
}
