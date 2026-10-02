<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleHandover extends Model
{
    protected $fillable = [
        'request_id',
        'vehicle_id',
        'driver_id',
        'handover_by_user_id',
        'received_by_name',
        'handover_date',
        'handover_time',
        'start_mileage',
        'fuel_level',
        'check_body',
        'check_tyre',
        'check_fuel',
        'check_smart_tag',
        'check_fuel_card',
        'check_gps',
        'check_keys',
        'condition_notes',
    ];

    protected $casts = [
        'handover_date' => 'date',
        'start_mileage' => 'integer',
        'check_body' => 'boolean',
        'check_tyre' => 'boolean',
        'check_fuel' => 'boolean',
        'check_smart_tag' => 'boolean',
        'check_fuel_card' => 'boolean',
        'check_gps' => 'boolean',
        'check_keys' => 'boolean',
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

    public function handoverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handover_by_user_id');
    }
}
