<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\IncidentReport;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleHandover;
use App\Models\VehicleMaintenance;
use App\Models\VehicleRequest;
use App\Models\VehicleReturn;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            ['name' => 'admin', 'label' => 'Pentadbir Sistem (Admin)', 'description' => 'Akses penuh kepada konfigurasi, audit log dan pengguna.'],
            ['name' => 'upf', 'label' => 'Pegawai UPF (Unit Pengurusan Fasiliti)', 'description' => 'Mengurus permohonan, penugasan pemandu, kenderaan, dan jadual.'],
            ['name' => 'pemohon', 'label' => 'Pemohon / Pegawai ASM', 'description' => 'Membuat permohonan kenderaan dan menyemak status permohonan.'],
            ['name' => 'pemandu', 'label' => 'Pemandu Kenderaan', 'description' => 'Menerima tugasan, log perjalanan, bacaan meter, minyak dan lapor isu.'],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['name' => $r['name']], $r);
        }

        // 2. Settings
        $settings = [
            'min_notice_days' => ['value' => '1', 'description' => 'Tempoh notis minimum permohonan (hari)'],
            'organization_name' => ['value' => 'Akademi Sains Malaysia (ASM)', 'description' => 'Nama Agensi / Organisasi'],
            'unit_name' => ['value' => 'Unit Pengurusan Fasiliti (UPF)', 'description' => 'Nama Unit Pengendali'],
            'office_address' => ['value' => 'Tingkat 20, Sayap Barat, Menara MATRADE, Jalan Sultan Haji Ahmad Shah, 50480 Kuala Lumpur', 'description' => 'Alamat Pejabat ASM'],
            'upf_phone' => ['value' => '03-8319 3200', 'description' => 'No. Telefon UPF'],
            'upf_email' => ['value' => 'upf@akademisains.gov.my', 'description' => 'Emel UPF'],
            'allow_conflict_override' => ['value' => '1', 'description' => 'Membenarkan override penugasan bertindih oleh UPF'],
        ];

        foreach ($settings as $k => $data) {
            Setting::updateOrCreate(['key' => $k], [
                'value' => $data['value'],
                'description' => $data['description'],
            ]);
        }

        // 3. Users
        $defaultPassword = Hash::make('password');

        $admin = User::updateOrCreate(['email' => 'admin@akademisains.gov.my'], [
            'name' => 'Pentadbir Sistem ASM',
            'password' => $defaultPassword,
            'role' => 'admin',
            'department' => 'Unit Teknologi Maklumat',
            'position' => 'Pegawai Teknologi Maklumat F41',
            'phone' => '012-3450001',
            'is_active' => true,
        ]);

        $upfOfficer = User::updateOrCreate(['email' => 'upf@akademisains.gov.my'], [
            'name' => 'Aizat bin Ahmad',
            'password' => $defaultPassword,
            'role' => 'upf',
            'department' => 'Unit Pengurusan Fasiliti (UPF)',
            'position' => 'Pegawai Fasiliti N41',
            'phone' => '019-3344551',
            'is_active' => true,
        ]);

        $upfOfficer2 = User::updateOrCreate(['email' => 'azwa@akademisains.gov.my'], [
            'name' => 'Azwa binti Mansor',
            'password' => $defaultPassword,
            'role' => 'upf',
            'department' => 'Unit Pengurusan Fasiliti (UPF)',
            'position' => 'Penolong Pegawai Tadbir N29',
            'phone' => '013-7788992',
            'is_active' => true,
        ]);

        $staffAzim = User::updateOrCreate(['email' => 'azim@akademisains.gov.my'], [
            'name' => 'Mohd Azim bin Zainal',
            'password' => $defaultPassword,
            'role' => 'pemohon',
            'department' => 'Bahagian Dasar & Inisiatif STI',
            'position' => 'Eksekutif Kanan Dasar',
            'phone' => '012-4455663',
            'is_active' => true,
        ]);

        $staffKamal = User::updateOrCreate(['email' => 'kamal@akademisains.gov.my'], [
            'name' => 'Kamal bin Ariffin',
            'password' => $defaultPassword,
            'role' => 'pemohon',
            'department' => 'Bahagian Jaringan Komuniti & Outreach',
            'position' => 'Pengurus Program',
            'phone' => '017-6655441',
            'is_active' => true,
        ]);

        // Driver users
        $driverUserFahizal = User::updateOrCreate(['email' => 'fahizal@akademisains.gov.my'], [
            'name' => 'Fahizal bin Ramli',
            'password' => $defaultPassword,
            'role' => 'pemandu',
            'department' => 'Unit Pengurusan Fasiliti (UPF)',
            'position' => 'Pemandu Kenderaan Gred H11',
            'phone' => '012-9876541',
            'is_active' => true,
        ]);

        $driverUserIzzul = User::updateOrCreate(['email' => 'izzul@akademisains.gov.my'], [
            'name' => 'Izzul bin Hakimi',
            'password' => $defaultPassword,
            'role' => 'pemandu',
            'department' => 'Unit Pengurusan Fasiliti (UPF)',
            'position' => 'Pemandu Kenderaan Gred H11',
            'phone' => '013-8877662',
            'is_active' => true,
        ]);

        $driverUserShareeza = User::updateOrCreate(['email' => 'shareeza@akademisains.gov.my'], [
            'name' => 'Shareeza bin Ishak',
            'password' => $defaultPassword,
            'role' => 'pemandu',
            'department' => 'Unit Pengurusan Fasiliti (UPF)',
            'position' => 'Pemandu Kenderaan Gred H11',
            'phone' => '019-2233445',
            'is_active' => true,
        ]);

        $staffFathorossoim = User::updateOrCreate(['email' => 'fathorossoim@akademisains.gov.my'], [
            'name' => 'Fathorossoim bin Sulaiman',
            'password' => $defaultPassword,
            'role' => 'pemohon',
            'department' => 'Bahagian Sains, Teknologi & Industri',
            'position' => 'Pegawai Sains C41',
            'phone' => '017-8899001',
            'is_active' => true,
        ]);

        // 4. Drivers Master (Pemandu Rasmi ASM: Fahizal, Izzul, Shareeza)
        $drivers = [
            [
                'user_id' => $driverUserFahizal->id,
                'driver_code' => 'DRV-01',
                'name' => 'Fahizal bin Ramli',
                'staff_number' => 'ASM-P001',
                'ic_number' => '850312-10-5431',
                'position' => 'Pemandu Kenderaan Gred H11',
                'phone' => '012-9876541',
                'license_number' => 'D850312105431',
                'license_class' => 'D, B2',
                'license_expiry' => '2028-03-12',
                'status' => 'Available',
                'emergency_contact_name' => 'Siti Nurhaliza (Isteri)',
                'emergency_contact_phone' => '012-9988776',
                'notes' => 'Pemandu utama Pegawai Tertinggi & Presiden ASM.',
            ],
            [
                'user_id' => $driverUserIzzul->id,
                'driver_code' => 'DRV-02',
                'name' => 'Izzul bin Hakimi',
                'staff_number' => 'ASM-P002',
                'ic_number' => '920718-14-6123',
                'position' => 'Pemandu Kenderaan Gred H11',
                'phone' => '013-8877662',
                'license_number' => 'D920718146123',
                'license_class' => 'D, B2',
                'license_expiry' => '2027-07-18',
                'status' => 'Available',
                'emergency_contact_name' => 'Hakimi bin Zainal (Bapa)',
                'emergency_contact_phone' => '013-4455667',
                'notes' => 'Mahir laluan Lembah Klang dan Putrajaya.',
            ],
            [
                'user_id' => $driverUserShareeza->id,
                'driver_code' => 'DRV-03',
                'name' => 'Shareeza bin Ishak',
                'staff_number' => 'ASM-P003',
                'ic_number' => '881105-08-5911',
                'position' => 'Pemandu Kenderaan Gred H11',
                'phone' => '019-2233445',
                'license_number' => 'D881105085911',
                'license_class' => 'D, B2, E',
                'license_expiry' => '2027-11-05',
                'status' => 'Available',
                'emergency_contact_name' => 'Mariam (Isteri)',
                'emergency_contact_phone' => '019-3322110',
                'notes' => 'Mempunyai lesen E (Bas/Kenderaan Berat).',
            ],
        ];

        $driverModels = [];
        foreach ($drivers as $d) {
            $driverModels[$d['driver_code']] = Driver::updateOrCreate(
                ['driver_code' => $d['driver_code']],
                $d
            );
        }

        // 5. Vehicles Master (9 kenderaan ASM dari borang rujukan)
        $vehicles = [
            [
                'vehicle_code' => 'VEH-01',
                'brand' => 'Honda',
                'model' => 'Accord 2.0 VTi-L',
                'plate_number' => 'W 4949 M',
                'type' => 'Sedan',
                'year' => 2021,
                'color' => 'Hitam Metalik (Crystal Black)',
                'status' => 'Available',
                'current_mileage' => 65420,
                'fuel_type' => 'RON95',
                'last_service_date' => '2026-08-10',
                'next_service_date' => '2026-11-10',
                'next_service_mileage' => 70000,
                'roadtax_expiry' => '2027-05-15',
                'insurance_expiry' => '2027-05-15',
                'insurance_company' => 'Etiqa Takaful',
                'notes' => 'Kenderaan rasmi kegunaan Presiden & Pengurusan Tertinggi ASM.',
            ],
            [
                'vehicle_code' => 'VEH-02',
                'brand' => 'Honda',
                'model' => 'CR-V 1.5 TC-P',
                'plate_number' => 'WSP 3697',
                'type' => 'SUV',
                'year' => 2022,
                'color' => 'Putih Mutiara (Platinum White)',
                'status' => 'Available',
                'current_mileage' => 48150,
                'fuel_type' => 'RON95',
                'last_service_date' => '2026-07-20',
                'next_service_date' => '2026-10-20',
                'next_service_mileage' => 55000,
                'roadtax_expiry' => '2027-06-20',
                'insurance_expiry' => '2027-06-20',
                'insurance_company' => 'Takaful Malaysia',
                'notes' => 'Kenderaan SUV rasmi perjalanan luar daerah & tetamu kehormat.',
            ],
            [
                'vehicle_code' => 'VEH-03',
                'brand' => 'Toyota',
                'model' => 'Innova 2.0G',
                'plate_number' => 'WNV 2434',
                'type' => 'MPV',
                'year' => 2020,
                'color' => 'Perak (Silver Metallic)',
                'status' => 'Maintenance',
                'current_mileage' => 112300,
                'fuel_type' => 'RON95',
                'last_service_date' => '2026-06-15',
                'next_service_date' => '2026-10-05',
                'next_service_mileage' => 115000,
                'roadtax_expiry' => Carbon::today()->subDays(12)->format('Y-m-d'),
                'insurance_expiry' => Carbon::today()->subDays(12)->format('Y-m-d'),
                'insurance_company' => 'Etiqa Takaful',
                'notes' => 'Cukai jalan dan insurans telah tamat tempoh. Sedang dalam tindakan pembaharuan dan penyelenggaraan brek.',
            ],
            [
                'vehicle_code' => 'VEH-04',
                'brand' => 'Proton',
                'model' => 'Persona 1.6 Premium',
                'plate_number' => 'WRF 1300',
                'type' => 'Sedan',
                'year' => 2023,
                'color' => 'Biru (Space Grey/Blue)',
                'status' => 'Available',
                'current_mileage' => 28400,
                'fuel_type' => 'RON95',
                'last_service_date' => '2026-07-02',
                'next_service_date' => '2026-12-02',
                'next_service_mileage' => 35000,
                'roadtax_expiry' => '2027-08-10',
                'insurance_expiry' => '2027-08-10',
                'insurance_company' => 'Zurich Takaful',
                'notes' => 'Kenderaan operasi am pegawai ASM urusan harian.',
            ],
            [
                'vehicle_code' => 'VEH-05',
                'brand' => 'Proton',
                'model' => 'Exora 1.6 Turbo',
                'plate_number' => 'WTS 8675',
                'type' => 'MPV',
                'year' => 2019,
                'color' => 'Coklat Metalik (Rosewood Maroon)',
                'status' => 'Available',
                'current_mileage' => 134800,
                'fuel_type' => 'RON95',
                'last_service_date' => '2026-05-18',
                'next_service_date' => '2026-11-18',
                'next_service_mileage' => 140000,
                'roadtax_expiry' => Carbon::today()->addDays(6)->format('Y-m-d'),
                'insurance_expiry' => Carbon::today()->addDays(6)->format('Y-m-d'),
                'insurance_company' => 'Etiqa Takaful',
                'notes' => 'Cukai jalan & insurans hampir tamat (baki 6 hari). Tindakan pembaharuan sedang diproses oleh UPF.',
            ],
            [
                'vehicle_code' => 'VEH-06',
                'brand' => 'Nissan',
                'model' => 'Serena S-Hybrid 2.0 Highway Star',
                'plate_number' => 'VHA 4950',
                'type' => 'MPV',
                'year' => 2022,
                'color' => 'Hitam (Diamond Black)',
                'status' => 'Available',
                'current_mileage' => 52900,
                'fuel_type' => 'RON95',
                'last_service_date' => '2026-08-15',
                'next_service_date' => '2026-11-15',
                'next_service_mileage' => 60000,
                'roadtax_expiry' => '2027-09-01',
                'insurance_expiry' => '2027-09-01',
                'insurance_company' => 'Takaful Ikhlas',
                'notes' => 'MPV eksekutif kerusi kapten untuk jemputan tetamu antarabangsa.',
            ],
            [
                'vehicle_code' => 'VEH-07',
                'brand' => 'Nissan',
                'model' => 'Almera 1.0 Turbo',
                'plate_number' => 'VHA 4951',
                'type' => 'Sedan',
                'year' => 2023,
                'color' => 'Kelabu (Tungsten Silver)',
                'status' => 'Available',
                'current_mileage' => 31200,
                'fuel_type' => 'RON95',
                'last_service_date' => '2026-06-25',
                'next_service_date' => '2026-10-25',
                'next_service_mileage' => 38000,
                'roadtax_expiry' => Carbon::today()->addDays(20)->format('Y-m-d'),
                'insurance_expiry' => Carbon::today()->addDays(20)->format('Y-m-d'),
                'insurance_company' => 'Takaful Ikhlas',
                'notes' => 'Cukai jalan & insurans akan tamat dalam tempoh 20 hari.',
            ],
            [
                'vehicle_code' => 'VEH-08',
                'brand' => 'Yamaha',
                'model' => 'Lagenda 115Z',
                'plate_number' => 'WNC 5125',
                'type' => 'Motosikal',
                'year' => 2021,
                'color' => 'Merah (Racing Red)',
                'status' => 'Available',
                'current_mileage' => 18900,
                'fuel_type' => 'RON95',
                'last_service_date' => '2026-07-10',
                'next_service_date' => '2026-11-10',
                'next_service_mileage' => 21000,
                'roadtax_expiry' => '2027-03-15',
                'insurance_expiry' => '2027-03-15',
                'insurance_company' => 'Etiqa Takaful',
                'notes' => 'Motosikal despatch penghantaran surat dan dokumen segera.',
            ],
            [
                'vehicle_code' => 'VEH-09',
                'brand' => 'Yamaha',
                'model' => '135LC Fi',
                'plate_number' => 'WWC 1546',
                'type' => 'Motosikal',
                'year' => 2022,
                'color' => 'Biru (Cyan Metallic)',
                'status' => 'Available',
                'current_mileage' => 14250,
                'fuel_type' => 'RON95',
                'last_service_date' => '2026-08-01',
                'next_service_date' => '2026-12-01',
                'next_service_mileage' => 17000,
                'roadtax_expiry' => '2027-04-20',
                'insurance_expiry' => '2027-04-20',
                'insurance_company' => 'Etiqa Takaful',
                'notes' => 'Motosikal operasi pantas UPF untuk urusan bank/agensi kerajaan.',
            ],
        ];

        $vehicleModels = [];
        foreach ($vehicles as $v) {
            $vehicleModels[$v['plate_number']] = Vehicle::updateOrCreate(
                ['plate_number' => $v['plate_number']],
                $v
            );
        }

        // Maintenance record for Innova
        VehicleMaintenance::updateOrCreate([
            'vehicle_id' => $vehicleModels['WNV 2434']->id,
            'service_date' => '2026-09-28',
        ], [
            'maintenance_type' => 'Servis Berkala & Brek',
            'service_mileage' => 112300,
            'workshop_name' => 'Pusat Servis Toyota Cheras',
            'cost' => 840.00,
            'description' => 'Servis minyak enjin sintetik penuh, penukaran minyak brek, dan pad brek hadapan.',
            'next_service_date' => '2027-03-28',
            'next_service_mileage' => 122300,
            'status' => 'In Progress',
        ]);

        // 6. Sample Requests
        // Request 1: Historical completed (1 SEP 2026) - ASM Sec Gen: Mesyuarat bersama KSN
        $req1 = VehicleRequest::updateOrCreate(['request_number' => 'REQ-2026-0001'], [
            'user_id' => $upfOfficer2->id,
            'applicant_name' => 'Azwa binti Mansor',
            'applicant_position' => 'Penolong Pegawai Tadbir N29',
            'applicant_department' => 'Unit Pengurusan Fasiliti (UPF)',
            'applicant_phone' => '013-7788992',
            'applicant_email' => 'azwa@akademisains.gov.my',
            'start_date' => '2026-09-01',
            'start_time' => '07:15:00',
            'end_date' => '2026-09-01',
            'end_time' => '13:00:00',
            'arrival_time' => '08:30:00',
            'origin' => 'No. 13, Jalan Saga SD 8/1, Bandar Seri Damansara',
            'destination' => 'Pejabat Perdana Menteri (PMO), Putrajaya',
            'purpose' => 'ASM Sec Gen: Mesyuarat bersama KSN di PMO Putrajaya',
            'other_passengers' => 'Setiausaha Agung ASM (YBhg. Dato)',
            'need_driver' => true,
            'need_smart_tag' => true,
            'need_fuel_card' => true,
            'need_gps' => false,
            'status' => 'completed',
            'assigned_driver_id' => $driverModels['DRV-03']->id, // Shareeza
            'assigned_vehicle_id' => $vehicleModels['W 4949 M']->id, // Accord
            'assigned_smart_tag' => true,
            'assigned_fuel_card' => true,
            'assigned_gps' => false,
            'assigned_by_user_id' => $upfOfficer->id,
            'assigned_at' => '2026-08-30 14:00:00',
            'driver_accepted_at' => '2026-08-30 14:30:00',
            'trip_started_at' => '2026-09-01 07:10:00',
            'trip_completed_at' => '2026-09-01 12:45:00',
        ]);

        // Handover & Return for Req 1
        VehicleHandover::updateOrCreate(['request_id' => $req1->id], [
            'vehicle_id' => $vehicleModels['W 4949 M']->id,
            'driver_id' => $driverModels['DRV-03']->id,
            'handover_by_user_id' => $upfOfficer->id,
            'received_by_name' => 'Shareeza bin Ishak',
            'handover_date' => '2026-09-01',
            'handover_time' => '07:05:00',
            'start_mileage' => 64980,
            'fuel_level' => 'Full',
            'check_body' => true,
            'check_tyre' => true,
            'check_fuel' => true,
            'check_smart_tag' => true,
            'check_fuel_card' => true,
            'check_gps' => false,
            'check_keys' => true,
            'condition_notes' => 'Kenderaan dalam keadaan bersih dan tiada calar baharu.',
        ]);

        VehicleReturn::updateOrCreate(['request_id' => $req1->id], [
            'vehicle_id' => $vehicleModels['W 4949 M']->id,
            'driver_id' => $driverModels['DRV-03']->id,
            'received_by_user_id' => $upfOfficer->id,
            'returned_by_name' => 'Shareeza bin Ishak',
            'return_date' => '2026-09-01',
            'return_time' => '13:00:00',
            'return_mileage' => 65090,
            'fuel_level' => '3/4',
            'total_km' => 110,
            'return_smart_tag' => true,
            'return_fuel_card' => true,
            'return_gps' => false,
            'return_keys' => true,
            'condition_notes' => 'Kenderaan dipulangkan tepat pada masa, Smart Tag dan Kad Inden diserahkan semula.',
            'has_damage_incident' => false,
            'is_upf_verified' => true,
            'upf_verified_by_user_id' => $upfOfficer->id,
            'upf_verified_at' => '2026-09-01 13:15:00',
            'upf_condition_status' => 'Baik & Sempurna',
            'upf_verification_notes' => 'Kenderaan telah diperiksa secara fizikal dan disahkan diterima balik dalam keadaan baik dan sempurna.',
        ]);

        // Request 2: Historical completed (1 SEP 2026) - ASM President: Mesyuarat bersama KSN
        $req2 = VehicleRequest::updateOrCreate(['request_number' => 'REQ-2026-0002'], [
            'user_id' => $upfOfficer2->id,
            'applicant_name' => 'Azwa binti Mansor',
            'applicant_position' => 'Penolong Pegawai Tadbir N29',
            'applicant_department' => 'Unit Pengurusan Fasiliti (UPF)',
            'applicant_phone' => '013-7788992',
            'applicant_email' => 'azwa@akademisains.gov.my',
            'start_date' => '2026-09-01',
            'start_time' => '07:30:00',
            'end_date' => '2026-09-01',
            'end_time' => '14:00:00',
            'arrival_time' => '08:30:00',
            'origin' => 'Rumah President, Bukit Tunku, KL',
            'destination' => 'Pejabat Perdana Menteri (PMO), Putrajaya',
            'purpose' => 'ASM President: Mesyuarat bersama KSN di Putrajaya',
            'other_passengers' => 'YBhg. Presiden ASM & Pegawai Khas',
            'need_driver' => true,
            'need_smart_tag' => true,
            'need_fuel_card' => true,
            'need_gps' => false,
            'status' => 'completed',
            'assigned_driver_id' => $driverModels['DRV-01']->id, // Fahizal
            'assigned_vehicle_id' => $vehicleModels['WSP 3697']->id, // CRV
            'assigned_smart_tag' => true,
            'assigned_fuel_card' => true,
            'assigned_gps' => false,
            'assigned_by_user_id' => $upfOfficer->id,
            'assigned_at' => '2026-08-30 14:15:00',
            'driver_accepted_at' => '2026-08-30 15:00:00',
            'trip_started_at' => '2026-09-01 07:20:00',
            'trip_completed_at' => '2026-09-01 13:50:00',
        ]);

        // Handover & Return for Req 2
        VehicleHandover::updateOrCreate(['request_id' => $req2->id], [
            'vehicle_id' => $vehicleModels['WSP 3697']->id,
            'driver_id' => $driverModels['DRV-01']->id,
            'handover_by_user_id' => $upfOfficer->id,
            'received_by_name' => 'Fahizal bin Ramli',
            'handover_date' => '2026-09-01',
            'handover_time' => '07:15:00',
            'start_mileage' => 47780,
            'fuel_level' => 'Full',
            'check_body' => true,
            'check_tyre' => true,
            'check_fuel' => true,
            'check_smart_tag' => true,
            'check_fuel_card' => true,
            'check_gps' => false,
            'check_keys' => true,
            'condition_notes' => 'Keadaan CRV tip-top.',
        ]);

        VehicleReturn::updateOrCreate(['request_id' => $req2->id], [
            'vehicle_id' => $vehicleModels['WSP 3697']->id,
            'driver_id' => $driverModels['DRV-01']->id,
            'received_by_user_id' => $upfOfficer->id,
            'returned_by_name' => 'Fahizal bin Ramli',
            'return_date' => '2026-09-01',
            'return_time' => '14:00:00',
            'return_mileage' => 47875,
            'fuel_level' => '3/4',
            'total_km' => 95,
            'return_smart_tag' => true,
            'return_fuel_card' => true,
            'return_gps' => false,
            'return_keys' => true,
            'condition_notes' => 'Perjalanan lancar tanpa sebarang kerosakan.',
            'has_damage_incident' => false,
            'is_upf_verified' => true,
            'upf_verified_by_user_id' => $upfOfficer->id,
            'upf_verified_at' => '2026-09-01 14:15:00',
            'upf_condition_status' => 'Baik & Sempurna',
            'upf_verification_notes' => 'Pemeriksaan fizikal selesai. Kenderaan dan kelengkapan diterima balik dalam keadaan memuaskan.',
        ]);

        // Request 3: TODAY (01 OCT 2026) - ASM President: Mesyuarat KSN & Taklimat Dasar STI
        // Assigned to Fahizal bin Ramli, Vehicle: Honda Accord W 4949 M!
        $req3 = VehicleRequest::updateOrCreate(['request_number' => 'REQ-2026-0003'], [
            'user_id' => $staffAzim->id,
            'applicant_name' => 'Mohd Azim bin Zainal',
            'applicant_position' => 'Eksekutif Kanan Dasar',
            'applicant_department' => 'Bahagian Dasar & Inisiatif STI',
            'applicant_phone' => '012-4455663',
            'applicant_email' => 'azim@akademisains.gov.my',
            'start_date' => '2026-10-01',
            'start_time' => '10:00:00',
            'end_date' => '2026-10-01',
            'end_time' => '15:00:00',
            'arrival_time' => '10:45:00',
            'origin' => 'Rumah President, Bukit Tunku',
            'destination' => 'Pejabat Perdana Menteri, Putrajaya',
            'purpose' => 'ASM President: Mesyuarat KSN & Sesi Taklimat Dasar STI Kebangsaan',
            'other_passengers' => 'ASM President (YBhg. Prof Emeritus) & En. Mohd Azim',
            'need_driver' => true,
            'need_smart_tag' => true,
            'need_fuel_card' => true,
            'need_gps' => false,
            'status' => 'assigned',
            'assigned_driver_id' => $driverModels['DRV-01']->id, // Fahizal
            'assigned_vehicle_id' => $vehicleModels['W 4949 M']->id, // Accord
            'assigned_smart_tag' => true,
            'assigned_fuel_card' => true,
            'assigned_gps' => false,
            'assigned_by_user_id' => $upfOfficer->id,
            'assigned_at' => '2026-09-30 16:30:00',
            'upf_remarks' => 'Sila bersedia 30 minit awal di Rumah President.',
        ]);

        // Request 4: TODAY (01 OCT 2026) - Meeting with AI Malaysia (1.00 PM - 4.00 PM)
        $req4 = VehicleRequest::updateOrCreate(['request_number' => 'REQ-2026-0004'], [
            'user_id' => $staffAzim->id,
            'applicant_name' => 'Mohd Azim bin Zainal',
            'applicant_position' => 'Eksekutif Kanan Dasar',
            'applicant_department' => 'Bahagian Dasar & Inisiatif STI',
            'applicant_phone' => '012-4455663',
            'applicant_email' => 'azim@akademisains.gov.my',
            'start_date' => '2026-10-01',
            'start_time' => '13:00:00',
            'end_date' => '2026-10-01',
            'end_time' => '16:00:00',
            'arrival_time' => '13:30:00',
            'origin' => 'Pejabat ASM, Menara MATRADE',
            'destination' => 'Sri Bestari Private School, Bandar Sri Damansara',
            'purpose' => 'Meeting with AI Malaysia & Science Education Workshop Team',
            'other_passengers' => 'Dr. Suraya & Pn. Nadia (Unit Pendidikan Sains)',
            'need_driver' => true,
            'need_smart_tag' => true,
            'need_fuel_card' => false,
            'need_gps' => false,
            'status' => 'driver_accepted',
            'assigned_driver_id' => $driverModels['DRV-02']->id, // Izzul
            'assigned_vehicle_id' => $vehicleModels['WSP 3697']->id, // CRV
            'assigned_smart_tag' => true,
            'assigned_fuel_card' => true,
            'assigned_gps' => false,
            'assigned_by_user_id' => $upfOfficer->id,
            'assigned_at' => '2026-09-30 15:00:00',
            'driver_accepted_at' => '2026-10-01 08:15:00',
            'upf_remarks' => 'Kelengkapan Smart Tag telah diserahkan di kaunter UPF.',
        ]);

        // Request 5: TOMORROW (02 OCT 2026) - Lawatan Tapak Program Sains Komuniti (Submitted / Pending assignment!)
        $req5 = VehicleRequest::updateOrCreate(['request_number' => 'REQ-2026-0005'], [
            'user_id' => $staffKamal->id,
            'applicant_name' => 'Kamal bin Ariffin',
            'applicant_position' => 'Pengurus Program',
            'applicant_department' => 'Bahagian Jaringan Komuniti & Outreach',
            'applicant_phone' => '017-6655441',
            'applicant_email' => 'kamal@akademisains.gov.my',
            'start_date' => '2026-10-02',
            'start_time' => '08:30:00',
            'end_date' => '2026-10-02',
            'end_time' => '12:30:00',
            'arrival_time' => '09:00:00',
            'origin' => 'Pejabat ASM, Menara MATRADE',
            'destination' => 'Pusat Sains Negara, Bukit Kiara, Kuala Lumpur',
            'purpose' => 'Lawatan Tapak & Pemeriksaan Logistik Program Sains Komuniti',
            'other_passengers' => 'En. Razak (Penolong Pegawai Sains), Pn. Fatin (Pegawai Komunikasi)',
            'need_driver' => true,
            'need_smart_tag' => true,
            'need_fuel_card' => true,
            'need_gps' => false,
            'applicant_remarks' => 'Perlukan ruang but luas untuk membawa 2 kotak bahan pameran robotik.',
            'status' => 'submitted',
        ]);

        // Request 6: UPCOMING (03 OCT 2026) - Penghantaran Dokumen ke MOSTI
        $req6 = VehicleRequest::updateOrCreate(['request_number' => 'REQ-2026-0006'], [
            'user_id' => $upfOfficer2->id,
            'applicant_name' => 'Azwa binti Mansor',
            'applicant_position' => 'Penolong Pegawai Tadbir N29',
            'applicant_department' => 'Unit Pengurusan Fasiliti (UPF)',
            'applicant_phone' => '013-7788992',
            'applicant_email' => 'azwa@akademisains.gov.my',
            'start_date' => '2026-10-03',
            'start_time' => '09:00:00',
            'end_date' => '2026-10-03',
            'end_time' => '13:00:00',
            'arrival_time' => '10:00:00',
            'origin' => 'Pejabat ASM, Menara MATRADE',
            'destination' => 'Kementerian Sains, Teknologi dan Inovasi (MOSTI), Presint 5, Putrajaya',
            'purpose' => 'Penghantaran Dokumen Terperingkat Laporan Tahunan ASM & Bahan Ekspo Inovasi',
            'other_passengers' => 'Tiada',
            'need_driver' => true,
            'need_smart_tag' => true,
            'need_fuel_card' => false,
            'need_gps' => false,
            'status' => 'submitted',
        ]);

        // Request 7: UPCOMING (04 OCT 2026) - Program Outreach Sains Wilayah Selatan
        $req7 = VehicleRequest::updateOrCreate(['request_number' => 'REQ-2026-0007'], [
            'user_id' => $staffKamal->id,
            'applicant_name' => 'Kamal bin Ariffin',
            'applicant_position' => 'Pengurus Program',
            'applicant_department' => 'Bahagian Jaringan Komuniti & Outreach',
            'applicant_phone' => '017-6655441',
            'applicant_email' => 'kamal@akademisains.gov.my',
            'start_date' => '2026-10-04',
            'start_time' => '08:00:00',
            'end_date' => '2026-10-04',
            'end_time' => '17:00:00',
            'arrival_time' => '09:30:00',
            'origin' => 'Klang Sentral',
            'destination' => 'Pusat Latihan Komuniti (PLK) Meru, Klang',
            'purpose' => 'NSC Logistics & Outreach: Pengangkutan Pasukan Outreach & Modul Sains',
            'other_passengers' => '5 orang fasilitator belia sains',
            'need_driver' => true,
            'need_smart_tag' => true,
            'need_fuel_card' => true,
            'need_gps' => true,
            'status' => 'assigned',
            'assigned_driver_id' => $driverModels['DRV-02']->id, // Izzul bin Hakimi
            'assigned_vehicle_id' => $vehicleModels['VHA 4950']->id, // Nissan Serena
            'assigned_smart_tag' => true,
            'assigned_fuel_card' => true,
            'assigned_gps' => true,
            'assigned_by_user_id' => $upfOfficer->id,
            'assigned_at' => '2026-09-30 17:00:00',
        ]);

        // Request 8: SELF-DRIVE COMPLETED TRIP (01 OCT 2026) - Mohd Azim pandu sendiri ke KPT Putrajaya
        // Pemulangan telah direkodkan oleh pemohon, MENUNGGU PENGESAHAN KEADAAN FIZIKAL OLEH UPF
        $req8 = VehicleRequest::updateOrCreate(['request_number' => 'REQ-2026-0008'], [
            'user_id' => $staffAzim->id,
            'applicant_name' => 'Mohd Azim bin Zainal',
            'applicant_position' => 'Eksekutif Kanan Dasar',
            'applicant_department' => 'Bahagian Dasar & Inisiatif STI',
            'applicant_phone' => '012-4455663',
            'applicant_email' => 'azim@akademisains.gov.my',
            'start_date' => '2026-10-01',
            'start_time' => '08:00:00',
            'end_date' => '2026-10-01',
            'end_time' => '12:00:00',
            'arrival_time' => '08:45:00',
            'origin' => 'Pejabat ASM, Menara MATRADE',
            'destination' => 'Kementerian Pendidikan Tinggi (KPT), Presint 5, Putrajaya',
            'purpose' => 'Sesi Meja Bulat Geran Penyelidikan STI & Dana Inovasi Bersama KPT',
            'other_passengers' => 'Pn. Aishah (Eksekutif Kewangan)',
            'need_driver' => false,
            'need_smart_tag' => true,
            'need_fuel_card' => true,
            'need_gps' => false,
            'applicant_remarks' => 'Memohon kebenaran memandu sendiri ke KPT Putrajaya.',
            'status' => 'completed',
            'assigned_driver_id' => null,
            'assigned_vehicle_id' => $vehicleModels['WRF 1300']->id, // Proton Persona
            'assigned_smart_tag' => true,
            'assigned_fuel_card' => true,
            'assigned_gps' => false,
            'assigned_by_user_id' => $upfOfficer->id,
            'assigned_at' => '2026-09-30 11:00:00',
            'upf_remarks' => 'Diluluskan untuk dipandu sendiri oleh pemohon (En. Mohd Azim).',
            'trip_started_at' => '2026-10-01 07:45:00',
            'trip_completed_at' => '2026-10-01 12:15:00',
        ]);

        VehicleHandover::updateOrCreate(['request_id' => $req8->id], [
            'vehicle_id' => $vehicleModels['WRF 1300']->id,
            'driver_id' => null,
            'handover_by_user_id' => $upfOfficer->id,
            'received_by_name' => 'Mohd Azim bin Zainal',
            'handover_date' => '2026-10-01',
            'handover_time' => '07:40:00',
            'start_mileage' => 28400,
            'fuel_level' => 'Full',
            'check_body' => true,
            'check_tyre' => true,
            'check_fuel' => true,
            'check_smart_tag' => true,
            'check_fuel_card' => true,
            'check_gps' => false,
            'check_keys' => true,
            'condition_notes' => 'Kenderaan diserahkan dalam keadaan bersih kepada pemohon.',
        ]);

        VehicleReturn::updateOrCreate(['request_id' => $req8->id], [
            'vehicle_id' => $vehicleModels['WRF 1300']->id,
            'driver_id' => null,
            'received_by_user_id' => null,
            'returned_by_name' => 'Mohd Azim bin Zainal',
            'return_date' => '2026-10-01',
            'return_time' => '12:15:00',
            'return_mileage' => 28478,
            'fuel_level' => '3/4',
            'total_km' => 78,
            'return_smart_tag' => true,
            'return_fuel_card' => true,
            'return_gps' => false,
            'return_keys' => true,
            'condition_notes' => 'Dipulangkan selepas tamat sesi di Putrajaya. Kunci dan kad diserahkan di kaunter UPF.',
            'has_damage_incident' => false,
            'is_upf_verified' => false,
            'upf_verified_by_user_id' => null,
            'upf_verified_at' => null,
            'upf_condition_status' => 'Menunggu Pengesahan UPF',
            'upf_verification_notes' => null,
        ]);

        // 7. Fuel Log Sample
        FuelLog::updateOrCreate(['receipt_number' => 'PET-20260901-098'], [
            'request_id' => $req1->id,
            'vehicle_id' => $vehicleModels['W 4949 M']->id,
            'driver_id' => $driverModels['DRV-03']->id,
            'log_date' => '2026-09-01',
            'log_time' => '12:30:00',
            'station_name' => 'Petronas Presint 9 Putrajaya',
            'fuel_type' => 'RON95',
            'liters' => 38.50,
            'price_per_liter' => 2.05,
            'total_amount' => 78.93,
            'mileage_at_fill' => 65085,
            'payment_method' => 'Kad Inden',
            'receipt_number' => 'PET-20260901-098',
            'remarks' => 'Isian penuh tangki menggunakan Kad Inden Petronas UPF ASM.',
        ]);

        // 8. Incident Report Sample
        IncidentReport::updateOrCreate(['report_number' => 'INC-2026-0001'], [
            'vehicle_id' => $vehicleModels['WNV 2434']->id,
            'driver_id' => $driverModels['DRV-01']->id,
            'reported_by_user_id' => $driverUserFahizal->id,
            'incident_type' => 'Kerosakan',
            'severity' => 'Sederhana',
            'incident_date' => '2026-09-25',
            'incident_time' => '16:45:00',
            'location' => 'Lebuhraya MEX berhampiran Plaza Tol Seri Kembangan',
            'description' => 'Terdapat bunyi geseran tajam pada bahagian brek hadapan kiri ketika memperlahankan kenderaan. Penghawa dingin baris belakang juga kurang sejuk.',
            'status' => 'Under Repair',
            'upf_action_notes' => 'Kenderaan telah dihantar ke Pusat Servis Toyota Cheras untuk pembaikan dan pemeriksaan penuh.',
            'cost' => 840.00,
        ]);

        // 9. Notifications
        Notification::create([
            'user_id' => $driverUserFahizal->id,
            'title' => 'Tugasan Baharu Ditetapkan',
            'message' => 'Anda telah ditugaskan untuk permohonan REQ-2026-0003: Mesyuarat KSN di Putrajaya pada 01 Okt 2026 jam 10:00 AM.',
            'type' => 'info',
            'link' => '/pemandu/tugasan',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $upfOfficer->id,
            'title' => 'Permohonan Kenderaan Baharu',
            'message' => 'Permohonan REQ-2026-0005 diterima daripada Kamal bin Ariffin untuk 02 Okt 2026.',
            'type' => 'info',
            'link' => '/upf/permohonan/5',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $staffAzim->id,
            'title' => 'Permohonan Telah Ditetapkan',
            'message' => 'Permohonan REQ-2026-0003 telah diluluskan. Pemandu: Fahizal bin Ramli, Kenderaan: Honda Accord (W 4949 M).',
            'type' => 'success',
            'link' => '/pemohon/permohonan/3',
            'is_read' => false,
        ]);

        // 10. Audit Logs
        AuditLog::create([
            'user_id' => $upfOfficer->id,
            'user_name' => 'Aizat bin Ahmad',
            'action' => 'Penugasan Pemandu & Kenderaan',
            'module' => 'Permohonan',
            'details' => 'Menetapkan Pemandu: Fahizal bin Ramli dan Kenderaan: Honda Accord W 4949 M untuk REQ-2026-0003.',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::parse('2026-09-30 16:30:00'),
        ]);

        AuditLog::create([
            'user_id' => $driverUserFahizal->id,
            'user_name' => 'Fahizal bin Ramli',
            'action' => 'Laporan Kerosakan Kenderaan',
            'module' => 'Insiden',
            'details' => 'Membuat laporan isu brek dan aircond bagi Toyota Innova WNV 2434 (#INC-2026-0001).',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::parse('2026-09-25 17:00:00'),
        ]);
    }
}
