<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_code',
        'name',
        'staff_number',
        'ic_number',
        'position',
        'phone',
        'license_number',
        'license_class',
        'license_expiry',
        'status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'photo_path',
        'notes',
    ];

    protected $casts = [
        'license_expiry' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(VehicleRequest::class, 'assigned_driver_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DriverSchedule::class);
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(IncidentReport::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Available' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'label' => 'Boleh Bertugas'],
            'Assigned' => ['class' => 'bg-blue-100 text-blue-800 border-blue-300', 'label' => 'Ada Tugasan'],
            'On Leave' => ['class' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'Bercuti'],
            'Off Duty' => ['class' => 'bg-slate-100 text-slate-800 border-slate-300', 'label' => 'Tamat Bertugas'],
            'Unavailable' => ['class' => 'bg-rose-100 text-rose-800 border-rose-300', 'label' => 'Tidak Tersedia'],
            default => ['class' => 'bg-gray-100 text-gray-800 border-gray-300', 'label' => $this->status],
        };
    }

    /**
     * Check if driver has conflict for a specific window
     */
    public function getConflict($startDate, $startTime, $endDate, $endTime, $excludeRequestId = null): ?VehicleRequest
    {
        $startDateStr = $startDate instanceof Carbon ? $startDate->format('Y-m-d') : substr((string) $startDate, 0, 10);
        $endDateStr = $endDate instanceof Carbon ? $endDate->format('Y-m-d') : substr((string) $endDate, 0, 10);

        $start = Carbon::parse("{$startDateStr} {$startTime}");
        $end = Carbon::parse("{$endDateStr} {$endTime}");

        $candidates = VehicleRequest::where('assigned_driver_id', $this->id)
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
        if (in_array($this->status, ['On Leave', 'Unavailable'])) {
            return false;
        }

        return $this->getConflict($startDate, $startTime, $endDate, $endTime, $excludeRequestId) === null;
    }
}
