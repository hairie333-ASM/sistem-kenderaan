<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique()->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Bahagian A: Maklumat Pemohon & Perjalanan
            $table->string('applicant_name');
            $table->string('applicant_position');
            $table->string('applicant_department')->nullable();
            $table->string('applicant_phone')->nullable();
            $table->string('applicant_email')->nullable();

            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date');
            $table->time('end_time');
            $table->time('arrival_time')->nullable(); // Masa jangka tiba

            $table->string('origin')->default('Pejabat ASM, MATRADE'); // Lokasi pickup
            $table->string('destination'); // Destinasi / Lokasi urusan
            $table->text('purpose'); // Tujuan
            $table->text('other_passengers')->nullable(); // Pegawai lain yang turut serta

            // Keperluan yang dimohon
            $table->boolean('need_driver')->default(true);
            $table->boolean('need_smart_tag')->default(false);
            $table->boolean('need_fuel_card')->default(false);
            $table->boolean('need_gps')->default(false);
            $table->text('applicant_remarks')->nullable();

            // Status Permohonan
            // draft, submitted, under_review, approved, assigned, driver_accepted, in_progress, completed, cancelled, rejected
            $table->string('status')->default('submitted')->index();

            // Bahagian B: Penugasan UPF
            $table->foreignId('assigned_driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('assigned_vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->boolean('assigned_smart_tag')->default(false);
            $table->boolean('assigned_fuel_card')->default(false);
            $table->boolean('assigned_gps')->default(false);

            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('assigned_at')->nullable();
            $table->text('upf_remarks')->nullable();
            $table->text('rejection_reason')->nullable();

            // Flag dan override
            $table->boolean('is_short_notice')->default(false);
            $table->boolean('override_conflict')->default(false);
            $table->string('override_conflict_reason')->nullable();

            // Lifecycle Pemandu & Perjalanan
            $table->dateTime('driver_accepted_at')->nullable();
            $table->dateTime('trip_started_at')->nullable();
            $table->dateTime('trip_completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('vehicle_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('vehicle_requests')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('handover_by_user_id')->constrained('users')->cascadeOnDelete(); // Pegawai UPF
            $table->string('received_by_name'); // Nama yang menerima kenderaan

            $table->date('handover_date');
            $table->time('handover_time');
            $table->integer('start_mileage')->default(0);
            $table->string('fuel_level')->default('Full'); // Full, 3/4, 1/2, 1/4

            // Checklist Kelengkapan & Keadaan
            $table->boolean('check_body')->default(true);
            $table->boolean('check_tyre')->default(true);
            $table->boolean('check_fuel')->default(true);
            $table->boolean('check_smart_tag')->default(false);
            $table->boolean('check_fuel_card')->default(false);
            $table->boolean('check_gps')->default(false);
            $table->boolean('check_keys')->default(true);

            $table->text('condition_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('vehicle_requests')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete(); // Pegawai UPF
            $table->string('returned_by_name');

            $table->date('return_date');
            $table->time('return_time');
            $table->integer('return_mileage')->default(0);
            $table->string('fuel_level')->default('Full');
            $table->integer('total_km')->default(0); // return_mileage - start_mileage

            // Checklist Pemulangan
            $table->boolean('return_smart_tag')->default(false);
            $table->boolean('return_fuel_card')->default(false);
            $table->boolean('return_gps')->default(false);
            $table->boolean('return_keys')->default(true);

            $table->text('condition_notes')->nullable();
            $table->boolean('has_damage_incident')->default(false);

            // Pengesahan Pemeriksaan Keadaan Kenderaan Oleh UPF
            $table->foreignId('upf_verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('upf_verified_at')->nullable();
            $table->boolean('is_upf_verified')->default(false);
            $table->string('upf_condition_status')->default('Menunggu Pengesahan'); // Baik & Sempurna, Memuaskan, Ada Kerosakan, Menunggu Pengesahan
            $table->text('upf_verification_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->nullable()->constrained('vehicle_requests')->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();

            $table->date('log_date');
            $table->time('log_time');
            $table->string('station_name'); // Petronas, Shell, Petron, BHP, Caltex
            $table->string('fuel_type')->default('RON95');
            $table->decimal('liters', 8, 2);
            $table->decimal('price_per_liter', 8, 2)->default(2.05);
            $table->decimal('total_amount', 10, 2);
            $table->integer('mileage_at_fill')->nullable();
            $table->string('payment_method')->default('Kad Inden'); // Kad Inden, Tunai, Touch 'n Go
            $table->string('receipt_number')->nullable();
            $table->string('receipt_photo_path')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique()->index();
            $table->foreignId('request_id')->nullable()->constrained('vehicle_requests')->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('reported_by_user_id')->constrained('users')->cascadeOnDelete();

            $table->string('incident_type'); // Kerosakan, Kemalangan, Tayar, Enjin, Aircond, Lampu, Body, Lain-lain
            $table->string('severity')->default('Sederhana'); // Rendah, Sederhana, Tinggi, Kritikal
            $table->date('incident_date');
            $table->time('incident_time');
            $table->string('location');
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->string('police_report_path')->nullable();

            $table->string('status')->default('Reported')->index(); // Reported, Under Review, Under Repair, Resolved
            $table->text('upf_action_notes')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
        Schema::dropIfExists('fuel_logs');
        Schema::dropIfExists('vehicle_returns');
        Schema::dropIfExists('vehicle_handovers');
        Schema::dropIfExists('vehicle_requests');
    }
};
