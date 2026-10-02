<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VehicleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'user_id',
        'applicant_name',
        'applicant_position',
        'applicant_department',
        'applicant_phone',
        'applicant_email',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'arrival_time',
        'origin',
        'destination',
        'purpose',
        'other_passengers',
        'need_driver',
        'need_smart_tag',
        'need_fuel_card',
        'need_gps',
        'applicant_remarks',
        'status',
        'assigned_driver_id',
        'assigned_vehicle_id',
        'assigned_smart_tag',
        'assigned_fuel_card',
        'assigned_gps',
        'assigned_by_user_id',
        'assigned_at',
        'upf_remarks',
        'rejection_reason',
        'is_short_notice',
        'override_conflict',
        'override_conflict_reason',
        'driver_accepted_at',
        'trip_started_at',
        'trip_completed_at',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by_user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'need_driver' => 'boolean',
        'need_smart_tag' => 'boolean',
        'need_fuel_card' => 'boolean',
        'need_gps' => 'boolean',
        'assigned_smart_tag' => 'boolean',
        'assigned_fuel_card' => 'boolean',
        'assigned_gps' => 'boolean',
        'is_short_notice' => 'boolean',
        'override_conflict' => 'boolean',
        'assigned_at' => 'datetime',
        'driver_accepted_at' => 'datetime',
        'trip_started_at' => 'datetime',
        'trip_completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'assigned_driver_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function handover(): HasOne
    {
        return $this->hasOne(VehicleHandover::class, 'request_id');
    }

    public function returnRecord(): HasOne
    {
        return $this->hasOne(VehicleReturn::class, 'request_id');
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class, 'request_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'request_id');
    }

    public function getFormattedStartAttribute(): string
    {
        return Carbon::parse("{$this->start_date->format('Y-m-d')} {$this->start_time}")->format('d M Y, h:i A');
    }

    public function getFormattedEndAttribute(): string
    {
        return Carbon::parse("{$this->end_date->format('Y-m-d')} {$this->end_time}")->format('d M Y, h:i A');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['class' => 'bg-gray-100 text-gray-800 border-gray-300', 'label' => 'Draf'],
            'submitted' => ['class' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'Permohonan Baru'],
            'under_review' => ['class' => 'bg-blue-100 text-blue-800 border-blue-300', 'label' => 'Sedang Disemak'],
            'approved' => ['class' => 'bg-sky-100 text-sky-800 border-sky-300', 'label' => 'Diluluskan'],
            'assigned' => ['class' => 'bg-indigo-100 text-indigo-800 border-indigo-300', 'label' => 'Telah Ditetapkan'],
            'driver_accepted' => ['class' => 'bg-teal-100 text-teal-800 border-teal-300', 'label' => 'Pemandu Terima'],
            'in_progress' => ['class' => 'bg-violet-100 text-violet-800 border-violet-300 animate-pulse', 'label' => 'Dalam Perjalanan'],
            'completed' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'label' => 'Selesai'],
            'cancelled' => ['class' => 'bg-rose-100 text-rose-800 border-rose-300', 'label' => 'Dibatalkan'],
            'rejected' => ['class' => 'bg-red-100 text-red-800 border-red-300', 'label' => 'Ditolak'],
            default => ['class' => 'bg-gray-100 text-gray-800 border-gray-300', 'label' => ucfirst($this->status)],
        };
    }

    /**
     * Check if this request conflicts with an assigned driver
     */
    public function checkDriverConflict(?int $driverId = null): ?VehicleRequest
    {
        $dId = $driverId ?? $this->assigned_driver_id;
        if (! $dId) {
            return null;
        }

        $startDateStr = $this->start_date instanceof Carbon ? $this->start_date->format('Y-m-d') : substr((string) $this->start_date, 0, 10);
        $endDateStr = $this->end_date instanceof Carbon ? $this->end_date->format('Y-m-d') : substr((string) $this->end_date, 0, 10);

        $start = Carbon::parse("{$startDateStr} {$this->start_time}");
        $end = Carbon::parse("{$endDateStr} {$this->end_time}");

        $candidates = self::where('assigned_driver_id', $dId)
            ->where('id', '!=', $this->id)
            ->whereNotIn('status', ['cancelled', 'rejected', 'completed', 'draft'])
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

    /**
     * Check if this request conflicts with an assigned vehicle
     */
    public function checkVehicleConflict(?int $vehicleId = null): ?VehicleRequest
    {
        $vId = $vehicleId ?? $this->assigned_vehicle_id;
        if (! $vId) {
            return null;
        }

        $startDateStr = $this->start_date instanceof Carbon ? $this->start_date->format('Y-m-d') : substr((string) $this->start_date, 0, 10);
        $endDateStr = $this->end_date instanceof Carbon ? $this->end_date->format('Y-m-d') : substr((string) $this->end_date, 0, 10);

        $start = Carbon::parse("{$startDateStr} {$this->start_time}");
        $end = Carbon::parse("{$endDateStr} {$this->end_time}");

        $candidates = self::where('assigned_vehicle_id', $vId)
            ->where('id', '!=', $this->id)
            ->whereNotIn('status', ['cancelled', 'rejected', 'completed', 'draft'])
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
}
