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
     * Compute expiry status array for any date
     */
    protected function computeExpiryStatus(?Carbon $date): array
    {
        if (! $date) {
            return [
                'status' => 'none',
                'label' => 'Tiada Rekod',
                'days' => null,
                'days_text' => 'Tiada rekod',
                'badge_class' => 'bg-slate-100 text-slate-600 border-slate-200',
                'text_class' => 'text-slate-500',
                'is_expired' => false,
                'is_expiring' => false,
                'is_valid' => false,
            ];
        }

        $today = Carbon::today();
        $expiry = $date->copy()->startOfDay();
        $days = (int) $today->diffInDays($expiry, false);

        if ($days < 0) {
            $abs = abs($days);

            return [
                'status' => 'expired',
                'label' => 'Tamat Tempoh',
                'days' => $days,
                'days_text' => "Tamat {$abs} hari lalu",
                'badge_class' => 'bg-rose-100 text-rose-800 border-rose-300',
                'text_class' => 'text-rose-600',
                'is_expired' => true,
                'is_expiring' => false,
                'is_valid' => false,
            ];
        }

        if ($days <= 30) {
            $daysText = $days === 0 ? 'Tamat hari ini' : "Baki {$days} hari lagi";

            return [
                'status' => 'expiring',
                'label' => 'Hampir Tamat',
                'days' => $days,
                'days_text' => $daysText,
                'badge_class' => 'bg-amber-100 text-amber-800 border-amber-300',
                'text_class' => 'text-amber-600',
                'is_expired' => false,
                'is_expiring' => true,
                'is_valid' => false,
            ];
        }

        return [
            'status' => 'valid',
            'label' => 'Sah / Aktif',
            'days' => $days,
            'days_text' => "Baki {$days} hari lagi",
            'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'text_class' => 'text-emerald-600',
            'is_expired' => false,
            'is_expiring' => false,
            'is_valid' => true,
        ];
    }

    public function getRoadtaxStatusAttribute(): array
    {
        return $this->computeExpiryStatus($this->roadtax_expiry);
    }

    public function getInsuranceStatusAttribute(): array
    {
        return $this->computeExpiryStatus($this->insurance_expiry);
    }

    public function getAlertLevelAttribute(): ?string
    {
        $rt = $this->roadtax_status;
        $ins = $this->insurance_status;

        if ($rt['status'] === 'expired' || $ins['status'] === 'expired') {
            return 'expired';
        }

        if ($rt['status'] === 'expiring' || $ins['status'] === 'expiring') {
            return 'expiring';
        }

        return null;
    }

    public function getAlertBadgeAttribute(): ?array
    {
        $level = $this->alert_level;

        if ($level === 'expired') {
            return [
                'level' => 'expired',
                'class' => 'bg-rose-100 text-rose-800 border-rose-300',
                'bar_class' => 'bg-rose-600 text-white',
                'border_class' => 'border-rose-400 ring-2 ring-rose-200',
                'icon' => 'fa-solid fa-triangle-exclamation',
                'label' => 'Tamat Tempoh',
            ];
        }

        if ($level === 'expiring') {
            return [
                'level' => 'expiring',
                'class' => 'bg-amber-100 text-amber-800 border-amber-300',
                'bar_class' => 'bg-amber-500 text-slate-900',
                'border_class' => 'border-amber-400 ring-2 ring-amber-200',
                'icon' => 'fa-solid fa-clock-rotate-left',
                'label' => 'Hampir Tamat',
            ];
        }

        return null;
    }

    public function hasAlert(): bool
    {
        return $this->alert_level !== null;
    }

    // Query Scopes
    public function scopeRoadtaxExpired($query)
    {
        return $query->whereNotNull('roadtax_expiry')->where('roadtax_expiry', '<', Carbon::today());
    }

    public function scopeRoadtaxExpiring($query, int $days = 30)
    {
        return $query->whereNotNull('roadtax_expiry')
            ->whereBetween('roadtax_expiry', [Carbon::today(), Carbon::today()->addDays($days)]);
    }

    public function scopeInsuranceExpired($query)
    {
        return $query->whereNotNull('insurance_expiry')->where('insurance_expiry', '<', Carbon::today());
    }

    public function scopeInsuranceExpiring($query, int $days = 30)
    {
        return $query->whereNotNull('insurance_expiry')
            ->whereBetween('insurance_expiry', [Carbon::today(), Carbon::today()->addDays($days)]);
    }

    public function scopeExpiredAlerts($query)
    {
        $today = Carbon::today();

        return $query->where(function ($q) use ($today) {
            $q->where(function ($sub) use ($today) {
                $sub->whereNotNull('roadtax_expiry')->where('roadtax_expiry', '<', $today);
            })->orWhere(function ($sub) use ($today) {
                $sub->whereNotNull('insurance_expiry')->where('insurance_expiry', '<', $today);
            });
        });
    }

    public function scopeExpiringAlerts($query, int $days = 30)
    {
        $today = Carbon::today();
        $future = Carbon::today()->addDays($days);

        return $query->where(function ($q) use ($today, $future) {
            $q->where(function ($sub) use ($today, $future) {
                $sub->whereBetween('roadtax_expiry', [$today, $future])
                    ->where(function ($rtCheck) use ($today) {
                        $rtCheck->whereNull('insurance_expiry')->orWhere('insurance_expiry', '>=', $today);
                    });
            })->orWhere(function ($sub) use ($today, $future) {
                $sub->whereBetween('insurance_expiry', [$today, $future])
                    ->where(function ($insCheck) use ($today) {
                        $insCheck->whereNull('roadtax_expiry')->orWhere('roadtax_expiry', '>=', $today);
                    });
            });
        });
    }

    public function scopeNeedsAttention($query, int $days = 30)
    {
        $future = Carbon::today()->addDays($days);

        return $query->where(function ($q) use ($future) {
            $q->where(function ($sub) use ($future) {
                $sub->whereNotNull('roadtax_expiry')->where('roadtax_expiry', '<=', $future);
            })->orWhere(function ($sub) use ($future) {
                $sub->whereNotNull('insurance_expiry')->where('insurance_expiry', '<=', $future);
            });
        });
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
