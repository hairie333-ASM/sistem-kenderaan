<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FuelLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = FuelLog::with(['vehicle', 'driver', 'request']);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('log_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('log_date', '<=', $request->end_date);
        }

        $logs = $query->orderBy('log_date', 'desc')->paginate(15)->withQueryString();

        $vehicles = Vehicle::orderBy('brand')->get();
        $drivers = Driver::orderBy('name')->get();

        $stats = [
            'total_liters' => FuelLog::sum('liters'),
            'total_cost' => FuelLog::sum('total_amount'),
            'total_transactions' => FuelLog::count(),
        ];

        return view('fuel.index', compact('logs', 'vehicles', 'drivers', 'stats'));
    }

    public function create(Request $request): View
    {
        $vehicles = Vehicle::orderBy('brand')->get();
        $drivers = Driver::orderBy('name')->get();

        $selectedRequest = null;
        if ($request->filled('request_id')) {
            $selectedRequest = VehicleRequest::find($request->request_id);
        }

        return view('fuel.create', compact('vehicles', 'drivers', 'selectedRequest'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'request_id' => 'nullable|exists:vehicle_requests,id',
            'log_date' => 'required|date',
            'log_time' => 'required',
            'station_name' => 'required|string|max:100',
            'fuel_type' => 'required|string|max:50',
            'liters' => 'required|numeric|min:0.1',
            'price_per_liter' => 'required|numeric|min:0.01',
            'total_amount' => 'required|numeric|min:0.01',
            'mileage_at_fill' => 'nullable|integer|min:0',
            'payment_method' => 'required|string|max:50',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_photo' => 'nullable|image|max:5120', // 5MB max
            'remarks' => 'nullable|string',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_photo')) {
            $receiptPath = $request->file('receipt_photo')->store('receipts', 'public');
        }

        $fuelLog = FuelLog::create([
            'vehicle_id' => $validated['vehicle_id'],
            'driver_id' => $validated['driver_id'] ?? null,
            'request_id' => $validated['request_id'] ?? null,
            'log_date' => $validated['log_date'],
            'log_time' => $validated['log_time'],
            'station_name' => $validated['station_name'],
            'fuel_type' => $validated['fuel_type'],
            'liters' => $validated['liters'],
            'price_per_liter' => $validated['price_per_liter'],
            'total_amount' => $validated['total_amount'],
            'mileage_at_fill' => $validated['mileage_at_fill'] ?? null,
            'payment_method' => $validated['payment_method'],
            'receipt_number' => $validated['receipt_number'] ?? null,
            'receipt_photo_path' => $receiptPath,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $vehicle = Vehicle::find($validated['vehicle_id']);

        AuditLog::record(
            'Rekod Minyak',
            'Bahan Api',
            "Merekod pembelian bahan api {$validated['liters']} L (RM {$validated['total_amount']}) bagi {$vehicle?->plate_number} di {$validated['station_name']} ({$validated['payment_method']})."
        );

        return redirect()->route('fuel.index')->with('success', 'Rekod pembelian minyak dan resit berjaya disimpan.');
    }
}
