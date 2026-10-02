<?php

namespace Tests\Unit;

use App\Models\Vehicle;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    public function test_vehicle_has_expected_fillable_attributes(): void
    {
        $vehicle = new Vehicle([
            'plate_number' => 'W 4949 M',
            'brand' => 'Honda',
            'model' => 'Accord',
        ]);

        $this->assertEquals('W 4949 M', $vehicle->plate_number);
        $this->assertEquals('Honda', $vehicle->brand);
        $this->assertEquals('Accord', $vehicle->model);
    }
}
