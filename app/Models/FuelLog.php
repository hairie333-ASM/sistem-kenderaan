<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelLog extends Model
{
    protected $fillable = [
        'request_id',
        'vehicle_id',
        'driver_id',
        'log_date',
        'log_time',
        'station_name',
        'fuel_type',
        'liters',
        'price_per_liter',
        'total_amount',
        'mileage_at_fill',
        'payment_method',
        'receipt_number',
        'receipt_photo_path',
        'remarks',
    ];

    protected $casts = [
        'log_date' => 'date',
        'liters' => 'decimal:2',
        'price_per_liter' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'mileage_at_fill' => 'integer',
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
}
