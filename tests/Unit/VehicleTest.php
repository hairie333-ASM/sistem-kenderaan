<?php

namespace Tests\Unit;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_roadtax_and_insurance_expiry_statuses(): void
    {
        $expiredVehicle = new Vehicle([
            'roadtax_expiry' => Carbon::today()->subDays(10),
            'insurance_expiry' => Carbon::today()->subDays(5),
        ]);

        $this->assertEquals('expired', $expiredVehicle->roadtax_status['status']);
        $this->assertTrue($expiredVehicle->roadtax_status['is_expired']);
        $this->assertStringContainsString('10 hari lalu', $expiredVehicle->roadtax_status['days_text']);
        $this->assertEquals('expired', $expiredVehicle->insurance_status['status']);
        $this->assertEquals('expired', $expiredVehicle->alert_level);

        $expiringVehicle = new Vehicle([
            'roadtax_expiry' => Carbon::today()->addDays(7),
            'insurance_expiry' => Carbon::today()->addDays(20),
        ]);

        $this->assertEquals('expiring', $expiringVehicle->roadtax_status['status']);
        $this->assertTrue($expiringVehicle->roadtax_status['is_expiring']);
        $this->assertStringContainsString('7 hari lagi', $expiringVehicle->roadtax_status['days_text']);
        $this->assertEquals('expiring', $expiringVehicle->insurance_status['status']);
        $this->assertEquals('expiring', $expiringVehicle->alert_level);

        $validVehicle = new Vehicle([
            'roadtax_expiry' => Carbon::today()->addMonths(6),
            'insurance_expiry' => Carbon::today()->addMonths(6),
        ]);

        $this->assertEquals('valid', $validVehicle->roadtax_status['status']);
        $this->assertTrue($validVehicle->roadtax_status['is_valid']);
        $this->assertNull($validVehicle->alert_level);
        $this->assertFalse($validVehicle->hasAlert());
    }

    public function test_alert_query_scopes(): void
    {
        Vehicle::create([
            'vehicle_code' => 'T-01',
            'brand' => 'Toyota',
            'model' => 'Innova',
            'plate_number' => 'ABC 1234',
            'type' => 'MPV',
            'status' => 'Available',
            'current_mileage' => 1000,
            'fuel_type' => 'RON95',
            'roadtax_expiry' => Carbon::today()->subDays(15),
            'insurance_expiry' => Carbon::today()->subDays(15),
        ]);

        Vehicle::create([
            'vehicle_code' => 'T-02',
            'brand' => 'Proton',
            'model' => 'Exora',
            'plate_number' => 'DEF 5678',
            'type' => 'MPV',
            'status' => 'Available',
            'current_mileage' => 2000,
            'fuel_type' => 'RON95',
            'roadtax_expiry' => Carbon::today()->addDays(5),
            'insurance_expiry' => Carbon::today()->addDays(10),
        ]);

        Vehicle::create([
            'vehicle_code' => 'T-03',
            'brand' => 'Honda',
            'model' => 'Civic',
            'plate_number' => 'GHI 9012',
            'type' => 'Sedan',
            'status' => 'Available',
            'current_mileage' => 3000,
            'fuel_type' => 'RON95',
            'roadtax_expiry' => Carbon::today()->addYear(),
            'insurance_expiry' => Carbon::today()->addYear(),
        ]);

        $this->assertEquals(1, Vehicle::expiredAlerts()->count());
        $this->assertEquals(1, Vehicle::expiringAlerts()->count());
        $this->assertEquals(2, Vehicle::needsAttention()->count());
        $this->assertEquals('ABC 1234', Vehicle::expiredAlerts()->first()->plate_number);
        $this->assertEquals('DEF 5678', Vehicle::expiringAlerts()->first()->plate_number);
    }
}
