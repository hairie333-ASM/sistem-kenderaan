<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_code')->unique();
            $table->string('brand'); // Honda, Proton, Toyota, Nissan, Yamaha
            $table->string('model'); // Accord, CRV, Innova, Persona, Exora, Serena, Almera, Lagenda, LC
            $table->string('plate_number')->unique()->index(); // W 4949 M, WSP 3697, etc.
            $table->string('type')->default('Sedan'); // Sedan, SUV, MPV, Motosikal, Van, Bas
            $table->integer('year')->nullable();
            $table->string('color')->nullable();
            $table->string('status')->default('Available')->index(); // Available, Assigned, In Use, Maintenance, Out of Service
            $table->integer('current_mileage')->default(0);
            $table->string('fuel_type')->default('RON95');
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->integer('next_service_mileage')->nullable();
            $table->date('roadtax_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->string('insurance_company')->nullable();
            $table->date('puspakom_expiry')->nullable();
            $table->string('image_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('driver_code')->unique();
            $table->string('name')->index();
            $table->string('staff_number')->nullable();
            $table->string('ic_number')->nullable();
            $table->string('position')->default('Pemandu Kenderaan Gred H11');
            $table->string('phone')->nullable();
            $table->string('license_number')->nullable();
            $table->string('license_class')->default('D, B2');
            $table->date('license_expiry')->nullable();
            $table->string('status')->default('Available')->index(); // Available, Assigned, On Leave, Off Duty, Unavailable
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('driver_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->string('title');
            $table->string('schedule_type')->default('Tugasan'); // Tugasan, Cuti Rehat, Cuti Sakit, Standby, Kursus
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('maintenance_type'); // Servis Berkala, Pembaikan, Tayar, Bateri, Puspakom
            $table->date('service_date');
            $table->integer('service_mileage')->nullable();
            $table->string('workshop_name')->nullable();
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->string('invoice_receipt_path')->nullable();
            $table->date('next_service_date')->nullable();
            $table->integer('next_service_mileage')->nullable();
            $table->string('status')->default('Completed'); // Scheduled, In Progress, Completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenances');
        Schema::dropIfExists('driver_schedules');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('vehicles');
    }
};
