<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentReport extends Model
{
    protected $fillable = [
        'report_number',
        'request_id',
        'vehicle_id',
        'driver_id',
        'reported_by_user_id',
        'incident_type',
        'severity',
        'incident_date',
        'incident_time',
        'location',
        'description',
        'photo_path',
        'police_report_path',
        'status',
        'upf_action_notes',
        'cost',
        'resolved_at',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'cost' => 'decimal:2',
        'resolved_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(VehicleRequest::class, 'request_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Reported' => ['class' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'Dilaporkan'],
            'Under Review' => ['class' => 'bg-blue-100 text-blue-800 border-blue-300', 'label' => 'Dalam Semakan'],
            'Under Repair' => ['class' => 'bg-purple-100 text-purple-800 border-purple-300', 'label' => 'Sedang Dibaiki'],
            'Resolved' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'label' => 'Selesai'],
            default => ['class' => 'bg-gray-100 text-gray-800 border-gray-300', 'label' => $this->status],
        };
    }
}
