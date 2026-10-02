<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_code',
        'brand',
        'model',
        'plate_number',
        'type',
        'year',
        'color',
        'status',
        'current_mileage',
        'fuel_type',
        'last_service_date',
        'next_service_date',
        'next_service_mileage',
        'roadtax_expiry',
        'insurance_expiry',
        'insurance_company',
        'puspakom_expiry',
        'image_path',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'current_mileage' => 'integer',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'roadtax_expiry' => 'date',
        'insurance_expiry' => 'date',
        'puspakom_expiry' => 'date',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(VehicleRequest::class, 'assigned_vehicle_id');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(VehicleMaintenance::class)->orderBy('service_date', 'desc');
    }

    public function handovers(): HasMany
    {
        return $this->hasMany(VehicleHandover::class)->orderBy('handover_date', 'desc');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(VehicleReturn::class)->orderBy('return_date', 'desc');
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class)->orderBy('log_date', 'desc');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(IncidentReport::class)->orderBy('incident_date', 'desc');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} ({$this->plate_number})";
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Available' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'label' => 'Boleh Digunakan'],
            'Assigned' => ['class' => 'bg-blue-100 text-blue-800 border-blue-300', 'label' => 'Telah Ditetapkan'],
            'In Use' => ['class' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'Sedang Digunakan'],
            'Maintenance' => ['class' => 'bg-purple-100 text-purple-800 border-purple-300', 'label' => 'Penyelenggaraan'],
            'Out of Service' => ['class' => 'bg-rose-100 text-rose-800 border-rose-300', 'label' => 'Tidak Beroperasi'],
            default => ['class' => 'bg-gray-100 text-gray-800 border-gray-300', 'label' => $this->status],
        };
    }

    /**
     * Check if vehicle has conflict for a specific window
     */
    public function getConflict($startDate, $startTime, $endDate, $endTime, $excludeRequestId = null): ?VehicleRequest
    {
        $start = Carbon::parse("{$startDate} {$startTime}");
        $end = Carbon::parse("{$endDate} {$endTime}");

        return VehicleRequest::where('assigned_vehicle_id', $this->id)
            ->whereNotIn('status', ['cancelled', 'rejected', 'completed', 'draft'])
            ->when($excludeRequestId, fn ($q) => $q->where('id', '!=', $excludeRequestId))
            ->where(function ($q) use ($start, $end) {
                $q->whereRaw("datetime(substr(start_date, 1, 10) || ' ' || substr(start_time, 1, 5) || ':00') < ?", [$end->format('Y-m-d H:i:s')])
                    ->whereRaw("datetime(substr(end_date, 1, 10) || ' ' || substr(end_time, 1, 5) || ':00') > ?", [$start->format('Y-m-d H:i:s')]);
            })
            ->first();
    }

    public function isAvailableOn($startDate, $startTime, $endDate, $endTime, $excludeRequestId = null): bool
    {
        if (in_array($this->status, ['Maintenance', 'Out of Service'])) {
            return false;
        }

        return $this->getConflict($startDate, $startTime, $endDate, $endTime, $excludeRequestId) === null;
    }
}
