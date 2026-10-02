<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMaintenance extends Model
{
    protected $fillable = [
        'vehicle_id',
        'maintenance_type',
        'service_date',
        'service_mileage',
        'workshop_name',
        'cost',
        'description',
        'invoice_receipt_path',
        'next_service_date',
        'next_service_mileage',
        'status',
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_date' => 'date',
        'cost' => 'decimal:2',
        'service_mileage' => 'integer',
        'next_service_mileage' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
