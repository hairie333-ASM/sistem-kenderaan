<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(Request $request): View
    {
        $query = Driver::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('driver_code', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('license_number', 'like', "%{$s}%");
            });
        }

        $drivers = $query->orderBy('name')->paginate(12)->withQueryString();

        $stats = [
            'total' => Driver::count(),
            'available' => Driver::where('status', 'Available')->count(),
            'assigned' => Driver::where('status', 'Assigned')->count(),
            'on_leave' => Driver::where('status', 'On Leave')->count(),
            'off_duty' => Driver::where('status', 'Off Duty')->count(),
        ];

        return view('drivers.index', compact('drivers', 'stats'));
    }

    public function create(): View
    {
        return view('drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'driver_code' => 'required|string|unique:drivers,driver_code',
            'name' => 'required|string|max:255',
            'staff_number' => 'nullable|string|max:50',
            'ic_number' => 'nullable|string|max:50',
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
            'license_class' => 'required|string|max:50',
            'license_expiry' => 'required|date',
            'status' => 'required|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'create_user_account' => 'nullable|boolean',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $userId = null;
        if ($request->boolean('create_user_account') && $request->filled('email')) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('password'),
                'role' => 'pemandu',
                'department' => 'Unit Pengurusan Fasiliti (UPF)',
                'position' => $validated['position'],
                'phone' => $validated['phone'],
            ]);
            $userId = $user->id;
        }

        $driver = Driver::create([
            'user_id' => $userId,
            'driver_code' => $validated['driver_code'],
            'name' => $validated['name'],
            'staff_number' => $validated['staff_number'] ?? null,
            'ic_number' => $validated['ic_number'] ?? null,
            'position' => $validated['position'],
            'phone' => $validated['phone'],
            'license_number' => $validated['license_number'],
            'license_class' => $validated['license_class'],
            'license_expiry' => $validated['license_expiry'],
            'status' => $validated['status'],
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditLog::record('Tambah Pemandu', 'Pemandu', "Mendaftar pemandu baharu {$driver->name} ({$driver->driver_code}).");

        return redirect()->route('drivers.show', $driver->id)->with('success', 'Pemandu baharu berjaya didaftarkan.');
    }

    public function show(int $id): View
    {
        $driver = Driver::with([
            'assignments' => function ($q) {
                $q->with('vehicle')->orderBy('start_date', 'desc')->take(10);
            },
            'fuelLogs' => function ($q) {
                $q->with('vehicle')->take(10);
            },
            'incidents' => function ($q) {
                $q->with('vehicle')->take(10);
            },
            'user',
        ])->findOrFail($id);

        return view('drivers.show', compact('driver'));
    }

    public function edit(int $id): View
    {
        $driver = Driver::findOrFail($id);

        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $driver = Driver::findOrFail($id);

        $validated = $request->validate([
            'driver_code' => 'required|string|unique:drivers,driver_code,'.$driver->id,
            'name' => 'required|string|max:255',
            'staff_number' => 'nullable|string|max:50',
            'ic_number' => 'nullable|string|max:50',
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
            'license_class' => 'required|string|max:50',
            'license_expiry' => 'required|date',
            'status' => 'required|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $driver->update($validated);

        AuditLog::record('Kemaskini Pemandu', 'Pemandu', "Mengemaskini maklumat pemandu {$driver->name}.");

        return redirect()->route('drivers.show', $driver->id)->with('success', 'Maklumat pemandu berjaya dikemaskini.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $driver = Driver::findOrFail($id);
        $name = $driver->name;

        if ($driver->assignments()->whereIn('status', ['assigned', 'driver_accepted', 'in_progress'])->exists()) {
            return redirect()->back()->with('error', 'Pemandu tidak boleh dipadam kerana masih mempunyai tugasan aktif.');
        }

        $driver->delete();
        AuditLog::record('Padam Pemandu', 'Pemandu', "Memadam data pemandu {$name}.");

        return redirect()->route('drivers.index')->with('success', "Pemandu {$name} berjaya dipadam.");
    }
}
