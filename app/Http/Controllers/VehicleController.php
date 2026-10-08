<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vehicle::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('plate_number', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%")
                    ->orWhere('model', 'like', "%{$s}%")
                    ->orWhere('vehicle_code', 'like', "%{$s}%");
            });
        }

        if ($request->filled('alert')) {
            match ($request->alert) {
                'expired' => $query->expiredAlerts(),
                'expiring' => $query->expiringAlerts(),
                'roadtax' => $query->where(function ($q) {
                    $q->roadtaxExpired()->orWhere(fn ($sub) => $sub->roadtaxExpiring());
                }),
                'insurance' => $query->where(function ($q) {
                    $q->insuranceExpired()->orWhere(fn ($sub) => $sub->insuranceExpiring());
                }),
                'all', 'needs_attention' => $query->needsAttention(),
                default => null,
            };
        }

        $vehicles = $query->orderBy('brand')->paginate(12)->withQueryString();

        $stats = [
            'total' => Vehicle::count(),
            'available' => Vehicle::where('status', 'Available')->count(),
            'assigned' => Vehicle::where('status', 'Assigned')->count(),
            'in_use' => Vehicle::where('status', 'In Use')->count(),
            'maintenance' => Vehicle::where('status', 'Maintenance')->count(),
            'out_of_service' => Vehicle::where('status', 'Out of Service')->count(),
            'alerts_total' => Vehicle::needsAttention()->count(),
            'alerts_expired' => Vehicle::expiredAlerts()->count(),
            'alerts_expiring' => Vehicle::expiringAlerts()->count(),
            'roadtax_expired' => Vehicle::roadtaxExpired()->count(),
            'roadtax_expiring' => Vehicle::roadtaxExpiring()->count(),
            'insurance_expired' => Vehicle::insuranceExpired()->count(),
            'insurance_expiring' => Vehicle::insuranceExpiring()->count(),
        ];

        return view('vehicles.index', compact('vehicles', 'stats'));
    }

    public function create(): View
    {
        if (! Auth::user()->isUpf()) {
            abort(403, 'Hanya Pegawai UPF dan Admin dibenarkan menambah kenderaan.');
        }

        return view('vehicles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! Auth::user()->isUpf()) {
            abort(403, 'Hanya Pegawai UPF dan Admin dibenarkan mendaftar kenderaan.');
        }

        $validated = $request->validate([
            'vehicle_code' => 'required|string|unique:vehicles,vehicle_code',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'plate_number' => 'required|string|unique:vehicles,plate_number|max:50',
            'type' => 'required|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
            'status' => 'required|string',
            'current_mileage' => 'required|integer|min:0',
            'fuel_type' => 'required|string',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
            'next_service_mileage' => 'nullable|integer',
            'roadtax_expiry' => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'insurance_company' => 'nullable|string|max:100',
            'puspakom_expiry' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::create($validated);

        AuditLog::record('Tambah Kenderaan', 'Kenderaan', "Menambah kenderaan baharu {$vehicle->brand} {$vehicle->model} ({$vehicle->plate_number}).");

        return redirect()->route('vehicles.show', $vehicle->id)->with('success', 'Kenderaan baharu berjaya didaftarkan.');
    }

    public function show(int $id): View
    {
        $vehicle = Vehicle::with([
            'requests' => function ($q) {
                $q->with(['driver', 'user'])->orderBy('start_date', 'desc')->take(10);
            },
            'maintenances',
            'fuelLogs' => function ($q) {
                $q->with('driver')->take(10);
            },
            'incidents' => function ($q) {
                $q->with('driver')->take(10);
            },
        ])->findOrFail($id);

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(int $id): View
    {
        if (! Auth::user()->isUpf()) {
            abort(403, 'Hanya Pegawai UPF dan Admin dibenarkan mengemaskini maklumat kenderaan.');
        }

        $vehicle = Vehicle::findOrFail($id);

        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if (! Auth::user()->isUpf()) {
            abort(403, 'Hanya Pegawai UPF dan Admin dibenarkan mengemaskini maklumat kenderaan.');
        }

        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'vehicle_code' => 'required|string|unique:vehicles,vehicle_code,'.$vehicle->id,
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'plate_number' => 'required|string|max:50|unique:vehicles,plate_number,'.$vehicle->id,
            'type' => 'required|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
            'status' => 'required|string',
            'current_mileage' => 'required|integer|min:0',
            'fuel_type' => 'required|string',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
            'next_service_mileage' => 'nullable|integer',
            'roadtax_expiry' => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'insurance_company' => 'nullable|string|max:100',
            'puspakom_expiry' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        AuditLog::record('Kemaskini Kenderaan', 'Kenderaan', "Mengemaskini data kenderaan {$vehicle->plate_number}.");

        return redirect()->route('vehicles.show', $vehicle->id)->with('success', 'Maklumat kenderaan berjaya dikemaskini.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if (! Auth::user()->isUpf()) {
            abort(403, 'Hanya Pegawai UPF dan Admin dibenarkan memadam kenderaan.');
        }

        $vehicle = Vehicle::findOrFail($id);
        $plate = $vehicle->plate_number;

        if ($vehicle->requests()->whereIn('status', ['assigned', 'driver_accepted', 'in_progress'])->exists()) {
            return redirect()->back()->with('error', 'Kenderaan tidak boleh dipadam kerana masih mempunyai tugasan aktif.');
        }

        $vehicle->delete();
        AuditLog::record('Padam Kenderaan', 'Kenderaan', "Memadam kenderaan {$plate}.");

        return redirect()->route('vehicles.index')->with('success', "Kenderaan {$plate} berjaya dipadam.");
    }
}
