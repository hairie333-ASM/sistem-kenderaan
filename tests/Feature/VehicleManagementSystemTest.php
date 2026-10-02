<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleHandover;
use App\Models\VehicleRequest;
use App\Models\VehicleReturn;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $upf;

    protected User $applicant;

    protected User $driverUser;

    protected Driver $driver;

    protected Vehicle $accord;

    protected Vehicle $crv;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        Role::create(['name' => 'admin', 'label' => 'Pentadbir Sistem']);
        Role::create(['name' => 'upf', 'label' => 'Pegawai UPF']);
        Role::create(['name' => 'pemohon', 'label' => 'Pemohon (Staf/Pegawai)']);
        Role::create(['name' => 'pemandu', 'label' => 'Pemandu']);

        // Default settings
        Setting::set('min_notice_days', '1', 'Tempoh notis minimum');
        Setting::set('allow_conflict_override', '1', 'Kebenaran override');

        // Users
        $this->admin = User::create([
            'name' => 'Administrator UPF',
            'email' => 'admin@test.gov.my',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->upf = User::create([
            'name' => 'Aizat',
            'email' => 'upf@test.gov.my',
            'password' => bcrypt('password'),
            'role' => 'upf',
            'department' => 'Unit Pengurusan Fasiliti',
        ]);

        $this->applicant = User::create([
            'name' => 'Mohd Azim',
            'email' => 'azim@test.gov.my',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
            'department' => 'Unit Komunikasi Korporat',
            'position' => 'Pegawai Eksekutif',
            'phone' => '019-1234567',
        ]);

        $this->driverUser = User::create([
            'name' => 'Fahizal',
            'email' => 'fahizal@test.gov.my',
            'password' => bcrypt('password'),
            'role' => 'pemandu',
            'phone' => '012-9876543',
        ]);

        $this->driver = Driver::create([
            'user_id' => $this->driverUser->id,
            'driver_code' => 'DRV-01',
            'name' => 'Fahizal',
            'staff_number' => 'ASM-D001',
            'phone' => '012-9876543',
            'license_class' => 'D, DA',
            'license_expiry' => '2028-12-31',
            'status' => 'Available',
        ]);

        // Vehicles
        $this->accord = Vehicle::create([
            'vehicle_code' => 'VEH-01',
            'brand' => 'Honda',
            'model' => 'Accord 2.0 VTi-L',
            'plate_number' => 'W 4949 M',
            'type' => 'Sedan',
            'year' => 2018,
            'color' => 'Hitam Mutiara',
            'status' => 'Available',
            'current_mileage' => 85200,
        ]);

        $this->crv = Vehicle::create([
            'vehicle_code' => 'VEH-02',
            'brand' => 'Honda',
            'model' => 'CR-V 1.5 TC',
            'plate_number' => 'WSP 3697',
            'type' => 'SUV',
            'year' => 2020,
            'color' => 'Kelabu Moden',
            'status' => 'Available',
            'current_mileage' => 64300,
        ]);
    }

    /** Test Staff can access permohonan baru page without errors */
    public function test_applicant_can_access_permohonan_baru_page(): void
    {
        $response = $this->actingAs($this->applicant)->get(route('requests.create'));
        $response->assertOk();
        $response->assertSee('PERMOHONAN PENGGUNAAN KENDERAAN PEJABAT');
    }

    /** Test Scenario 1: Staff submits new vehicle request */
    public function test_scenario_01_applicant_submits_vehicle_request(): void
    {
        $futureDate = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $response = $this->actingAs($this->applicant)->post(route('requests.store'), [
            'applicant_name' => 'Mohd Azim',
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_department' => 'Unit Komunikasi Korporat',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $futureDate,
            'start_time' => '09:00',
            'end_date' => $futureDate,
            'end_time' => '13:00',
            'origin' => 'ASM MATRADE, Jalan Sultan Haji Ahmad Shah',
            'destination' => 'Pejabat Perdana Menteri, Putrajaya',
            'purpose' => 'Mesyuarat Bersama KSN mengenai Pelan Dasar STI Negara',
            'other_passengers' => 'Dr. Hazami, Puan Zaleha',
            'need_driver' => '1',
            'need_smart_tag' => '1',
            'need_fuel_card' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicle_requests', [
            'user_id' => $this->applicant->id,
            'destination' => 'Pejabat Perdana Menteri, Putrajaya',
            'status' => 'submitted',
            'need_driver' => true,
        ]);

        $req = VehicleRequest::first();
        $this->assertStringStartsWith('ASM/UPF/', $req->request_number);
    }

    /** Test Scenario 2: Validation of short notice warning when < 1 day */
    public function test_scenario_02_short_notice_warning_and_validation(): void
    {
        $today = Carbon::today()->format('Y-m-d');

        $response = $this->actingAs($this->applicant)->post(route('requests.store'), [
            'applicant_name' => 'Mohd Azim',
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_department' => 'Unit Komunikasi Korporat',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $today,
            'start_time' => '14:00',
            'end_date' => $today,
            'end_time' => '17:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'Kementerian Sains, Teknologi dan Inovasi (MOSTI)',
            'purpose' => 'Penyerahan Laporan Tahunan Pentadbiran',
            'need_driver' => '1',
        ]);

        // Form allows submission but marks is_short_notice = true
        $this->assertDatabaseHas('vehicle_requests', [
            'destination' => 'Kementerian Sains, Teknologi dan Inovasi (MOSTI)',
            'is_short_notice' => true,
        ]);
    }

    /** Test Scenario 3: Pre-submission availability check endpoint */
    public function test_scenario_03_pre_submission_availability_check_api(): void
    {
        $targetDate = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($this->applicant)->postJson(route('requests.check-availability'), [
            'start_date' => $targetDate,
            'start_time' => '10:00',
            'end_date' => $targetDate,
            'end_time' => '12:00',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'is_short_notice',
            'warning_message',
            'drivers',
            'vehicles',
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['drivers']);
        $this->assertNotEmpty($data['vehicles']);
    }

    /** Test Scenario 4: UPF receives and reviews request */
    public function test_scenario_04_upf_reviews_request(): void
    {
        $targetDate = Carbon::tomorrow()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0001',
            'user_id' => $this->applicant->id,
            'applicant_name' => $this->applicant->name,
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $targetDate,
            'start_time' => '10:00',
            'end_date' => $targetDate,
            'end_time' => '12:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'MOSTI, Putrajaya',
            'purpose' => 'Mesyuarat Bajet STI',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->upf)->post(route('upf.review', $req->id), [
            'action' => 'under_review',
            'review_notes' => 'Permohonan dalam semakan oleh Pegawai UPF.',
        ]);

        $response->assertRedirect();
        $this->assertEquals('under_review', $req->fresh()->status);
    }

    /** Test Scenario 5: UPF assigns driver and vehicle */
    public function test_scenario_05_upf_assigns_driver_and_vehicle(): void
    {
        $targetDate = Carbon::tomorrow()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0002',
            'user_id' => $this->applicant->id,
            'applicant_name' => $this->applicant->name,
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $targetDate,
            'start_time' => '10:00',
            'end_date' => $targetDate,
            'end_time' => '12:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'MOSTI, Putrajaya',
            'purpose' => 'Mesyuarat Bajet STI',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->upf)->post(route('upf.assign', $req->id), [
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'assigned_smart_tag' => '1',
            'assigned_fuel_card' => '1',
            'assigned_gps' => '0',
            'upf_notes' => 'Ditetapkan kenderaan Accord dan pemandu Fahizal.',
        ]);

        $response->assertRedirect();
        $freshReq = $req->fresh();
        $this->assertEquals('assigned', $freshReq->status);
        $this->assertEquals($this->driver->id, $freshReq->assigned_driver_id);
        $this->assertEquals($this->accord->id, $freshReq->assigned_vehicle_id);
        $this->assertTrue((bool) $freshReq->assigned_smart_tag);
    }

    /** Test Scenario 6: Conflict detection flags collision on overlapping schedule */
    public function test_scenario_06_conflict_detection_engine(): void
    {
        $targetDate = Carbon::tomorrow()->format('Y-m-d');

        // Existing booking 10:00 AM - 1:00 PM with Accord and Fahizal
        VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0010',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'Pegawai A',
            'applicant_position' => 'Pegawai',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'pegawaiA@test.gov.my',
            'start_date' => $targetDate,
            'start_time' => '10:00',
            'end_date' => $targetDate,
            'end_time' => '13:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'Putrajaya',
            'purpose' => 'Tugasan 1',
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'status' => 'assigned',
        ]);

        // Second booking overlapping: 11:00 AM - 2:00 PM
        $req2 = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0011',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'Pegawai B',
            'applicant_position' => 'Pegawai',
            'applicant_phone' => '019-7654321',
            'applicant_email' => 'pegawaiB@test.gov.my',
            'start_date' => $targetDate,
            'start_time' => '11:00',
            'end_date' => $targetDate,
            'end_time' => '14:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'Cyberjaya',
            'purpose' => 'Tugasan 2',
            'status' => 'submitted',
        ]);

        // Attempt assignment without override -> should be rejected with conflict_error
        $response = $this->actingAs($this->upf)->post(route('upf.assign', $req2->id), [
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'override_conflict' => '0',
        ]);

        $response->assertSessionHas('conflict_error');
        $this->assertEquals('submitted', $req2->fresh()->status);

        // Attempt assignment WITH override -> succeeds
        $responseOverride = $this->actingAs($this->upf)->post(route('upf.assign', $req2->id), [
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'override_conflict' => '1',
            'upf_notes' => 'Dibenarkan override oleh Pegawai UPF.',
        ]);

        $responseOverride->assertRedirect();
        $this->assertEquals('assigned', $req2->fresh()->status);
    }

    /** Test Scenario 7 & 8: Driver views tasks and accepts assignment */
    public function test_scenario_07_and_08_driver_accepts_task(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0020',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'ASM President',
            'applicant_position' => 'Presiden',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'president@test.gov.my',
            'start_date' => $today,
            'start_time' => '10:00',
            'end_date' => $today,
            'end_time' => '13:00',
            'origin' => 'Rumah President',
            'destination' => 'Pejabat Perdana Menteri, Putrajaya',
            'purpose' => 'Mesyuarat bersama KSN',
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'status' => 'assigned',
        ]);

        // Driver views tasks page
        $tasksViewResponse = $this->actingAs($this->driverUser)->get(route('driver.tasks'));
        $tasksViewResponse->assertOk();
        $tasksViewResponse->assertSee('Mesyuarat bersama KSN');

        // Driver accepts task
        $acceptResponse = $this->actingAs($this->driverUser)->post(route('driver.tasks.accept', $req->id));
        $acceptResponse->assertRedirect();
        $this->assertEquals('driver_accepted', $req->fresh()->status);
    }

    /** Test Scenario 9: Driver starts trip */
    public function test_scenario_09_driver_starts_trip(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0021',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'ASM President',
            'applicant_position' => 'Presiden',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'president@test.gov.my',
            'start_date' => $today,
            'start_time' => '10:00',
            'end_date' => $today,
            'end_time' => '13:00',
            'origin' => 'Rumah President',
            'destination' => 'PMO',
            'purpose' => 'Mesyuarat KSN',
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'status' => 'driver_accepted',
        ]);

        $response = $this->actingAs($this->driverUser)->post(route('driver.tasks.start', $req->id));
        $response->assertRedirect();
        $this->assertEquals('in_progress', $req->fresh()->status);
    }

    /** Test Scenario 10: Vehicle Handover (Ambil Kenderaan) */
    public function test_scenario_10_vehicle_handover_ambil_kenderaan(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0030',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'Mohd Azim',
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $today,
            'start_time' => '08:00',
            'end_date' => $today,
            'end_time' => '12:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'Cyberjaya',
            'purpose' => 'Bengkel AI',
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'status' => 'driver_accepted',
        ]);

        $response = $this->actingAs($this->upf)->post(route('handovers.store', $req->id), [
            'received_by_name' => 'Mohd Azim',
            'handover_date' => $today,
            'handover_time' => '08:00',
            'start_mileage' => 85200,
            'fuel_level' => 'Penuh',
            'check_body' => '1',
            'check_tyre' => '1',
            'check_smart_tag' => '1',
            'check_fuel_card' => '1',
            'condition_notes' => 'Bersih dan baik',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicle_handovers', [
            'request_id' => $req->id,
            'start_mileage' => 85200,
            'check_smart_tag' => true,
        ]);
    }

    /** Test Scenario 11: Vehicle Return (Pulang Kenderaan & Total KM Calculation) */
    public function test_scenario_11_vehicle_return_and_total_km_calculation(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0031',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'Mohd Azim',
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $today,
            'start_time' => '08:00',
            'end_date' => $today,
            'end_time' => '12:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'Cyberjaya',
            'purpose' => 'Bengkel AI',
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'status' => 'in_progress',
        ]);

        // Handover record with initial meter 85200
        VehicleHandover::create([
            'request_id' => $req->id,
            'vehicle_id' => $this->accord->id,
            'driver_id' => $this->driver->id,
            'handover_by_user_id' => $this->upf->id,
            'received_by_name' => 'Mohd Azim',
            'handover_date' => $today,
            'handover_time' => '08:00',
            'start_mileage' => 85200,
            'fuel_level' => 'Penuh',
        ]);

        // Return vehicle with final meter 85295 (+95 KM)
        $response = $this->actingAs($this->upf)->post(route('handovers.return.store', $req->id), [
            'returned_by_name' => 'Mohd Azim',
            'return_date' => $today,
            'return_time' => '12:30',
            'return_mileage' => 85295,
            'fuel_level' => '3/4',
            'return_smart_tag' => '1',
            'condition_notes' => 'Baik tanpa calar',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicle_returns', [
            'request_id' => $req->id,
            'return_mileage' => 85295,
            'total_km' => 95,
        ]);

        $this->assertEquals('completed', $req->fresh()->status);
        $this->assertEquals(85295, $this->accord->fresh()->current_mileage);
    }

    /** Test Scenario 12: Fuel log record */
    public function test_scenario_12_fuel_log_and_receipt(): void
    {
        $today = Carbon::today()->format('Y-m-d');

        $response = $this->actingAs($this->driverUser)->post(route('fuel.store'), [
            'vehicle_id' => $this->accord->id,
            'driver_id' => $this->driver->id,
            'log_date' => $today,
            'log_time' => '10:30',
            'station_name' => 'Petronas Solaris Serdang',
            'fuel_type' => 'RON 95',
            'liters' => 45.5,
            'price_per_liter' => 2.05,
            'total_amount' => 93.28,
            'payment_method' => 'Kad Inden Petrol',
            'mileage_at_fill' => 85250,
            'receipt_number' => 'RCP-88991',
            'remarks' => 'Isian minyak penuh bagi perjalanan ke Putrajaya',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('fuel_logs', [
            'vehicle_id' => $this->accord->id,
            'station_name' => 'Petronas Solaris Serdang',
            'total_amount' => 93.28,
        ]);
    }

    /** Test Scenario 13: Incident and damage report */
    public function test_scenario_13_incident_reporting(): void
    {
        $today = Carbon::today()->format('Y-m-d');

        $response = $this->actingAs($this->driverUser)->post(route('incidents.store'), [
            'vehicle_id' => $this->accord->id,
            'driver_id' => $this->driver->id,
            'incident_type' => 'kerosakan',
            'severity' => 'minor',
            'incident_date' => $today,
            'incident_time' => '11:15',
            'location' => 'Lebuhraya MEX KM 14',
            'description' => 'Lampu brek belakang kanan tidak menyala (mentol terbakar)',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('incident_reports', [
            'vehicle_id' => $this->accord->id,
            'incident_type' => 'kerosakan',
            'location' => 'Lebuhraya MEX KM 14',
        ]);
    }

    /** Test Scenario 14: Schedule Excel view and CSV export */
    public function test_scenario_14_schedule_excel_view_and_export(): void
    {
        $viewResponse = $this->actingAs($this->upf)->get(route('schedules.excel'));
        $viewResponse->assertOk();
        $viewResponse->assertSee('JADUAL TUGASAN PEMANDU');

        $exportResponse = $this->actingAs($this->upf)->get(route('schedules.export'));
        $exportResponse->assertOk();
        $exportResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    /** Test Scenario 15: Printable official A4 form */
    public function test_scenario_15_printable_official_form(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0099',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'Mohd Azim',
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $today,
            'start_time' => '09:00',
            'end_date' => $today,
            'end_time' => '13:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'Putrajaya',
            'purpose' => 'Mesyuarat Rasmi Bersama KSN',
            'need_driver' => true,
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'status' => 'assigned',
        ]);

        $printResponse = $this->actingAs($this->applicant)->get(route('requests.print', $req->id));
        $printResponse->assertOk();
        $printResponse->assertSee('PERMOHONAN KENDERAAN PEJABAT');
        $printResponse->assertSee('BAHAGIAN A');
        $printResponse->assertSee('BAHAGIAN B');
        $printResponse->assertSee('BAHAGIAN C');
        $printResponse->assertSee('BAHAGIAN D');
    }

    /** Test Scenario 16: Pemohon restricted from master data and incident reporting */
    public function test_scenario_16_applicant_restricted_from_armada_and_damage_reports(): void
    {
        // Pemohon cannot access Vehicle Master
        $this->actingAs($this->applicant)->get(route('vehicles.index'))->assertForbidden();

        // Pemohon cannot access Driver Master
        $this->actingAs($this->applicant)->get(route('drivers.index'))->assertForbidden();

        // Pemohon cannot access Fuel Logs
        $this->actingAs($this->applicant)->get(route('fuel.index'))->assertForbidden();

        // Pemohon cannot report damages/accidents
        $this->actingAs($this->applicant)->get(route('incidents.create'))->assertForbidden();
    }

    /** Test Scenario 17: Driver and UPF can access incident report and fuel log */
    public function test_scenario_17_driver_can_report_damage_and_log_fuel(): void
    {
        $this->actingAs($this->driverUser)->get(route('incidents.index'))->assertOk();
        $this->actingAs($this->driverUser)->get(route('incidents.create'))->assertOk();
        $this->actingAs($this->driverUser)->get(route('fuel.index'))->assertOk();
    }

    /** Test Scenario 18: Applicant can submit request for self-drive */
    public function test_scenario_18_applicant_can_request_self_drive(): void
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $response = $this->actingAs($this->applicant)->post(route('requests.store'), [
            'applicant_name' => 'Mohd Azim',
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $tomorrow,
            'start_time' => '08:30',
            'end_date' => $tomorrow,
            'end_time' => '17:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'Cyberjaya',
            'purpose' => 'Bengkel AI Kebangsaan (Pandu Sendiri)',
            'need_driver' => '0',
            'need_smart_tag' => '1',
        ]);

        $response->assertRedirect();
        $createdReq = VehicleRequest::where('purpose', 'Bengkel AI Kebangsaan (Pandu Sendiri)')->first();
        $this->assertNotNull($createdReq);
        $this->assertFalse((bool) $createdReq->need_driver);

        // View detail displays self-drive badge
        $showResponse = $this->actingAs($this->applicant)->get(route('requests.show', $createdReq->id));
        $showResponse->assertOk();
        $showResponse->assertSee('Pandu Sendiri (Tanpa Pemandu)');
    }

    /** Test Scenario 19: UPF assigns vehicle to applicant without driver due to driver unavailability */
    public function test_scenario_19_upf_assigns_vehicle_for_self_drive_due_to_no_driver(): void
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0200',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'Mohd Azim',
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $tomorrow,
            'start_time' => '09:00',
            'end_date' => $tomorrow,
            'end_time' => '13:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'MOSTI Putrajaya',
            'purpose' => 'Hantar Dokumen Terperingkat',
            'need_driver' => true, // Applicant originally asked for a driver
            'status' => 'submitted',
        ]);

        // UPF assigns vehicle, but leaves driver empty (ketiadaan pemandu)
        $assignResponse = $this->actingAs($this->upf)->post(route('upf.assign', $req->id), [
            'assigned_driver_id' => null,
            'assigned_vehicle_id' => $this->accord->id,
            'assigned_smart_tag' => 1,
            'upf_remarks' => 'Atas ketiadaan pemandu, kenderaan diluluskan kepada pemohon untuk dipandu sendiri.',
        ]);

        $assignResponse->assertRedirect(route('requests.show', $req->id));
        $req->refresh();

        $this->assertEquals('assigned', $req->status);
        $this->assertNull($req->assigned_driver_id);
        $this->assertEquals($this->accord->id, $req->assigned_vehicle_id);

        // Applicant received notification explaining self-drive due to no driver
        $applicantNotification = $this->applicant->notifications()->latest()->first();
        $this->assertNotNull($applicantNotification);
        $this->assertStringContainsString('ketiadaan pemandu', $applicantNotification->message);

        // View detail shows clear feedback to applicant
        $showResponse = $this->actingAs($this->applicant)->get(route('requests.show', $req->id));
        $showResponse->assertOk();
        $showResponse->assertSee('Kenderaan Diberikan Kepada Pemohon (Pandu Sendiri)');
        $showResponse->assertSee('ketiadaan pemandu');
    }

    /** Test Scenario 21: Self-drive vehicle return marks pending UPF inspection */
    public function test_scenario_21_self_drive_return_pending_upf_verification(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0201',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'Mohd Azim',
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $today,
            'start_time' => '08:00',
            'end_date' => $today,
            'end_time' => '12:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'KPT Putrajaya',
            'purpose' => 'Mesyuarat Geran STI',
            'need_driver' => false,
            'assigned_driver_id' => null,
            'assigned_vehicle_id' => $this->accord->id,
            'status' => 'in_progress',
        ]);

        VehicleHandover::create([
            'request_id' => $req->id,
            'vehicle_id' => $this->accord->id,
            'driver_id' => null,
            'handover_by_user_id' => $this->upf->id,
            'received_by_name' => 'Mohd Azim',
            'handover_date' => $today,
            'handover_time' => '08:00',
            'start_mileage' => 85200,
            'fuel_level' => 'Penuh',
        ]);

        // Applicant records self-drive return
        $response = $this->actingAs($this->applicant)->post(route('handovers.return.store', $req->id), [
            'returned_by_name' => 'Mohd Azim',
            'return_date' => $today,
            'return_time' => '12:30',
            'return_mileage' => 85280,
            'fuel_level' => '3/4',
            'condition_notes' => 'Kenderaan dipulangkan dalam keadaan baik.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicle_returns', [
            'request_id' => $req->id,
            'returned_by_name' => 'Mohd Azim',
            'received_by_user_id' => null,
            'is_upf_verified' => false,
            'upf_condition_status' => 'Menunggu Pengesahan UPF',
        ]);

        // Viewing detail page shows pending UPF verification
        $showResponse = $this->actingAs($this->applicant)->get(route('requests.show', $req->id));
        $showResponse->assertOk();
        $showResponse->assertSee('MENUNGGU PENGESAHAN KEADAAN FIZIKAL OLEH UPF');
    }

    /** Test Scenario 22: UPF verifies vehicle condition upon return */
    public function test_scenario_22_upf_verifies_vehicle_condition(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $req = VehicleRequest::create([
            'request_number' => 'ASM/UPF/2026/0202',
            'user_id' => $this->applicant->id,
            'applicant_name' => 'Mohd Azim',
            'applicant_position' => 'Pegawai Eksekutif',
            'applicant_phone' => '019-1234567',
            'applicant_email' => 'azim@test.gov.my',
            'start_date' => $today,
            'start_time' => '08:00',
            'end_date' => $today,
            'end_time' => '12:00',
            'origin' => 'ASM MATRADE',
            'destination' => 'KPT Putrajaya',
            'purpose' => 'Mesyuarat Geran STI',
            'need_driver' => false,
            'assigned_driver_id' => null,
            'assigned_vehicle_id' => $this->accord->id,
            'status' => 'completed',
        ]);

        VehicleHandover::create([
            'request_id' => $req->id,
            'vehicle_id' => $this->accord->id,
            'driver_id' => null,
            'handover_by_user_id' => $this->upf->id,
            'received_by_name' => 'Mohd Azim',
            'handover_date' => $today,
            'handover_time' => '08:00',
            'start_mileage' => 85200,
            'fuel_level' => 'Penuh',
        ]);

        $returnRecord = VehicleReturn::create([
            'request_id' => $req->id,
            'vehicle_id' => $this->accord->id,
            'driver_id' => null,
            'received_by_user_id' => null,
            'returned_by_name' => 'Mohd Azim',
            'return_date' => $today,
            'return_time' => '12:30',
            'return_mileage' => 85280,
            'fuel_level' => '3/4',
            'total_km' => 80,
            'is_upf_verified' => false,
            'upf_condition_status' => 'Menunggu Pengesahan UPF',
        ]);

        // UPF verifies condition
        $verifyResponse = $this->actingAs($this->upf)->post(route('handovers.return.verify', $req->id), [
            'upf_condition_status' => 'Baik & Sempurna',
            'upf_verification_notes' => 'Pemeriksaan fizikal kenderaan selesai dan memuaskan.',
        ]);

        $verifyResponse->assertRedirect(route('requests.show', $req->id));

        $returnRecord->refresh();
        $this->assertTrue($returnRecord->is_upf_verified);
        $this->assertEquals($this->upf->id, $returnRecord->upf_verified_by_user_id);
        $this->assertEquals($this->upf->id, $returnRecord->received_by_user_id);
        $this->assertEquals('Baik & Sempurna', $returnRecord->upf_condition_status);
        $this->assertEquals('Pemeriksaan fizikal kenderaan selesai dan memuaskan.', $returnRecord->upf_verification_notes);

        // Vehicle is Available
        $this->assertEquals('Available', $this->accord->fresh()->status);

        // Detail page shows verified badge and UPF officer name
        $showResponse = $this->actingAs($this->applicant)->get(route('requests.show', $req->id));
        $showResponse->assertOk();
        $showResponse->assertSee('DISAHKAN DALAM KEADAAN BAIK & DITERIMA OLEH UPF', false);
        $showResponse->assertSee($this->upf->name);
    }

    /** Test Scenario 23: UPF officer does not have driver personal tasks and is redirected to schedule */
    public function test_scenario_23_upf_does_not_have_driver_tasks_and_is_redirected(): void
    {
        // 1. UPF user visiting driver.tasks is redirected to schedules.excel
        $response = $this->actingAs($this->upf)->get(route('driver.tasks'));
        $response->assertRedirect(route('schedules.excel'));
        $response->assertSessionHas('info');

        // 2. UPF navigation bar does not show 'Tugasan Saya (Pemandu)'
        $navResponse = $this->actingAs($this->upf)->get(route('dashboard'));
        $navResponse->assertOk();
        $navResponse->assertDontSee('Tugasan Saya (Pemandu)');
        $navResponse->assertSee('Jadual Perjalanan');

        // 3. Driver visiting driver.tasks can view their personal task dashboard
        $driverResponse = $this->actingAs($this->driverUser)->get(route('driver.tasks'));
        $driverResponse->assertOk();
        $driverResponse->assertSee('TUGASAN & JADUAL:', false);
        $driverResponse->assertSee($this->driver->name);
    }

    /** Test Scenario 24: Driver can view Master Data Kenderaan but cannot add or edit */
    public function test_scenario_24_driver_can_view_master_data_kenderaan_but_cannot_mutate(): void
    {
        // 1. Driver sees 'Master Data Kenderaan' in menu
        $dashboardResponse = $this->actingAs($this->driverUser)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Master Data Kenderaan');

        // 2. Driver can access vehicles.index and see vehicle list
        $indexResponse = $this->actingAs($this->driverUser)->get(route('vehicles.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('MASTER DATA KENDERAAN');
        $indexResponse->assertSee($this->accord->plate_number);
        // Driver does not see '+ Tambah Kenderaan Baharu'
        $indexResponse->assertDontSee('+ Tambah Kenderaan Baharu');

        // 3. Driver can view vehicle details (vehicles.show)
        $showResponse = $this->actingAs($this->driverUser)->get(route('vehicles.show', $this->accord->id));
        $showResponse->assertOk();
        $showResponse->assertSee($this->accord->plate_number);
        $showResponse->assertDontSee('Kemaskini Maklumat');

        // 4. Driver is forbidden from accessing vehicles.create
        $createResponse = $this->actingAs($this->driverUser)->get(route('vehicles.create'));
        $createResponse->assertForbidden();
    }

    /** Test Scenario 25: Applicant (Pemohon) does not see Kenderaan & Operasi menu */
    public function test_scenario_25_applicant_does_not_see_kenderaan_dan_operasi_menu(): void
    {
        $response = $this->actingAs($this->applicant)->get(route('dashboard'));
        $response->assertOk();

        // Pemohon does not see 'Kenderaan & Operasi' or 'Laporan & Pentadbiran'
        $response->assertDontSee('Kenderaan & Operasi');
        $response->assertDontSee('Log Minyak & Kad Inden');
        $response->assertDontSee('Kerosakan & Kemalangan');
        $response->assertDontSee('Laporan & Pentadbiran');

        // Pemohon sees their own clean applicant navigation
        $response->assertSee('Dashboard');
        $response->assertSee('Permohonan');
        $response->assertSee('Jadual Perjalanan');
        $response->assertSee('Senarai Permohonan Saya');
    }

    /** Test Scenario 26: Driver can view assigned request details (Butiran) without 500 error */
    public function test_scenario_26_driver_can_view_assigned_request_details(): void
    {
        $req = VehicleRequest::create([
            'request_number' => 'REQ-TEST-DRIVER-SHOW',
            'user_id' => $this->applicant->id,
            'applicant_name' => $this->applicant->name,
            'applicant_position' => 'Pegawai Kanan',
            'applicant_phone' => '012-3456789',
            'applicant_email' => $this->applicant->email,
            'start_date' => '2026-10-04',
            'start_time' => '08:00:00',
            'end_date' => '2026-10-04',
            'end_time' => '17:00:00',
            'origin' => 'Klang Sentral',
            'destination' => 'Pusat Latihan Komuniti (PLK) Meru, Klang',
            'purpose' => 'NSC Logistics & Outreach: Pengangkutan Pasukan Outreach & Modul Sains',
            'need_driver' => true,
            'status' => 'assigned',
            'assigned_driver_id' => $this->driver->id,
            'assigned_vehicle_id' => $this->accord->id,
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($this->driverUser)->get(route('requests.show', $req->id));
        $response->assertOk();
        $response->assertSee('REQ-TEST-DRIVER-SHOW');
        $response->assertSee('Pusat Latihan Komuniti (PLK) Meru, Klang');
        $response->assertSee($this->accord->plate_number);
        $response->assertSee('Saya Telah Terima Tugasan');
    }
}
