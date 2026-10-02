<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\IncidentReport;
use App\Models\Notification;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncidentController extends Controller
{
    public function index(Request $request): View
    {
        $query = IncidentReport::with(['vehicle', 'driver', 'reportedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('incident_type')) {
            $query->where('incident_type', $request->incident_type);
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $incidents = $query->orderBy('incident_date', 'desc')->paginate(12)->withQueryString();

        $vehicles = Vehicle::orderBy('brand')->get();

        $stats = [
            'total' => IncidentReport::count(),
            'reported' => IncidentReport::where('status', 'Reported')->count(),
            'under_repair' => IncidentReport::where('status', 'Under Repair')->count(),
            'resolved' => IncidentReport::where('status', 'Resolved')->count(),
        ];

        return view('incidents.index', compact('incidents', 'vehicles', 'stats'));
    }

    public function create(Request $request): View
    {
        if (Auth::user()->isApplicant()) {
            abort(403, 'Laporan kerosakan hanya boleh dibuat oleh pemandu kenderaan atau pihak UPF.');
        }

        $vehicles = Vehicle::orderBy('brand')->get();
        $drivers = Driver::orderBy('name')->get();

        $selectedRequest = null;
        if ($request->filled('request_id')) {
            $selectedRequest = VehicleRequest::find($request->request_id);
        }

        $selectedVehicleId = $request->input('vehicle_id', $selectedRequest?->assigned_vehicle_id);

        $currentDriver = Auth::user()->driver ?? Driver::where('user_id', Auth::id())->first();
        $selectedDriverId = $selectedRequest?->assigned_driver_id ?? $currentDriver?->id;

        return view('incidents.create', compact('vehicles', 'drivers', 'selectedRequest', 'selectedVehicleId', 'selectedDriverId'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->isApplicant()) {
            abort(403, 'Laporan kerosakan hanya boleh dibuat oleh pemandu kenderaan atau pihak UPF.');
        }
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'request_id' => 'nullable|exists:vehicle_requests,id',
            'incident_type' => 'required|string|max:50',
            'severity' => 'required|string|max:50',
            'incident_date' => 'required|date',
            'incident_time' => 'required',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('incidents', 'public');
        }

        $year = Carbon::now()->year;
        $count = IncidentReport::whereYear('created_at', $year)->count() + 1;
        $reportNumber = sprintf('INC-%d-%04d', $year, $count);

        $incident = IncidentReport::create([
            'report_number' => $reportNumber,
            'vehicle_id' => $validated['vehicle_id'],
            'driver_id' => $validated['driver_id'] ?? null,
            'request_id' => $validated['request_id'] ?? null,
            'reported_by_user_id' => Auth::id(),
            'incident_type' => $validated['incident_type'],
            'severity' => $validated['severity'],
            'incident_date' => $validated['incident_date'],
            'incident_time' => $validated['incident_time'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'status' => 'Reported',
        ]);

        $vehicle = Vehicle::find($validated['vehicle_id']);

        // Notify UPF officers
        $upfOfficers = User::whereIn('role', ['upf', 'admin'])->get();
        foreach ($upfOfficers as $officer) {
            Notification::create([
                'user_id' => $officer->id,
                'title' => "Laporan Isu/Kerosakan Baharu: {$reportNumber}",
                'message' => "Laporan {$validated['incident_type']} ({$validated['severity']}) diterima bagi {$vehicle?->plate_number} di {$validated['location']}.",
                'type' => 'alert',
                'link' => route('incidents.show', $incident->id),
            ]);
        }

        AuditLog::record(
            'Lapor Isu',
            'Insiden',
            "Laporan isu {$reportNumber} ({$validated['incident_type']}) bagi kenderaan {$vehicle?->plate_number} telah dihantar."
        );

        return redirect()->route('incidents.show', $incident->id)->with('success', "Laporan ({$reportNumber}) berjaya dihantar kepada Unit Pengurusan Fasiliti (UPF).");
    }

    public function show(int $id): View
    {
        $incident = IncidentReport::with(['vehicle', 'driver', 'reportedBy', 'request'])->findOrFail($id);

        return view('incidents.show', compact('incident'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $incident = IncidentReport::with('vehicle')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Reported,Under Review,Under Repair,Resolved',
            'upf_action_notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $incident->status;
        $newStatus = $validated['status'];

        $updateData = [
            'status' => $newStatus,
            'upf_action_notes' => $validated['upf_action_notes'] ?? $incident->upf_action_notes,
            'cost' => $validated['cost'] ?? $incident->cost,
        ];

        if ($newStatus === 'Resolved' && ! $incident->resolved_at) {
            $updateData['resolved_at'] = Carbon::now();
        }

        $incident->update($updateData);

        // Auto update vehicle status
        if ($newStatus === 'Under Repair' && $incident->vehicle) {
            $incident->vehicle->update(['status' => 'Maintenance']);
        } elseif ($newStatus === 'Resolved' && $incident->vehicle && $incident->vehicle->status === 'Maintenance') {
            $incident->vehicle->update(['status' => 'Available']);
        }

        AuditLog::record(
            'Kemaskini Status Insiden',
            'Insiden',
            "Status laporan {$incident->report_number} dikemaskini daripada {$oldStatus} ke {$newStatus}."
        );

        return redirect()->route('incidents.show', $incident->id)->with('success', 'Status laporan dan tindakan UPF berjaya dikemaskini.');
    }
}
