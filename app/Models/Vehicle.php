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
        $startDateStr = $startDate instanceof Carbon ? $startDate->format('Y-m-d') : substr((string) $startDate, 0, 10);
        $endDateStr = $endDate instanceof Carbon ? $endDate->format('Y-m-d') : substr((string) $endDate, 0, 10);

        $start = Carbon::parse("{$startDateStr} {$startTime}");
        $end = Carbon::parse("{$endDateStr} {$endTime}");

        $candidates = VehicleRequest::where('assigned_vehicle_id', $this->id)
            ->whereNotIn('status', ['cancelled', 'rejected', 'completed', 'draft'])
            ->when($excludeRequestId, fn ($q) => $q->where('id', '!=', $excludeRequestId))
            ->where('start_date', '<=', $endDateStr.' 23:59:59')
            ->where('end_date', '>=', $startDateStr.' 00:00:00')
            ->get();

        return $candidates->first(function ($c) use ($start, $end) {
            $cStartDateStr = $c->start_date instanceof Carbon ? $c->start_date->format('Y-m-d') : substr((string) $c->start_date, 0, 10);
            $cEndDateStr = $c->end_date instanceof Carbon ? $c->end_date->format('Y-m-d') : substr((string) $c->end_date, 0, 10);
            $cStart = Carbon::parse("{$cStartDateStr} {$c->start_time}");
            $cEnd = Carbon::parse("{$cEndDateStr} {$c->end_time}");

            return $cStart < $end && $cEnd > $start;
        });
    }

    public function isAvailableOn($startDate, $startTime, $endDate, $endTime, $excludeRequestId = null): bool
    {
        if (in_array($this->status, ['Maintenance', 'Out of Service'])) {
            return false;
        }

        return $this->getConflict($startDate, $startTime, $endDate, $endTime, $excludeRequestId) === null;
    }
}
