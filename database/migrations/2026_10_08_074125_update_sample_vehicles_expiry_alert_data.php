<?php

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $today = Carbon::today();

        // 1. Vehicle 3 (WNV 2434) - Expired (Tamat tempoh 12 hari lalu)
        Vehicle::where('plate_number', 'WNV 2434')->update([
            'roadtax_expiry' => $today->copy()->subDays(12)->format('Y-m-d'),
            'insurance_expiry' => $today->copy()->subDays(12)->format('Y-m-d'),
            'notes' => 'Cukai jalan dan insurans telah tamat tempoh. Sedang dalam tindakan pembaharuan dan penyelenggaraan brek.',
        ]);

        // 2. Vehicle 5 (WTS 8675) - Expiring Soon (Hampir tamat baki 6 hari)
        Vehicle::where('plate_number', 'WTS 8675')->update([
            'roadtax_expiry' => $today->copy()->addDays(6)->format('Y-m-d'),
            'insurance_expiry' => $today->copy()->addDays(6)->format('Y-m-d'),
            'notes' => 'Cukai jalan & insurans hampir tamat (baki 6 hari). Tindakan pembaharuan sedang diproses oleh UPF.',
        ]);

        // 3. Vehicle 7 (VHA 4951) - Expiring Soon (Hampir tamat baki 20 hari)
        Vehicle::where('plate_number', 'VHA 4951')->update([
            'roadtax_expiry' => $today->copy()->addDays(20)->format('Y-m-d'),
            'insurance_expiry' => $today->copy()->addDays(20)->format('Y-m-d'),
            'notes' => 'Cukai jalan & insurans akan tamat dalam tempoh 20 hari.',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Vehicle::where('plate_number', 'WNV 2434')->update([
            'roadtax_expiry' => '2027-02-18',
            'insurance_expiry' => '2027-02-18',
        ]);

        Vehicle::where('plate_number', 'WTS 8675')->update([
            'roadtax_expiry' => '2027-04-12',
            'insurance_expiry' => '2027-04-12',
        ]);

        Vehicle::where('plate_number', 'VHA 4951')->update([
            'roadtax_expiry' => '2027-09-01',
            'insurance_expiry' => '2027-09-01',
        ]);
    }
};
