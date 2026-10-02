<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleHandover;
use App\Models\VehicleRequest;
use App\Models\VehicleReturn;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HandoverController extends Controller
{
    public function createHandover(int $requestId): View|RedirectResponse
    {
        $vehicleRequest = VehicleRequest::with(['vehicle', 'driver', 'user', 'handover'])->findOrFail($requestId);

        if (! $vehicleRequest->assigned_vehicle_id) {
            return redirect()->route('requests.show', $vehicleRequest->id)->with('error', 'Sila tetapkan kenderaan terlebih dahulu sebelum membuat serahan.');
        }

        if ($vehicleRequest->handover) {
            return redirect()->route('requests.show', $vehicleRequest->id)->with('info', 'Serahan kenderaan telah pun direkodkan sebelum ini.');
        }

        return view('handovers.create', compact('vehicleRequest'));
    }

    public function storeHandover(Request $request, int $requestId): RedirectResponse
    {
        $vehicleRequest = VehicleRequest::with(['vehicle', 'driver'])->findOrFail($requestId);
        $user = Auth::user();

        $validated = $request->validate([
            'received_by_name' => 'required|string|max:255',
            'handover_date' => 'required|date',
            'handover_time' => 'required',
            'start_mileage' => 'required|integer|min:0',
            'fuel_level' => 'required|string',
            'check_body' => 'nullable|boolean',
            'check_tyre' => 'nullable|boolean',
            'check_fuel' => 'nullable|boolean',
            'check_smart_tag' => 'nullable|boolean',
            'check_fuel_card' => 'nullable|boolean',
            'check_gps' => 'nullable|boolean',
            'check_keys' => 'nullable|boolean',
            'condition_notes' => 'nullable|string',
        ]);

        $handoverByUserId = ($user->isUpf() || $user->isAdmin())
            ? $user->id
            : ($vehicleRequest->assigned_by_user_id ?? User::whereIn('role', ['upf', 'admin'])->first()?->id ?? $user->id);

        $handover = VehicleHandover::create([
            'request_id' => $vehicleRequest->id,
            'vehicle_id' => $vehicleRequest->assigned_vehicle_id,
            'driver_id' => $vehicleRequest->assigned_driver_id,
            'handover_by_user_id' => $handoverByUserId,
            'received_by_name' => $validated['received_by_name'],
            'handover_date' => $validated['handover_date'],
            'handover_time' => $validated['handover_time'],
            'start_mileage' => $validated['start_mileage'],
            'fuel_level' => $validated['fuel_level'],
            'check_body' => $request->boolean('check_body'),
            'check_tyre' => $request->boolean('check_tyre'),
            'check_fuel' => $request->boolean('check_fuel'),
            'check_smart_tag' => $request->boolean('check_smart_tag'),
            'check_fuel_card' => $request->boolean('check_fuel_card'),
            'check_gps' => $request->boolean('check_gps'),
            'check_keys' => $request->boolean('check_keys'),
            'condition_notes' => $validated['condition_notes'] ?? null,
        ]);

        // Update vehicle status & mileage
        $vehicle = $vehicleRequest->vehicle;
        if ($vehicle) {
            $vehicle->update([
                'status' => 'In Use',
                'current_mileage' => max($vehicle->current_mileage, $validated['start_mileage']),
            ]);
        }

        // Update request status to in_progress if not yet
        if (in_array($vehicleRequest->status, ['assigned', 'driver_accepted'])) {
            $vehicleRequest->update([
                'status' => 'in_progress',
                'trip_started_at' => Carbon::now(),
            ]);
        }

        AuditLog::record(
            'Serahan Kenderaan',
            'Operasi',
            "Serahan kenderaan {$vehicle?->plate_number} kepada {$validated['received_by_name']} direkodkan oleh {$user->name}. Mileage mula: {$validated['start_mileage']} km."
        );

        return redirect()->route('requests.show', $vehicleRequest->id)->with('success', 'Rekod serahan kenderaan berjaya disimpan.');
    }

    public function createReturn(int $requestId): View|RedirectResponse
    {
        $vehicleRequest = VehicleRequest::with(['vehicle', 'driver', 'user', 'handover', 'returnRecord'])->findOrFail($requestId);

        if (! $vehicleRequest->handover) {
            return redirect()->route('handovers.create', $vehicleRequest->id)->with('warning', 'Sila rekodkan serahan kenderaan terlebih dahulu sebelum pemulangan.');
        }

        if ($vehicleRequest->returnRecord) {
            return redirect()->route('requests.show', $vehicleRequest->id)->with('info', 'Pemulangan kenderaan telah pun direkodkan sebelum ini.');
        }

        return view('handovers.return', compact('vehicleRequest'));
    }

    public function storeReturn(Request $request, int $requestId): RedirectResponse
    {
        $vehicleRequest = VehicleRequest::with(['vehicle', 'driver', 'handover'])->findOrFail($requestId);
        $user = Auth::user();

        $startMileage = $vehicleRequest->handover ? $vehicleRequest->handover->start_mileage : 0;

        $validated = $request->validate([
            'returned_by_name' => 'required|string|max:255',
            'return_date' => 'required|date',
            'return_time' => 'required',
            'return_mileage' => 'required|integer|gte:'.$startMileage,
            'fuel_level' => 'required|string',
            'return_smart_tag' => 'nullable|boolean',
            'return_fuel_card' => 'nullable|boolean',
            'return_gps' => 'nullable|boolean',
            'return_keys' => 'nullable|boolean',
            'condition_notes' => 'nullable|string',
            'has_damage_incident' => 'nullable|boolean',
            'upf_condition_status' => 'nullable|string|in:Baik & Sempurna,Memuaskan,Ada Kerosakan',
            'upf_verification_notes' => 'nullable|string',
        ]);

        $totalKm = $validated['return_mileage'] - $startMileage;

        $isUpfUser = $user->isUpf() || $user->isAdmin();

        $receivedByUserId = $isUpfUser ? $user->id : null;
        $upfVerifiedByUserId = $isUpfUser ? $user->id : null;
        $upfVerifiedAt = $isUpfUser ? Carbon::now() : null;
        $isUpfVerified = $isUpfUser;
        $upfConditionStatus = $isUpfUser
            ? $request->input('upf_condition_status', 'Baik & Sempurna')
            : 'Menunggu Pengesahan UPF';
        $upfVerificationNotes = $isUpfUser
            ? ($request->input('upf_verification_notes') ?: 'Disahkan penerimaan & keadaan baik oleh Pegawai UPF.')
            : null;

        $returnRecord = VehicleReturn::create([
            'request_id' => $vehicleRequest->id,
            'vehicle_id' => $vehicleRequest->assigned_vehicle_id,
            'driver_id' => $vehicleRequest->assigned_driver_id,
            'received_by_user_id' => $receivedByUserId,
            'upf_verified_by_user_id' => $upfVerifiedByUserId,
            'upf_verified_at' => $upfVerifiedAt,
            'is_upf_verified' => $isUpfVerified,
            'upf_condition_status' => $upfConditionStatus,
            'upf_verification_notes' => $upfVerificationNotes,
            'returned_by_name' => $validated['returned_by_name'],
            'return_date' => $validated['return_date'],
            'return_time' => $validated['return_time'],
            'return_mileage' => $validated['return_mileage'],
            'fuel_level' => $validated['fuel_level'],
            'total_km' => $totalKm,
            'return_smart_tag' => $request->boolean('return_smart_tag'),
            'return_fuel_card' => $request->boolean('return_fuel_card'),
            'return_gps' => $request->boolean('return_gps'),
            'return_keys' => $request->boolean('return_keys'),
            'condition_notes' => $validated['condition_notes'] ?? null,
            'has_damage_incident' => $request->boolean('has_damage_incident'),
        ]);

        // Update vehicle status & mileage
        $vehicle = $vehicleRequest->vehicle;
        if ($vehicle) {
            $vehicleStatus = ($isUpfVerified && $upfConditionStatus === 'Ada Kerosakan')
                ? 'Maintenance'
                : 'Available';

            $vehicle->update([
                'status' => $vehicleStatus,
                'current_mileage' => $validated['return_mileage'],
            ]);
        }

        // Update request status to completed
        $vehicleRequest->update([
            'status' => 'completed',
            'trip_completed_at' => Carbon::now(),
        ]);

        AuditLog::record(
            'Pemulangan Kenderaan',
            'Operasi',
            "Pemulangan kenderaan {$vehicle?->plate_number} oleh {$validated['returned_by_name']}. Mileage akhir: {$validated['return_mileage']} km (Jumlah perjalanan: {$totalKm} km).".($isUpfVerified ? " Disahkan oleh UPF ({$upfConditionStatus})." : ' Menunggu pengesahan fizikal UPF.')
        );

        if (! $isUpfUser) {
            // Notify UPF officers for physical inspection
            $upfOfficers = User::whereIn('role', ['upf', 'admin'])->get();
            foreach ($upfOfficers as $officer) {
                Notification::create([
                    'user_id' => $officer->id,
                    'title' => "Semakan Pemulangan Kenderaan: {$vehicleRequest->request_number}",
                    'message' => "Kenderaan {$vehicle?->plate_number} telah dipulangkan oleh {$validated['returned_by_name']}. Sila lakukan pemeriksaan fizikal dan sahkan keadaan kenderaan.",
                    'type' => 'info',
                    'link' => route('requests.show', $vehicleRequest->id),
                ]);
            }
        }

        if ($request->boolean('has_damage_incident')) {
            return redirect()->route('incidents.create', [
                'request_id' => $vehicleRequest->id,
                'vehicle_id' => $vehicleRequest->assigned_vehicle_id,
            ])->with('warning', "Pemulangan direkodkan ({$totalKm} km). Sila isi laporan kerosakan/isu kenderaan seperti yang ditandakan.");
        }

        $successMsg = $isUpfUser
            ? "Pemulangan kenderaan dan pengesahan keadaan fizikal oleh UPF berjaya direkodkan! Jumlah perjalanan: {$totalKm} KM."
            : "Pemulangan kenderaan berjaya direkodkan ({$totalKm} KM). Pihak UPF akan melakukan semakan dan pengesahan fizikal kenderaan.";

        return redirect()->route('requests.show', $vehicleRequest->id)->with('success', $successMsg);
    }

    public function verifyReturn(Request $request, int $requestId): RedirectResponse
    {
        $vehicleRequest = VehicleRequest::with(['returnRecord', 'vehicle', 'user'])->findOrFail($requestId);

        $validated = $request->validate([
            'upf_condition_status' => 'required|string|in:Baik & Sempurna,Memuaskan,Ada Kerosakan',
            'upf_verification_notes' => 'nullable|string|max:1000',
        ]);

        $returnRecord = $vehicleRequest->returnRecord;
        if (! $returnRecord) {
            return redirect()->back()->with('error', 'Rekod pemulangan kenderaan belum wujud untuk disahkan.');
        }

        $officer = Auth::user();

        $returnRecord->update([
            'received_by_user_id' => $officer->id,
            'upf_verified_by_user_id' => $officer->id,
            'upf_verified_at' => Carbon::now(),
            'is_upf_verified' => true,
            'upf_condition_status' => $validated['upf_condition_status'],
            'upf_verification_notes' => $validated['upf_verification_notes'] ?: 'Disahkan kenderaan dalam keadaan baik & sempurna oleh UPF.',
        ]);

        $vehicle = $vehicleRequest->vehicle;
        if ($vehicle) {
            $newStatus = ($validated['upf_condition_status'] === 'Ada Kerosakan') ? 'Maintenance' : 'Available';
            $vehicle->update(['status' => $newStatus]);
        }

        AuditLog::record(
            'Pengesahan Pemulangan UPF',
            'Operasi',
            "Pegawai UPF {$officer->name} mengesahkan pemeriksaan keadaan kenderaan {$vehicle?->plate_number} bagi permohonan {$vehicleRequest->request_number}. Status: {$validated['upf_condition_status']}."
        );

        if ($vehicleRequest->user_id) {
            Notification::create([
                'user_id' => $vehicleRequest->user_id,
                'title' => "Pengesahan Pemulangan Kenderaan: {$vehicleRequest->request_number}",
                'message' => "Pegawai UPF ({$officer->name}) telah mengesahkan penerimaan kenderaan {$vehicle?->plate_number} ({$validated['upf_condition_status']}).",
                'type' => 'success',
                'link' => route('requests.show', $vehicleRequest->id),
            ]);
        }

        return redirect()->route('requests.show', $vehicleRequest->id)->with('success', 'Pengesahan pemeriksaan keadaan kenderaan oleh UPF berjaya direkodkan.');
    }
}
