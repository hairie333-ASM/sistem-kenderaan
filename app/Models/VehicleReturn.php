<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleReturn extends Model
{
    protected $fillable = [
        'request_id',
        'vehicle_id',
        'driver_id',
        'received_by_user_id',
        'returned_by_name',
        'return_date',
        'return_time',
        'return_mileage',
        'fuel_level',
        'total_km',
        'return_smart_tag',
        'return_fuel_card',
        'return_gps',
        'return_keys',
        'condition_notes',
        'has_damage_incident',
        'upf_verified_by_user_id',
        'upf_verified_at',
        'is_upf_verified',
        'upf_condition_status',
        'upf_verification_notes',
    ];

    protected $casts = [
        'return_date' => 'date',
        'return_mileage' => 'integer',
        'total_km' => 'integer',
        'return_smart_tag' => 'boolean',
        'return_fuel_card' => 'boolean',
        'return_gps' => 'boolean',
        'return_keys' => 'boolean',
        'has_damage_incident' => 'boolean',
        'is_upf_verified' => 'boolean',
        'upf_verified_at' => 'datetime',
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

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'upf_verified_by_user_id');
    }
}
