<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VehicleRequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = VehicleRequest::with(['driver', 'vehicle', 'user']);

        // If regular applicant, only show own requests
        if ($user->isApplicant()) {
            $query->where('user_id', $user->id);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereIn('status', ['submitted', 'under_review']);
            } elseif ($request->status === 'active') {
                $query->whereIn('status', ['approved', 'assigned', 'driver_accepted', 'in_progress']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('start_date', $request->date);
        }

        // Search keyword
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('request_number', 'like', "%{$s}%")
                    ->orWhere('applicant_name', 'like', "%{$s}%")
                    ->orWhere('purpose', 'like', "%{$s}%")
                    ->orWhere('destination', 'like', "%{$s}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('requests.index', compact('requests'));
    }

    public function create(): View
    {
        $user = Auth::user();
        $minNoticeDays = (int) Setting::get('min_notice_days', 1);

        $drivers = Driver::orderBy('name')->get();
        $vehicles = Vehicle::orderBy('brand')->get();

        $initialDrivers = $drivers->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'status' => $d->status,
            'is_available' => true,
        ])->values();

        $initialVehicles = $vehicles->map(fn ($v) => [
            'id' => $v->id,
            'name' => "{$v->brand} {$v->model}",
            'plate_number' => $v->plate_number,
            'status' => $v->status,
            'is_available' => $v->status === 'Available',
        ])->values();

        return view('requests.create', compact('user', 'minNoticeDays', 'drivers', 'vehicles', 'initialDrivers', 'initialVehicles'));
    }

    /**
     * AJAX endpoint to check availability before submit
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date');
        $startTime = $request->input('start_time');
        $endDate = $request->input('end_date') ?: $startDate;
        $endTime = $request->input('end_time') ?: $startTime;

        if (! $startDate || ! $startTime || ! $endTime) {
            return response()->json(['error' => 'Sila lengkapkan tarikh dan masa.'], 422);
        }

        $minNoticeDays = (int) Setting::get('min_notice_days', 1);
        $reqStart = Carbon::parse("{$startDate} {$startTime}");
        $isShortNotice = $reqStart->diffInDays(Carbon::now(), false) > -$minNoticeDays;

        $drivers = Driver::all()->map(function ($d) use ($startDate, $startTime, $endDate, $endTime) {
            $conflict = $d->getConflict($startDate, $startTime, $endDate, $endTime);

            return [
                'id' => $d->id,
                'name' => $d->name,
                'status' => $d->status,
                'is_available' => $d->isAvailableOn($startDate, $startTime, $endDate, $endTime),
                'conflict_with' => $conflict ? $conflict->request_number.' ('.$conflict->purpose.')' : null,
            ];
        });

        $vehicles = Vehicle::all()->map(function ($v) use ($startDate, $startTime, $endDate, $endTime) {
            $conflict = $v->getConflict($startDate, $startTime, $endDate, $endTime);

            return [
                'id' => $v->id,
                'name' => "{$v->brand} {$v->model}",
                'plate_number' => $v->plate_number,
                'type' => $v->type,
                'status' => $v->status,
                'is_available' => $v->isAvailableOn($startDate, $startTime, $endDate, $endTime),
                'conflict_with' => $conflict ? $conflict->request_number.' ('.$conflict->purpose.')' : null,
            ];
        });

        return response()->json([
            'is_short_notice' => $isShortNotice,
            'warning_message' => $isShortNotice ? 'Permohonan kurang daripada tempoh minimum ('.$minNoticeDays.' hari). Sila semak dengan UPF.' : null,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_position' => 'required|string|max:255',
            'applicant_department' => 'nullable|string|max:255',
            'applicant_phone' => 'required|string|max:50',
            'applicant_email' => 'required|email|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'arrival_time' => 'nullable',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string',
            'other_passengers' => 'nullable|string',
            'need_driver' => 'nullable|boolean',
            'need_smart_tag' => 'nullable|boolean',
            'need_fuel_card' => 'nullable|boolean',
            'need_gps' => 'nullable|boolean',
            'applicant_remarks' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Calculate short notice
        $minNoticeDays = (int) Setting::get('min_notice_days', 1);
        $reqStart = Carbon::parse("{$validated['start_date']} {$validated['start_time']}");
        $isShortNotice = $reqStart->diffInDays(Carbon::now(), false) > -$minNoticeDays;

        // Generate Request Number: ASM/UPF/YYYY/XXXX
        $year = Carbon::now()->year;
        $countThisYear = VehicleRequest::whereYear('created_at', $year)->count() + 1;
        $requestNumber = sprintf('ASM/UPF/%d/%04d', $year, $countThisYear);

        $vehicleRequest = VehicleRequest::create([
            'request_number' => $requestNumber,
            'user_id' => $user->id,
            'applicant_name' => $validated['applicant_name'],
            'applicant_position' => $validated['applicant_position'],
            'applicant_department' => $validated['applicant_department'] ?? null,
            'applicant_phone' => $validated['applicant_phone'],
            'applicant_email' => $validated['applicant_email'],
            'start_date' => $validated['start_date'],
            'start_time' => $validated['start_time'],
            'end_date' => $validated['end_date'],
            'end_time' => $validated['end_time'],
            'arrival_time' => $validated['arrival_time'] ?? null,
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'purpose' => $validated['purpose'],
            'other_passengers' => $validated['other_passengers'] ?? null,
            'need_driver' => $request->boolean('need_driver'),
            'need_smart_tag' => $request->boolean('need_smart_tag'),
            'need_fuel_card' => $request->boolean('need_fuel_card'),
            'need_gps' => $request->boolean('need_gps'),
            'applicant_remarks' => $validated['applicant_remarks'] ?? null,
            'status' => 'submitted',
            'is_short_notice' => $isShortNotice,
        ]);

        // Notify UPF officers
        $upfOfficers = User::whereIn('role', ['upf', 'admin'])->get();
        foreach ($upfOfficers as $officer) {
            Notification::create([
                'user_id' => $officer->id,
                'title' => 'Permohonan Kenderaan Baharu: '.$requestNumber,
                'message' => "Permohonan baharu diterima daripada {$validated['applicant_name']} bagi destinasi {$validated['destination']} pada {$validated['start_date']}.",
                'type' => 'info',
                'link' => route('requests.show', $vehicleRequest->id),
            ]);
        }

        AuditLog::record(
            'Hantar Permohonan',
            'Permohonan',
            "Permohonan kenderaan {$requestNumber} berjaya dihantar oleh {$validated['applicant_name']} ke {$validated['destination']}."
        );

        $msg = "Permohonan kenderaan ({$requestNumber}) berjaya dihantar kepada Unit Pengurusan Fasiliti (UPF) untuk semakan.";
        if ($isShortNotice) {
            $msg .= " Peringatan: Permohonan dibuat dalam tempoh singkat (kurang {$minNoticeDays} hari) dan tertakluk kepada pertimbangan khas UPF.";
        }

        return redirect()->route('requests.show', $vehicleRequest->id)->with('success', $msg);
    }

    public function show(int $id): View
    {
        $vehicleRequest = VehicleRequest::with([
            'driver',
            'vehicle',
            'user',
            'handover',
            'returnRecord',
            'fuelLogs',
            'incidents',
            'assignedBy',
            'cancelledBy',
        ])->findOrFail($id);

        $driverConflict = $vehicleRequest->checkDriverConflict();
        $vehicleConflict = $vehicleRequest->checkVehicleConflict();

        $drivers = Driver::orderBy('name')->get();
        $vehicles = Vehicle::orderBy('brand')->get();

        return view('requests.show', compact('vehicleRequest', 'driverConflict', 'vehicleConflict', 'drivers', 'vehicles'));
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        $vehicleRequest = VehicleRequest::findOrFail($id);
        $user = Auth::user();

        // Check permission
        if (! $user->isUpf() && $vehicleRequest->user_id !== $user->id) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk membatalkan permohonan ini.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $vehicleRequest->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_by_user_id' => $user->id,
        ]);

        // Free up assigned vehicle status if needed
        if ($vehicleRequest->assigned_vehicle_id) {
            $vehicle = Vehicle::find($vehicleRequest->assigned_vehicle_id);
            if ($vehicle && $vehicle->status === 'Assigned') {
                $vehicle->update(['status' => 'Available']);
            }
        }

        // Notify driver if assigned
        if ($vehicleRequest->assigned_driver_id && $vehicleRequest->driver?->user_id) {
            Notification::create([
                'user_id' => $vehicleRequest->driver->user_id,
                'title' => 'Tugasan Dibatalkan: '.$vehicleRequest->request_number,
                'message' => "Tugasan permohonan {$vehicleRequest->request_number} pada {$vehicleRequest->start_date->format('d/m/Y')} telah dibatalkan.",
                'type' => 'warning',
                'link' => route('requests.show', $vehicleRequest->id),
            ]);
        }

        // Notify applicant if cancelled by UPF
        if ($user->isUpf() && $vehicleRequest->user_id !== $user->id) {
            Notification::create([
                'user_id' => $vehicleRequest->user_id,
                'title' => 'Permohonan Dibatalkan oleh UPF',
                'message' => "Permohonan {$vehicleRequest->request_number} telah dibatalkan oleh UPF. Sebab: {$validated['cancellation_reason']}",
                'type' => 'warning',
                'link' => route('requests.show', $vehicleRequest->id),
            ]);
        }

        AuditLog::record(
            'Batal Permohonan',
            'Permohonan',
            "Permohonan {$vehicleRequest->request_number} telah dibatalkan oleh {$user->name}. Sebab: {$validated['cancellation_reason']}"
        );

        return redirect()->route('requests.show', $vehicleRequest->id)->with('info', 'Permohonan telah dibatalkan.');
    }

    /**
     * Printable version matching official ASM/UPFIT/PERMOHONAN KENDERAAN PEJABAT
     */
    public function printForm(int $id): View
    {
        $vehicleRequest = VehicleRequest::with([
            'driver',
            'vehicle',
            'user',
            'handover',
            'returnRecord',
            'assignedBy',
        ])->findOrFail($id);

        return view('requests.print', compact('vehicleRequest'));
    }
}
