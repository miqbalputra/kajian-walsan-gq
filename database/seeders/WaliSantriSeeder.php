<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Import Wali Santri data from Excel.
 * 
 * Aturan username:
 * - Ayah: A + NIS anak tertua (Mustawa tertinggi)
 * - Ibu:  B + NIS anak tertua (Mustawa tertinggi)
 * - Password = Username
 *
 * Generated on: 2026-04-14T01:28:48.452Z
 */
class WaliSantriSeeder extends Seeder
{
    public function run(): void
    {
        // Prevent duplicate execution
        if (Student::count() > 0) {
            $this->command?->warn('⚠️  Students table is not empty. Skipping import to prevent duplicates.');
            $this->command?->warn('   To re-import, truncate students, parents, parent_student, and related users first.');
            return;
        }

        $waliSantriRole = Role::where('name', 'wali_santri')->first();
        if (!$waliSantriRole) {
            $this->command?->error('❌ Role "wali_santri" not found. Run migrations first.');
            return;
        }

        DB::beginTransaction();
        try {
            // Step 1: Create classes
            $classMap = $this->createClasses();

            // Step 2: Create students
            $studentMap = $this->createStudents($classMap);

            // Step 3: Create parent accounts and link to students
            $credentials = $this->createFamilies($waliSantriRole, $studentMap);

            DB::commit();

            $this->command?->info('✅ Import completed!');
            $this->command?->info('   Classes: ' . count($classMap));
            $this->command?->info('   Students: ' . count($studentMap));
            $this->command?->info('   Parent accounts: ' . count($credentials));

            // Export credentials CSV
            $this->exportCredentials($credentials);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command?->error('❌ Import failed: ' . $e->getMessage());
            Log::error('WaliSantriSeeder failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    private function createClasses(): array
    {
        $classes = [
            ['name' => 'Mustawa 1 Ikhwan', 'level' => '1'],
            ['name' => 'Mustawa 1 Akhwat', 'level' => '1'],
            ['name' => 'Mustawa 2 Ikhwan', 'level' => '2'],
            ['name' => 'Mustawa 2 Akhwat', 'level' => '2'],
            ['name' => 'Mustawa 3 Ikhwan', 'level' => '3'],
            ['name' => 'Mustawa 3 Akhwat', 'level' => '3'],
            ['name' => 'Mustawa 4 Ikhwan', 'level' => '4'],
            ['name' => 'Mustawa 4 Akhwat', 'level' => '4'],
            ['name' => 'Mustawa 5 Ikhwan', 'level' => '5'],
            ['name' => 'Mustawa 5 Akhwat', 'level' => '5'],
            ['name' => 'Mustawa 6 Ikhwan', 'level' => '6'],
            ['name' => 'Mustawa 6 Akhwat', 'level' => '6'],
        ];

        $map = [];
        foreach ($classes as $cls) {
            $record = ClassRoom::firstOrCreate(
                ['name' => $cls['name']],
                ['level' => $cls['level'], 'capacity' => 25, 'is_active' => true]
            );
            $map[$cls['name']] = $record->id;
        }

        return $map;
    }

    private function createStudents(array $classMap): array
    {
        $students = [
            ['0214460290', 'Abdullah', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-09-13', 'Babakan rt 14/04 kec. Kalimanah kab. Purbalingga', null],
            ['0214460291', 'Abdullah Qays', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-10-22', 'Klapasaeit Rt 02 Rw 01 Kalimanah Purbalingga', null],
            ['0214460292', 'Abdulloh', 'Mustawa 1 Ikhwan', 'L', 'Padang', '2018-04-25', 'Desa Lemberang Rt 1 Rw 1, Kecamatan Sokaraja, Kabupaten Banyumas', null],
            ['0214460293', 'Faatih', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-02-22', 'Kedungbenda RT 02 RW 12 kecamatan Kemangkon,Kabupaten Purbalingga', null],
            ['0214460294', 'Haikal Omar Alshehab', 'Mustawa 1 Ikhwan', 'L', 'Cirebon', '2018-05-18', 'Desa banjaranyar RT/RW 03/08 kec sokaraja kab Banyumas', null],
            ['0214460295', 'Hisyam Abdurrahman Amri', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-04-02', 'Kedungjati RT 02 RW 01, Kecamatan Bukateja, Kabupaten Purbalingga', null],
            ['0214460296', 'Mohamad Al Fatih', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-02-19', 'Klapasawit RT 03 RW 06 Kec Kalimanah Kab Purbalingga Jawa Tengah', '-'],
            ['0214460297', 'Muadz Ar-Rasyid', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2019-03-02', 'Komplek Kavling Tunas Ilmu Blok B1 no.01, Dukuhcakra RT.04/04 Desa Kedungwuluh, Kec. Kalimanah, Kab. Purbalingga, Jawa Tengah', null],
            ['0214460298', 'Muhammad Abdurrahman', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2019-03-29', 'Desa karang lewas RT 10 RW 05 kecamatan Kutasari,kabupaten Purbalingga', null],
            ['0214460299', 'Muhammad Alfarizy Rasyid', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2017-11-01', 'Jalan Mandalika RT 5/5 Selabaya, Kalimanah', null],
            ['0214460300', 'Muhammad Hamzah Al-Hafizh', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-12-12', 'Wirasana RT 001/RW002 Kecamatan Purbalingga, Kabupaten Purbalingga', null],
            ['0214460301', 'Muhammad Tsaqib Fawwaz', 'Mustawa 1 Ikhwan', 'L', 'Banyumas', '2018-06-13', 'Tambaksogra RT 09 RW 02, Kecamatan Sumbang, Kabupaten Banyumas', null],
            ['0214460302', 'Muhammad Uwais Alqarny', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-05-30', 'Adoarsa,rt 8,rw 4,kecamatan kertanegara,kabupaten purbalingga', null],
            ['0214460303', 'Musyaffa Abdullah', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-09-07', 'Banjaran RT 8 RW 4 Kecamatan Bojongaari Kabupaten Purbalingga', null],
            ['0214460304', 'Sufyan Ats Tsauri', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-08-10', 'Bojong, RT 01 RW 03 Kecamatan Purbalingga, Kabupaten Purbalingga', null],
            ['0214460305', 'Usamah Abdurrahman Ayyub', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-10-29', 'Dusun Melung RT 04 RW 05 Desa Larangan Kecamatan Pengadegan Kabupaten Purbalingga', null],
            ['0214460306', 'Utsman', 'Mustawa 1 Ikhwan', 'L', 'Banyumas', '2017-12-08', 'RT 1 RW 7 kecamatan Kalimanah, kabupaten Purbalingga', null],
            ['0214460307', 'Uwaies Abdul Lathief', 'Mustawa 1 Ikhwan', 'L', 'Purbalingga', '2018-10-17', 'Desa Karanggedang RT.04 RW.01 kecamatan karanganyar kabupaten purbalt', null],
            ['0214460308', 'Zain Attaqi', 'Mustawa 1 Ikhwan', 'L', 'BANYUMAS', '2019-05-05', 'Desa Karangturi RT 06 RW 01. kecamatan Sumbang Kabupaten Banyumas', null],
            ['0214460309', 'Zehyaan Ar Rayyan', 'Mustawa 1 Ikhwan', 'L', 'Banjarnegara', '2019-05-15', 'Jompo RT 03 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214450246', 'Abdul Mu\'iz', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2017-01-20', 'Desa selaganggeng RT 02 RW 02, kecamatan mrebet, kabupaten Purbalingga', null],
            ['0214450247', 'Abdullah Yusuf Alfarisi', 'Mustawa 2 Ikhwan', 'L', 'Banyumas', '2017-07-12', 'Jl. Riyanto GG. Mawar III no 265a, Kel. Sumampir, Kec. Purwokerto Utara, Kab. Banyumas', null],
            ['0214450248', 'Abu Abdillah Ahsan Faozi', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2017-06-14', 'Pegandekan RT 001 RW 002, Kecamatan Kemangkon, Kabupaten Purbqlingga', 'Sefi Mardianti'],
            ['0214450249', 'Ahmad', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2017-01-09', 'Griya Kalika B9 Kedungwuluh, Kalimanah Purbalingga', null],
            ['0214450250', 'Bilal', 'Mustawa 2 Ikhwan', 'L', 'banyumas', '2016-12-10', 'kedungwuluh rt 8 rw 2 kalimanah purbalingga', null],
            ['0214450251', 'Daniyal Abdul Ghoniy', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2016-07-27', 'Desa Bukateja RT 04 RW 02 ,kec.Bukateja,kab.Purbalingga', null],
            ['0214450252', 'Ibrahim', 'Mustawa 2 Ikhwan', 'L', 'Banyumas', '2017-10-15', 'Banjarsari Kidul RT 01 RW 04 Kecamatan Sokaraja Kabupaten Banyumas', null],
            ['0214450253', 'Muhammad Adam', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2017-07-22', 'Kedungwuluh, karangwinong. Kec:kalimanah kab;purbalinggart02/04', 'Bpk yuswiarjo'],
            ['0214450254', 'Muhammad Ahnaf Elfathan', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2025-08-31', 'Klapasawit RT 02 RW 07,Kecamatan Kalimanah Kabupaten Purbalingga', null],
            ['0214450255', 'Muhammad Syaddad Afuw Rutka', 'Mustawa 2 Ikhwan', 'L', 'Tegal', '2018-03-29', 'Kober RT 3 RW 9 Kec.Purwokerto barat Banyumas', null],
            ['0214450256', 'Musa Putra Wibowo', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2017-12-16', 'Purbalingga Lor Rt3 Rw3, kecamatan Purbalingga, kabupaten Purbalingga', null],
            ['0214450257', 'Naoki Hafiz Azzikri', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2017-09-15', 'Klapasawit RT 03/05 kecamatan kalimanah kabupaten Purbalingga', 'Tidak Ada'],
            ['0214450258', 'Raffasya Arkha Syahreza', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2017-11-26', 'Jln.Puring Rt 01 Rw 04 Nmr 9A', null],
            ['0214450259', 'Syafiq Abdullah', 'Mustawa 2 Ikhwan', 'L', 'Banjarnegara', '2018-03-26', 'Desa Blimbing RT.05/02 kec.mandirja', 'Tidak ada'],
            ['0214450260', 'Syafiq Athar Ubaidillah', 'Mustawa 2 Ikhwan', 'L', 'PURBALINGGA', '2017-09-16', 'Purbalingga Wetan RT 01 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', null],
            ['0214450261', 'Tolhah', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2014-05-05', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214450262', 'Ukasyah Abdurrahman', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2017-07-28', 'Jetis rt 14 rw 05, Kecamatan Kemangkon, Kabupaten Purbalingga', null],
            ['0214450263', 'Ukasyah Kalifa Fil Ardh', 'Mustawa 2 Ikhwan', 'L', 'Banyumas', '2017-08-10', 'Banjar Anyar RT 01 RW 05, Kecamatan Sokaraja, Kabupaten Banyumas', null],
            ['0214450264', 'Urwah Abdurrahman Al Huwairy', 'Mustawa 2 Ikhwan', 'L', 'Purbalingga', '2018-05-27', 'Karangcegak RT 03 RW 01 Kecamatan Kutasari Kabupaten Purbalingga', null],
            ['0214450265', 'Zulfadhli Amru Mubarak', 'Mustawa 2 Ikhwan', 'L', 'Banyumas', '2016-12-03', 'Karangwangkal RT 03 RW 01 Purwokerto Utara', 'Tidak ada'],
            ['0214440201', 'Abdillah Fajri Assajid', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-05-21', 'Jl. Mawar 487 Rt. 4 Rw. 4 kalimantan wetan  kec. kalimantan kab.Purbalingga', null],
            ['0214440202', 'Abdullah Al Faqih', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-01-30', 'Dukuhcakra rt4/rw4 kec. Kalimanah kab. Purbalingga', null],
            ['0214440203', 'Abdurrahman', 'Mustawa 3 Ikhwan', 'L', 'Sidoarjo', '2016-11-12', 'Padamara, RT 03 RW 03, Kecamatan Padamara, Kabupaten Purbalingga', null],
            ['0214440204', 'Abdurrofi Malikal Khakim', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-11-25', 'Kalimanah wetan RT 04 RW 07, kecamatan Kalimanah, kabupaten Purbalingga', null],
            ['0214440205', 'Ahmad Sinaan', 'Mustawa 3 Ikhwan', 'L', 'Banyumas', '2016-07-22', 'Babakan Asri Blok A12, Babakan, Kec. Kalimanah, Kab. Purbalingga', null],
            ['0214440206', 'Ahmad Yahya Ayyash', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-07-20', 'Lambur RT 01 RW 01,kecamatan mrebet, Kabupaten purbalingga', 'Tidak ada'],
            ['0214440207', 'Daffa Akar Ibrahim', 'Mustawa 3 Ikhwan', 'L', 'Bekasi', '2015-04-14', 'Kalikajar RT 03 RW 08 kecamatan Kaligondang kabupaten Purbalingga', null],
            ['0214440208', 'Fawaz Baihaqi Al Khairy', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2017-01-21', 'Bojanegara rt 5 rw 1 padamara, Purbalingga ( sedang tinggal di kedungwuluh rt 6 rw 1)', null],
            ['0214440209', 'Hisyam Abdurrozzaq', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-03-29', 'Karangpule RT 03 RW 02, Kecamatan Padamara, Kabupaten Purbalingga', 'Tidak ada'],
            ['0214440210', 'Muhammad Al Fatih', 'Mustawa 3 Ikhwan', 'L', 'Banyumas', '2017-02-15', 'Kramat Rt 05 Rw 01, Kecamatan Kembaran Kabupaten Banyumas', 'Paryono (kakek)'],
            ['0214440211', 'Muhammad Reyhan Al Hafidz', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-10-13', 'Purbalingga Kidul RT 01 RW 04, Kecamatan Purbalingga, Kabupaten Purbalingga', null],
            ['0214440212', 'Muhammad Syafii Erlangga', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2017-01-01', 'Karang anayr rt02/01 Kec Karamganyar kab purbalingga', 'Toda ada'],
            ['0214440213', 'Muhammad Syafiq Arsy Saepuloh', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-06-27', 'Selabaya RT 02 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214440214', 'Musa Bin Imam Fajar Subekhi', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2015-11-04', 'Bojongsari rt 02 rw 05 purbalingga', null],
            ['0214440215', 'Musa Bin Mingan Sutomo', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-05-05', 'Kedungwuluh RT 07 RW 01,Kecamatan Kalimanah,Kabupaten Purbalingga', 'Ibu Mutinggah'],
            ['0214440216', 'Radja Adnan Rifti Arsenio', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2015-10-28', 'Jl kenanga no 1 RT 3 RW 7 perumahan penambongan Purbalingga', null],
            ['0214440217', 'Raihan Firdaus', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-08-03', 'Babakan 6/2 Kalimanah Purbalingga', null],
            ['0214440218', 'Umar \'Abdurrozaq', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2016-08-25', 'Carangmanggang RT 16 RW 06 karangbanjar bojongsari purbalingga', null],
            ['0214440219', 'Usman Abdurrofi', 'Mustawa 3 Ikhwan', 'L', 'Purbalingga', '2017-01-28', 'Desa Karangbanjar Rt19 rw08 kecamatan bojongsari kabupaten Purbalingga', null],
            ['0214440220', 'Yahya Abdullah', 'Mustawa 3 Ikhwan', 'L', 'Banyumas', '2017-02-07', 'Kedungwuluh Rt 08 Rw 02', null],
            ['0214430147', 'Abdul Sakha Mubarakh', 'Mustawa 4 Ikhwan', 'L', 'Jakarta Timur', '2025-08-28', 'Grecol RT 05 RW 01 Kecamatan Kalimanah ,Kabupaten Purbalingga', 'Tidak ada'],
            ['0214430149', 'Abdulloh Syamil', 'Mustawa 4 Ikhwan', 'L', 'Purbalingga', '2015-06-29', 'Ds.Walik Kutasari RT 16 RW 08 Kecamatan Kutasari, Kabupaten Purbalingga', null],
            ['0214430150', 'Abdurrahman', 'Mustawa 4 Ikhwan', 'L', 'Purbalingga', '2016-02-08', 'Kedungwuluh RT 06 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214430151', 'Abdurrohman Bin Tsabit', 'Mustawa 4 Ikhwan', 'L', 'Purbalingga', '2015-02-18', 'Kedungwuluh RT 07 RW 02 Kecamatan  Kalimanah Kabupaten  Purbalingga', null],
            ['0214430152', 'Ahsanul Faqih M', 'Mustawa 4 Ikhwan', 'L', 'Bekasi', '2014-08-12', 'Karang petir RT 01 RW 01, kecamatan Kalimanah, kabupaten Purbalingga', 'Hadi Priyanto'],
            ['0214460286', 'Athallah Hafiz Nurseto', 'Mustawa 4 Ikhwan', 'L', 'Purwokerto', '2015-08-28', 'Ds. Suro RT 06/04, Karangjati, Kecamatan Kalibagor, Kabupaten Banyumas, Jawa Tengah, Indonesia 53193', null],
            ['0214430154', 'Fatih Abdurahman Ayyub', 'Mustawa 4 Ikhwan', 'L', 'Banjarnegara', '2015-05-23', 'Desa Blimbing RT.05/02 kec.Mandiraja', 'Tidak ada'],
            ['0214430155', 'Guinandra Amzar Khalfani Iskandar', 'Mustawa 4 Ikhwan', 'L', 'Sleman, Jogjakarta', '2016-03-09', 'Grumbul kikiran, desa Silado, Sumbang, banyumas', null],
            ['0214430156', 'Haikal Hafiz Pratama', 'Mustawa 4 Ikhwan', 'L', 'Purbalingga', '2015-01-13', 'Kalikabong rt2/rw1.kec.kalimanah.kab.purbalingga.sekarang domisili klahang rt1/rw6.kec.sokaraja.kab.banyumas', null],
            ['0214430157', 'Hamzah Nail', 'Mustawa 4 Ikhwan', 'L', 'Purbalingga', '2015-11-17', 'Jl Komisaris Noto Soemarsono 124 b  RT 03/02 Purbalingga 53313', null],
            ['0214430158', 'Ibrohim Putra Wibowo', 'Mustawa 4 Ikhwan', 'L', 'Purbalingga', '2016-08-01', 'Purbalingga Lor RT 3 RW 3', null],
            ['0214430160', 'Kahfi El Azzam', 'Mustawa 4 Ikhwan', 'L', 'Purbalingga', '2015-07-28', 'Klapasawit RT 04 RW 05, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214430162', 'Muhamad Faris', 'Mustawa 4 Ikhwan', 'L', 'Banyumas', '2015-10-28', 'Banyumas Rt 01/04, kecamatan Banyumas, Kabupaten Banyumas', 'Tidak ada'],
            ['0214430163', 'Mustofa Al Huda', 'Mustawa 4 Ikhwan', 'L', 'Purbalingga', '2015-10-16', 'Kalikabong RT 01/ RW 01, Kecamatan Kalimanah, Kabupaten PurbalinggaP', 'Dwio Ratono'],
            ['0214430164', 'Unais Ainurrofiq', 'Mustawa 4 Ikhwan', 'L', 'Banyumas', '2015-01-20', 'Kotayasa RT 08 RW 05, Kecamatan Sumbang, Kabupaten, Banyumas,', 'Darsun'],
            ['0214430166', 'Yazid Ghani Abdillah', 'Mustawa 4 Ikhwan', 'L', 'Bekasi', '2015-12-02', 'Kedungwuluh 02/04 Kec.Kalimanah Kab.Purbalingga.', null],
            ['0214420107', 'Abdullah Syaukani', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2015-05-16', 'Kedungwuluh RT 05 RW 01, Kalimanah, Purbalingga', null],
            ['0214440190', 'Abdulloh', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2014-10-07', 'Griya kalika B9 Kedungwuluh Kalimanah Purbalingga', null],
            ['0214420108', 'Abdulloh Bilal', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2014-08-05', 'Kedungwuluh rt 07 rw 01, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214420109', 'Adh Dhuha Kaisar Alwi', 'Mustawa 5 Ikhwan', 'L', 'Banyumas', '2013-12-04', 'Klahang RT 02 RW 02 kecamatan Sokaraja Kabupaten Banyumas', null],
            ['0214420111', 'Arzachel Rasyad Mufid', 'Mustawa 5 Ikhwan', 'L', 'Banjarnegara', '2014-12-21', 'Babakan RT 43 RW 11 Kec. Kalimanah Kab. Purbalingga', null],
            ['0214420143', 'Jarvis Gaurav Elzanki', 'Mustawa 5 Ikhwan', 'L', 'Banyumas', '2015-07-17', 'RT 001 RW 005, Kel. Purbalingga Lor, Kec. Purbalingga, Kab. Purbalingga, Jawa Tengah', null],
            ['0214430193', 'Kenzie Rayyan Adhyasta', 'Mustawa 5 Ikhwan', 'L', 'Banjarnegara', '2014-11-15', 'Kedungwuluh, RT 02 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214420113', 'Lutfi Sakhi Zaidan', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2014-04-14', 'Carangmanggang RT 16 RW 06 karangbanjar bojongsari Purbalingga', null],
            ['0214420114', 'Muhammad Alwi Al Rasyid', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2014-04-29', 'Purbalingga Kidul RT 01 RW 04, Kecamatan Purbalingga.kabupaten purbalinggabalingga, Kabupaten Purbalingga', null],
            ['0214420115', 'Muhammad Faiz Al Barra', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2014-06-16', 'Wirasana, RT 02/07 Purbalingga', null],
            ['0214420116', 'Muhammad Mahrez Davitra', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2014-08-27', 'Lambur Rt 01 Rw 01,kecamatan mrebet, Kabupaten purbalingga', 'Tidak ada'],
            ['0214420117', 'Muhammad Syafiq Dwiputra Setiono', 'Mustawa 5 Ikhwan', 'L', 'Karawang', '2015-04-19', 'Desa Munjul Rt 12 RW 06 Kecamatan Kutasari Kabupaten Purbalingga', null],
            ['0214420118', 'Nizar Hilmi Abdullah', 'Mustawa 5 Ikhwan', 'L', 'Limbangan, kutasari, purbalingga', '2014-02-26', 'Limbangan rt 10 rw 05, kecamatan kutasari, kabupaten purbalingga', null],
            ['0214420119', 'Rayhan Mirza Santoso', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2014-03-29', 'Perum Grita Perwira Asri, blok B no. 9, Babakan Purbalingga', 'Titi Hadiah Milihani'],
            ['0214420120', 'Said Ibnu Abdurrohman', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2014-07-25', 'Babakan RT 21 RW 06 kecamatan Kalimanah kabupaten Purbalingga', null],
            ['0214420121', 'Ubay Ibnu Abdullah', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2014-07-06', 'Jl Wiraguna no 16 purbalingga kidul, purbalingga.', null],
            ['0214420122', 'Ubay Zufar', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '1983-04-13', 'Jl Komisaris Noto Soemarsono 124 b  RT03/02 Purbalingga 53313', null],
            ['0214420123', 'Umair Abdurrozzaq Ar Royyan', 'Mustawa 5 Ikhwan', 'L', 'Banyumas', '2013-03-19', 'Kotayasa RT 08 Rw 02, kecamatan sumbang, kabupaten Banyumas', 'Darsun'],
            ['0214420124', 'Yusuf Hanif Alghifari', 'Mustawa 5 Ikhwan', 'L', 'Banyumas', '2014-11-19', 'Selabaya/ Ruko selabaya indah jl geriliya barat', 'Tidak Ada'],
            ['0214420125', 'Zaidan Putra Arroyan', 'Mustawa 5 Ikhwan', 'L', 'Purbalingga', '2013-04-08', 'Karang jambe rt 02/rw 03.kec.padamara.kab.purbalingga', 'Tidak ada'],
            ['0214410082', 'Abdul Barr', 'Mustawa 6 Ikhwan', 'L', 'Purbalingga', '2013-12-25', 'Klapasawit RT 01 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214410084', 'Abdul Rozaq', 'Mustawa 6 Ikhwan', 'L', 'Purbalingga', '2013-05-16', 'Karanglewas RT 11 RW 05, kecamatan Kutasari,kabupaten Purbalingga', 'Tidak ada'],
            ['0214410078', 'Abdullah Hudzaifah Anggoro', 'Mustawa 6 Ikhwan', 'L', 'Banyumas', '2014-02-14', 'Perum. Berlian Karangsentul Blok F No. 3, RT 02 RW 05, Desa Bojanegara, Kecamatan Padamara, Kabupaten Purbalingga', null],
            ['0214410080', 'Abdurahman Faruh', 'Mustawa 6 Ikhwan', 'L', 'Banyumas', '2013-02-28', 'Kalisube Rt 01/04. Banyumas', 'Tidak ada'],
            ['0214410083', 'Abdurrahman Faid', 'Mustawa 6 Ikhwan', 'L', 'Purbalingga', '2013-09-14', 'Dukuhcakra rt 4/rw4 kec. Kalimanah kab. Purbalingga', null],
            ['0214410086', 'Adam', 'Mustawa 6 Ikhwan', 'L', 'Banyumas', '2013-10-29', 'Perum Abdi Negara, Kresna IV No 2. Bojaneraga, Padamara - Purbalingga', null],
            ['0214410081', 'Dafa Khusna Aulia Pradipta Al-Gifari', 'Mustawa 6 Ikhwan', 'L', 'Purbalingga', '2013-07-17', 'Galuh RT 08 RW 04, Bojongsari, Purbalingga', null],
            ['0214410051', 'Faisal Imam Khalil', 'Mustawa 6 Ikhwan', 'L', 'Purbalingga', '2014-01-09', 'Perum trajumalang blok C1, Kandang gampang RT 04/02, kec. Purbalingga, Purbalingga', null],
            ['0214410085', 'Hafiz Azka Jibran', 'Mustawa 6 Ikhwan', 'L', 'Purbalingga', '2013-05-22', 'Selabaya RT 02 RW 04 kecamatan Kalimanah kabupaten Purbalingga', null],
            ['0214410088', 'Ibrahim Nusantara Putra Susanto', 'Mustawa 6 Ikhwan', 'L', 'Banyumas', '2014-05-23', 'Sokawera RT 02 RW 04, Kec. Padamara Kab. Purbalingga', null],
            ['0214450243', 'Muhammad Dandi Pratama', 'Mustawa 6 Ikhwan', 'L', 'Banjarnegara', '2013-05-28', 'Kavling Tunas Ilmu Depok Kedungwuluh RT 3 RW 3 Kalimanah', 'Agus Sutomo'],
            ['0214410091', 'Muhammad Labib', 'Mustawa 6 Ikhwan', 'L', 'Purbalingga', '2013-09-10', 'Pekalongan,RT02 RW04 kecamatan Bojongsari, kabupaten Purbalingga', null],
            ['0214440198', 'Muhammad Yazid Al Fajr', 'Mustawa 6 Ikhwan', 'L', 'Depok', '2013-08-19', 'Graha Permata Selabaya No G14 Selabaya Kalimanah', null],
            ['0214410089', 'Ukasyah Abdullah Al Atsary', 'Mustawa 6 Ikhwan', 'L', 'Banyumas', '2014-03-29', 'Karangcegak RT 03 RW 01 Kecamatan Kutasari Kabupaten Purbalingga', null],
            ['0214440199', 'Utbah', 'Mustawa 6 Ikhwan', 'L', 'Purbalingga', '2014-03-01', 'Kedungmenjangan RT 01 RW 03, Kecamatan Purbalingga, Kabupaten Purbalingga', null],
            ['0214440192', 'Yazid', 'Mustawa 6 Ikhwan', 'L', 'Riyadh', '2013-03-09', 'Ponpes Tahfiz An-Naba, Jl. Karangjati, Desa Suro Rt06/Rw04, Kec. Kalibagor, Kab.Banyumas', null],
            ['0214410079', 'Zain Abrizam', 'Mustawa 6 Ikhwan', 'L', 'Purbalingga', '2013-10-22', 'Karangkabur rt 01/ rw 02, kel, bojanegara, kec. Padamara, kab. Purbalingga', null],
            ['0214460310', 'Afanin Umaiza Rahmah', 'Mustawa 1 Akhwat', 'P', 'purbalingga', '2018-11-04', 'karangreja RT012 RW006,kecamatan kutasari,kabupaten purbalingga', null],
            ['0214460311', 'Aisyah Ahsanu Amala', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2019-02-25', 'Domisili:Perum puri kalimanah blokA17', null],
            ['0214460312', 'Amroh Kholilah', 'Mustawa 1 Akhwat', 'P', 'Banyumas', '2018-12-18', 'Banjarsari Kidul RT 01 RW 04 Kecamatan Sokaraja Kabupaten Banyumas', null],
            ['0214460313', 'Aysha Khaliqa Naditya', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2019-02-01', 'Penaruban RT 2 RW 2,KALIGONDANG, PURBALINGGA', 'Firma nur aditya'],
            ['0214460314', 'Din Nailah', 'Mustawa 1 Akhwat', 'P', 'Banyumas', '2018-03-20', 'Jalan Jambu No. 5 Desa Kalimanah Wetan RT 04 RW 07 Kecamatan Kalimanah Kabupaten Purbalingga', null],
            ['0214460315', 'Faradillah Mufidah Alfathunissa', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2019-01-11', 'Griya Kalika blok C12A Sokawera Kecamatan Padamara, Kabupaten Purbalingga', null],
            ['0214460316', 'Fatimah Nuha Jamilah', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2018-05-02', 'Selabaya rt02 rw04 kec.kalimanah Purbalingga', null],
            ['0214460317', 'Jazlyn Ameera', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2018-09-06', 'Desa Gembong, RT 03 RW 02, Kecamatan Bojongsari, Kabupaten Purbalingga', null],
            ['0214460318', 'Khaulah Asiyah', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2018-11-12', 'Kedungwuluh rt 07 RW 01, kecamatan Kalimanah, kabupaten Purbalingga', null],
            ['0214460319', 'Najla Hanin', 'Mustawa 1 Akhwat', 'P', 'Banyumas', '2018-04-04', 'RT 1 RW 4 desa kalisube,kec Banyumas,kab,Banyumas', null],
            ['0214460320', 'Najma Arsy Yumna', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2018-11-21', 'Selabaya RT 02 RW 04', null],
            ['0214460321', 'Nara Nur Lestari', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2019-01-31', 'Kedungmenjangan Rt 04  Rw02 Kec. Purbalingga Kab. Purbalingga', null],
            ['0214460322', 'Rifda', 'Mustawa 1 Akhwat', 'P', 'Banyumas', '2019-02-19', 'Kedungwuluh Rt 08 Rw 02', null],
            ['0214460323', 'Rufaidah', 'Mustawa 1 Akhwat', 'P', 'Pemalang', '2018-07-10', 'Babakan rt10/02,Kalimanah, Purbalingga', null],
            ['0214460324', 'Shofiyah Khumairo', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2018-06-01', 'Babakan RT 21 RW 06 kecamatan Kalimanah kabupaten Purbalingga', null],
            ['0214460325', 'Shofiyyah Ibnatu Agus', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2018-05-23', 'Perum Puri Babakan lama, no 76, RT 32 RW 08, Kecamatan Kalimanah, Purbalingga', null],
            ['0214460326', 'Sofiyyah Ibrahim', 'Mustawa 1 Akhwat', 'P', 'Banyumas', '2017-08-02', 'Jl. Baturraden Timur. Gang Sadewa RT4 RW2 No. 7. Sumbang, Banyumas.', null],
            ['0214460328', 'Zainab', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2019-01-18', 'Padamara, RT 03 RW 03, Kec. Padamara, Kab. Purbalingga', null],
            ['0214460327', 'Zainab Adzkiya', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2018-06-14', 'Kandang Gampang RT 03 RW 05, Kecamatan Purbalingga, Kabupaten Purbalingga', null],
            ['0214460329', 'Zainab Rufaidah', 'Mustawa 1 Akhwat', 'P', 'Purbalingga', '2019-05-23', 'Bojanegara rt 5 rw 1 padamara, Purbalingga (sedang tinggal di kedungwuluh rt 6 rw 1)', null],
            ['0214450266', 'Afiza Farzana Mufidah', 'Mustawa 2 Akhwat', 'P', 'Bekasi', '2017-01-09', 'Karang petir RT 01 RW 01, kecamatan Kalimanah, kabupaten Purbalingga', 'Hadi Priyanto'],
            ['0214450267', 'Aisyah Adz Dzakiyyah', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2017-03-14', 'Karang tengah rt 05/rw03, kemangkon, Purbalingga', null],
            ['0214450268', 'Aisyah Nismaranur Ndaru', 'Mustawa 2 Akhwat', 'P', 'Jakarta', '2018-01-21', 'Kedungwuluh RT 05 RW 02, Kalimanah, Purbalingga', null],
            ['0214450270', 'Annisa Khairana Miza', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2021-04-19', 'Klapasawit RT 2 RW 5, Kalimanah, purbalingga', null],
            ['0214450271', 'Arsyifa Khoirunnisaa', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2018-01-05', 'Klapasawit RT 02 RW 01', null],
            ['0214450272', 'Asiyah Kurniawan', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2017-12-29', 'Pagedangan RT 03 RW 06   Kecamatan Purbalingga kidul Kabupaten  Purbalingga', null],
            ['0214450273', 'Athaya Ashiilah', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2014-11-14', 'Kedungwuluh RT 04 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214450274', 'Azkadina Zoraida Wijaya', 'Mustawa 2 Akhwat', 'P', 'Banyumas', '2017-03-17', 'Karangklesem RT 03 RW 02 kec. Kutasari kab. Purbalingga', null],
            ['0214450288', 'Gemilang Silmi Kaffah', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2017-11-17', 'Pengadegan rt08/rw04, Kec.Pengadegan, Kab.Purbalingga', null],
            ['0214450275', 'Hamida Asy-Syaima', 'Mustawa 2 Akhwat', 'P', 'PURBALINGGA', '2017-08-31', 'Rt 08 Rw 04. Des karangsari Kalimanah', null],
            ['0214450276', 'Haniifah', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2017-03-16', 'Kedungwuluh RT 1 RW 2, kecamatan Kalimanah kabupaten Purbalingga', null],
            ['0214450277', 'Khansa Alula Nadir', 'Mustawa 2 Akhwat', 'P', 'Purwakarta', '2017-07-12', 'Pengalusan, RT 05 RW 01, kecamatan Mrebet, Kabupaten Purbalingga', null],
            ['0214450278', 'Maisaroh', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2017-02-05', 'Kedungwuluh RT 02 RW 04 Kecamatan Kalimanah Kabupaten Purbalingga', 'Sohiri'],
            ['0214450279', 'Maryam Ash-Shiddiqah', 'Mustawa 2 Akhwat', 'P', 'Bandung', '2017-06-07', 'Kavling Tunas Ilmu Blok B1 No.1 RT 04 RW 04  Desa Kedungwuluh, Kec Kalimanah, Kab Purbalingga', null],
            ['0214450280', 'Nafisah Qulubiha', 'Mustawa 2 Akhwat', 'P', 'Banyumas', '2017-07-12', 'Banteran RT 4 RW 5 kec sumbang kab banyumas', null],
            ['0214460331', 'Raihana Asiyah', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2017-07-03', 'Jl. Jati, Rt. 1/ Rw. 3, Desa Kalimanah Wetan, Kec. Kalimanah Purbalingga', null],
            ['0214450281', 'Rumaisha Zaina Salsabila', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2018-04-27', 'Kelurahan bojong Rt03Rw01 Kec. Purbalingga Kab. Purbalingga', null],
            ['0214450282', 'Shofiyah Nur Sabiya', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2017-05-29', 'Klapasawit RT 02 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214450283', 'Shofiyyah Muhana Nufaisah', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2017-07-21', 'Jalan Mangga Timur A1 perumahan selabaya indah desa selabaya kecamatan kalimanah', null],
            ['0214450284', 'Sumayyah', 'Mustawa 2 Akhwat', 'P', 'Purbalingga', '2017-01-22', 'Kedungwuluh,RT 08 RW 01,kalimanah kabupaten Purbalingga', null],
            ['0214440222', 'Aisyah Karunia Arbina', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-02-10', 'Kajongan RT 002 RW 002, Kecamatan Bojongsari, Kabupaten Purbalingga', null],
            ['0214440223', 'Anindya Mukhbita Myesha', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-10-27', 'Kedungwuluh rt 04 rw 01, kecamatan kalimanah, kabupaten purbalingga', null],
            ['0214440224', 'Annasya Adreena Saila', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-10-13', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Purbalingga', null],
            ['0214440225', 'Annasya Qirani Azzahra', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-08-23', 'Kedungwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214460289', 'Arwa Naila Dzakiyyah', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-09-30', 'Desa karangcegak RT 12 RW 05, Kec. Kutasari, Kab. Purbalingga', null],
            ['0214440226', 'Attaqia Saufa Adzkia', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2017-04-11', 'Kedungwuluh RT 8 RW 1, kecamatan Kalimanah kabupaten purbalingga', null],
            ['0214440227', 'Ghavrila Joza Valwa', 'Mustawa 3 Akhwat', 'P', 'Kutai Kartanegara', '2017-01-04', 'Karang jambe RT.2 RW.3 Kecamatan Padamara Kabupaten Purbalinggamra', null],
            ['0214440228', 'Hafshoh Nafilah', 'Mustawa 3 Akhwat', 'P', 'Pekanbaru', '2017-05-13', 'Banteran RT 01 RW 05 kecamatan Purwokerto kabupaten Banyumas', null],
            ['0214440229', 'Humaira Althafunnisa', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2017-01-25', 'Galuh RT 008 RW 004, Kecamatan Bojongsari, Kabupaten Purbalingga', null],
            ['0214440230', 'Kahla Halimah', 'Mustawa 3 Akhwat', 'P', 'Banjarnegara', '2017-02-26', 'Kedungwuluh RT.01, RW.01. Kalimanah. Purbalingga', null],
            ['0214440231', 'Kayyisa Hawa Almedina', 'Mustawa 3 Akhwat', 'P', 'Banyumas', '2016-09-08', 'Kedunhwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214440232', 'Khadijah', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-11-05', 'Jl.Jend Sudirman gg.Baraba 23 Rt 03/03 Purbalingga Kidul - Purbalingga', null],
            ['0214440233', 'Maryam', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2015-06-21', 'Jompo RT 01 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214440234', 'Mashel Anindya Azkadina', 'Mustawa 3 Akhwat', 'P', 'Bekasi', '2016-11-22', 'Gandatapa RT 05 RW 06, kecamatan Sumbang, Kabupaten Banyumas', null],
            ['0214440235', 'Nafisah Anasyitah', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-08-08', 'Domisili:Perum puri kalimanah blokA17', null],
            ['0214440236', 'Rafayza Khumaira Al Habibi', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-02-12', 'Kalimanah kulon RT 1 RW 2, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214440237', 'Ruhma', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-08-25', 'Pekiringan Dusun 5 RT 02/10 Pekiringan Karangmoncol Purbalingga', null],
            ['0214440238', 'Syafiqa Hasna Humaira', 'Mustawa 3 Akhwat', 'P', 'Batam', '2016-12-01', 'Desa Purbalingga Wetan RT 4 RW 9 Kec. Purbalingga, Kab. Purbalingga', null],
            ['0214440239', 'Syaima Bintu Agus', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2016-06-24', 'Perum Babakan lama, nomor 76, RT 32 RW 08, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214440240', 'Zahira', 'Mustawa 3 Akhwat', 'P', 'Purbalingga', '2017-02-21', 'Padamara RT. 003 RW. 003, Kec. Padamara, Kab. Purbalingga', null],
            ['0214430169', 'Aisyah Nur Khisma', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-12-20', 'Sokawera Rt 01 RW 05,kecamatan Padamara, kabupaten Purbalingga', null],
            ['0214430170', 'Anisa Naufalin Fikria Faozi', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2016-03-02', 'Desa pegandekan RT 001 RW 002,kecamatan kemangkon,kabupaten purblingga', 'Sefi Mardianti'],
            ['0214430171', 'Annisa Syafa Aqila', 'Mustawa 4 Akhwat', 'P', 'Banyumas', '2016-02-21', 'Perumahan Puri Tama Indah blok Q4. Gemuruh. RT 2 RW 8, Padamara. Purbalingga', null],
            ['0214430172', 'Aqila Zahwa Jauza', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-02-10', 'Kandang gampang rt 03 rw 05 ,kecamatan /kabupaten Purbalingga', null],
            ['0214430173', 'Asma Khairunisa', 'Mustawa 4 Akhwat', 'P', 'Banyumas', '2015-10-04', 'Perum Griya Bantar Indah Blok J no.1 Rt 02 Rw 05 Bantarwuni Kembaran Banyumas', null],
            ['0214430174', 'El Syauqia Hibatillah', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-12-19', 'Mangunegara RT 6 RW 1 , Kecamatan Mrebet, Kabupaten Purbalingga', 'Tidak ada'],
            ['0214430175', 'Fathimah Al-Humairaa', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2016-04-06', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214430176', 'Fatima Khairani Karfi', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-12-31', 'Jl cempaka raya no 4 rt 006 rw 007 Perum Penambongan  Purbalingga', null],
            ['0214430177', 'Fatimah Az Zahra', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-03-29', 'Grecol rt 04 rw 01,kecamatan kalimanah,kabupaten purbalingga', null],
            ['0214430178', 'Fatimah Az Zahra Bayuputri', 'Mustawa 4 Akhwat', 'P', 'Bekasi', '2015-05-14', 'Bancar RT 003 RW 003, Kecamatan Purbalingga, Kabupaten Purbalingga', null],
            ['0214430179', 'Fiandra Ghani Azkadina', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2016-04-10', 'Griya Kalika blok C12A Sokawera, Kecamatan Padamara', null],
            ['0214430180', 'Habibah Lathifunnisa', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2016-01-24', 'Brobot RT 5 RW 2,Kecamatan Bojongsari, Kabupaten Purbalingga', null],
            ['0214430181', 'Hafsah Az Zahran', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-12-04', 'Kalikabong RT 02 RW 04 kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214430182', 'Hanin Aulia Nur Faizah', 'Mustawa 4 Akhwat', 'P', 'Banyumas', '2015-04-17', 'karang tengah, rt 6 rw 2 kecamatan kembaran kabupaten banyumas', null],
            ['0214430183', 'Khansa\' Nur \'Afifah', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-07-21', 'Karangtengah Rt 07 Rw 04, kecematan Kemangkon, Kabupaten Purbalingga', 'Bpk Sutriyo'],
            ['0214430184', 'Lulu Anindya Rahma', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-09-13', 'Bojanegara rt 03rw02, kecamatan padamara, kabupaten purbalingga', null],
            ['0214430185', 'Nabila Hafshah Mujahidah', 'Mustawa 4 Akhwat', 'P', 'Batam', '2015-04-29', 'Kaligondang RT 03 RW 04 kecamatan Kaligondang kabupaten Purbalingga', null],
            ['0214430186', 'Nabila Hasna', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-08-29', 'Kembangan Rt 04 Rw 05, Bukateja, Purbalingga', null],
            ['0214430187', 'Niki Gendhis Satria', 'Mustawa 4 Akhwat', 'P', 'PURBALINGGA', '2015-09-05', 'Purbalingga lor RT 02 RW 03 , kecamatan Purbalingga, Kabupaten Purbalingga', 'tidak ada'],
            ['0214430188', 'Shakila Nuraini', 'Mustawa 4 Akhwat', 'P', 'Purbalingga', '2015-11-09', 'Babakan RT 15 RW 04, Kalimanah, Purbalingga', 'tidak ada'],
            ['0214430189', 'Zaila Sakinah Syaima', 'Mustawa 4 Akhwat', 'P', 'Purbaljngga', '2015-07-05', 'Desa kr sari Rt 08 Rw 04 kalimanah Purbalingga', null],
            ['0214420126', 'Adzkiya Hasna Syafiyah', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2014-12-16', 'Kedungwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214420127', 'Aisyah', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2015-07-09', 'Desa Babakan RT 14 RW 04 kec. Kalimanah kab. Purbalingga', null],
            ['0214420128', 'Amelia Karla Nur Azizah', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2014-05-18', 'Purbalingga kidul RT 02,RW 01,Purbalingga kidul,Purbalingga', null],
            ['0214420130', 'Dhiya Aqilla', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2015-01-19', 'Kedungwuluh RT 06 RW 01 kecamatan Kalimanah ,Kabupaten Purbalingga', null],
            ['0214420131', 'Dzakira Aftani Arsy', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2014-08-08', 'Dusun 2 Meri RT 15 RW 06, Kecamatan Kutasari, Kabupaten Purbalingga', null],
            ['0214420132', 'Jihan Dhiya Ayyaasya', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2015-07-30', 'Gemuruh RT 01 RW 07, Kecamatan Padamara  Kabupaten Purbalingga', null],
            ['0214420133', 'Khaulah', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2015-06-04', 'Galuh RT 04 RW 03', null],
            ['0214420134', 'Maryam', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2014-10-14', 'Kedungwuluh RT 08 RW 02, kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214420135', 'Nadhira Rizky Setiandari', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2014-09-24', 'Purbalingga Wetan RT 01 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', null],
            ['0214420136', 'Naila Mafaza', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2014-04-17', 'Kedungwuluh RT 2 RW 4,Kecamatan Kalimanah,Kabupaten Purbalingga', null],
            ['0214420138', 'Rumaisya Nuraini', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2014-10-31', 'Pekaja RT 05 RW 03 Kecamatan Kalibagor Kabupaten Banyumas', null],
            ['0214420139', 'Shaquila Najwa Aira', 'Mustawa 5 Akhwat', 'P', 'Banyumas', '2014-10-26', 'Kedungwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214420141', 'Talita Zalfatunida Naqiyya', 'Mustawa 5 Akhwat', 'P', 'Purbalingga', '2014-04-20', 'Bojongsari rt02 rw13 bojongsari purbalingga', null],
            ['0214410092', 'Ainiya Faida Azmi', 'Mustawa 6 Akhwat', 'P', 'Bekasi', '2025-09-04', 'Karangpule RT 02 RW 03, Kecamatan Padamara, Kabupaten Purbalingga', 'Tidak ada'],
            ['0214410093', 'Aisyah Talita Zahran', 'Mustawa 6 Akhwat', 'P', 'Putbalingga', '2014-03-12', 'Kalikabong RT 02 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214460285', 'Anisa Sherena Aquila Nurseto', 'Mustawa 6 Akhwat', 'P', 'Purwokerto', '2013-12-28', 'Kompleks Pondok Tahfidz AnNaba Banyumas', null],
            ['0214410095', 'Aqila Keisya Azzahra', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2013-11-28', 'Karang Banjar RT 16 RW 06, kecamatan Bojongsari, kabupaten Purbalingga', 'Tuwarno'],
            ['0214410096', 'Askanah Nida Hayfa', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2013-06-09', 'Purbalingga Kidul RT 03 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', null],
            ['0214410097', 'Balqis Najma Habibah', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2014-02-24', 'Sokawera RT 2 RW 4 kecamatan Padamara kabupaten Purbalingga', 'Tidak ada'],
            ['0214410098', 'Fadhilatul Aulia', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2012-06-15', 'Klapasawit RT 1 RW 2 , kalimanah, purbalingga', null],
            ['0214430194', 'Fahira Kayla Fathiani', 'Mustawa 6 Akhwat', 'P', 'Cirebon', '2013-04-04', 'Griya Kalika blok C12A Sokawera Kecamatan Padamara, Kabupaten Padamara', null],
            ['0214410099', 'Fathimah', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2013-11-28', 'Bojongsari rt 02 rw 05 purbalingga', null],
            ['0214410100', 'Fatimah Azzahra', 'Mustawa 6 Akhwat', 'P', 'Banyumas', '2013-11-09', 'Sambeng Kulon RT 04 RW 01 kecamatan Kembaran kabupaten Banyumas', null],
            ['0214410062', 'Gendis Amira Pramesti Humayun', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2013-08-05', 'Kedungwuluh RT 08 RW 02 Kecamatan Kalimanah, Kabupaten Purbalingga', null],
            ['0214410101', 'Hanifah Uswatun Hasanah', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2013-11-28', 'Grecol RT 04 RW 01,kecamatan kalimanah,kabupaten purbalingga', null],
            ['0214410102', 'Huriyah Qurratu\'ain Rosyidah Prasetyo', 'Mustawa 6 Akhwat', 'P', 'Utecht, Netherland', '2014-03-17', 'Jl letkol isdiman no 01 rt/rw 01/04, kecamatan purbalingga kidul, kabupaten purbalingga', null],
            ['0214410103', 'Khanza Ghinna Nur Azizah', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2013-02-18', 'Purbalingga kidul RT 02 RW 01,Kecamatan Purbalingga,Kabupaten Purbalingga', null],
            ['0214410104', 'Khanza Saafia Nur Arafah Wibowo', 'Mustawa 6 Akhwat', 'P', 'Banyumas', '2013-12-30', 'Desa Bojanegara, RT 01 RW 03, Kecamatan Padamara, Kabupaten Purbalingga Jawa Tengah', null],
            ['0214410105', 'Liyana Nafisa Amri', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2013-03-14', 'Kedungjati RT 02 RW 01, Kecamatan Bukateja, Kabupaten Purbalingga', null],
            ['0214410106', 'Niswah Muthmainnah', 'Mustawa 6 Akhwat', 'P', 'Batam', '2013-05-22', 'Desa Kaligondang RT 03 RW 04 kec Kaligondang kab Purbalingga', null],
            ['0214410059', 'Ravikah Rahmah Arum Fathurrohman', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2013-10-18', 'Desa Purbalingga Kidul, RT.002/RW.001, Kecamatan Purbalingga, Kabupaten Purbalingga', 'Suharto'],
            ['0214400048', 'Salsabila Nadhifah', 'Mustawa 6 Akhwat', 'P', 'Purbalingga', '2013-08-18', 'Jompo Rt 01 RW 04 kec kalimanah kab purbalingga', null],
        ];

        $map = [];
        foreach ($students as [$nis, $nama, $kelas, $gender, $tempatLahir, $tanggalLahir, $alamat, $namaWali]) {
            $student = Student::firstOrCreate(
                ['nis' => $nis],
                [
                    'name'        => $nama,
                    'class_id'    => $classMap[$kelas] ?? null,
                    'gender'      => $gender,
                    'birth_place' => $tempatLahir,
                    'birth_date'  => $tanggalLahir,
                    'address'     => $alamat,
                    'guardian_name' => $namaWali,
                    'is_active'   => true,
                ]
            );
            $map[$nis] = $student->id;
        }

        return $map;
    }

    private function createFamilies(Role $waliSantriRole, array $studentMap): array
    {
        // Each family: [nama_ayah, nik_ayah, hp_ayah, pekerjaan_ayah, email_ayah,
        //               nama_ibu, nik_ibu, hp_ibu, pekerjaan_ibu, email_ibu,
        //               alamat, [children NIS list - first is oldest]]
        $families = [
            ['Popo Apri Rayo', '3303060504900002', '085647138483', 'Wiraswasta', 'popoaprirayo@gmail.com', 'Latifah Riani', '3303106710930001', '085228436937', 'Pengajar', 'latiefah.riani@gmail.com', 'Babakan rt 14/04 kec. Kalimanah kab. Purbalingga', ['0214420127', '0214460290']],
            ['Abdul Rohman', '1809020212900004', '082379455505', 'Karyawan Swasta', 'abdulrohmanalkhairy@gmail.com', 'Ihda Nur Azizah', '3303065603960004', '082236413676', 'Guru Tahfidz', null, 'Klapasaeit Rt 02 Rw 01 Kalimanah Purbalingga', ['0214460291']],
            ['Sahad Halomoan', '1308191609930001', '085959463344', 'Wiraswasta', 'halomoansahad@gmail.com', 'Farradhiva Ankla', '3302196101920002', '082138827550', 'Ibu Rumah Tangga', 'farradhiva.ankla@gmail.com', 'Desa Lemberang Rt 1 Rw 1, Kecamatan Sokaraja, Kabupaten Banyumas', ['0214460292']],
            ['Priyadi', '3303012907920002', '088902811189', 'Pedagang', 'abufaatihpriyadi@gmail.com', 'Iis Nur\'aini', '3301015004950003', '08816605668', 'Ibu rumah tangga', null, 'Kedungbenda RT 02 RW 12 kecamatan Kemangkon,Kabupaten Purbalingga', ['0214460293']],
            ['Aminudin', '3302192101900001', '08982111321', 'Pedagang', 'adinachmad9@gmail.com', 'Siti saonah fauziah', '3209216207980004', '08990594647', 'Tidak tahu', 'adinachmad9@gmail.com', 'Desa banjaranyar RT/RW 03/08 kec sokaraja kab Banyumas', ['0214460294']],
            ['Ulil Amri', '3303022507870002', '081215173817', 'Wiraswasta', 'klikgrafikabukateja@gmail.com', 'Bena Mulfiarni', '3303064610900002', '082225391384', 'Ibu Rumah Tangga', 'mmulfiarni@gmail.com', 'Kedungjati RT 02 RW 01, Kecamatan Bukateja, Kabupaten Purbalingga', ['0214410105', '0214460295']],
            ['Eko Martanto', '3303062910830001', '085227706045', 'Dagang', null, 'Sutiyah', '3303064705830005', '0895634691444', 'Karyawan', 'sutiyahpurbalingga@gmail.com', 'Klapasawit RT 03 RW 06 Kec Kalimanah Kab Purbalingga Jawa Tengah', ['0214460296']],
            ['Muhamad Riana Yudianto', '3204051902930008', '087822994032', 'Guru', 'mrianayudianto@gmail.com', 'Gletika', '3204325209890007', '087722875749', 'Ibu Rumah Tangga', 'g.ummumaryam@gmail.com', 'Komplek Kavling Tunas Ilmu Blok B1 no.01, Dukuhcakra RT.04/04 Desa Kedungwuluh, Kec. Kalimanah, Kab. Purbalingga, Jawa Tengah', ['0214460297']],
            ['Heri Abdul Rozak', '3303072410740001', '081218512532', 'Dagang', 'azizahnur182011@gmail.com', 'TRI WAHYUNINGSIH', '3303074312790001', '081218512532', 'Ibu rumah tangga', 'azizahnur182011@gmail.com', 'Desa karang lewas RT 10 RW 05 kecamatan Kutasari,kabupaten Purbalingga', ['0214460298']],
            ['Ferdinan Candra Riawan', '3303051304910001', '085866125974', 'Pedagang', 'dinancandra@gmail.com', 'Kirana Puspa', '3301024603920010', '085713685802', 'Ibu rumah tangga, membantu suami berjualan', 'candranana502@gmail.com', 'Jalan Mandalika RT 5/5 Selabaya, Kalimanah', ['0214460299']],
            ['Rijal Awali', '3303053112800005', '087728400111', 'Terapist', 'rijal311280@gmail.com', 'Ani Widanarti', '3303054308860002', '085879815517', 'Ibu rumah tangga', 'aniwidanarti3@gmail.com', 'Wirasana RT 001/RW002 Kecamatan Purbalingga, Kabupaten Purbalingga', ['0214460300']],
            ['Rohmatulloh', '3302262801900002', '089636769737', 'Karyawan swasta', 'rohmatullohaja@gmail.com', 'Eka Meiliana', '3302215405920005', '089502191143', 'Ibu Rumah Tangga', null, 'Tambaksogra RT 09 RW 02, Kecamatan Sumbang, Kabupaten Banyumas', ['0214460301']],
            ['Aan Rasiman', '3303180302850001', '083844329285', 'Penjahit', 'mamasalyayuais@gmail.com', 'Nenih Agustina', '3201146308910002', '083876124001', 'Ibu rumah tangga', null, 'Adoarsa,rt 8,rw 4,kecamatan kertanegara,kabupaten purbalingga', ['0214460302']],
            ['Defy Kurniawan', '3303071612790002', '088983517355', 'Wiraswasta', 'musyaffa.abdullah79@gmail.com', 'Fitriyani', '3303144806850005', '088983212256', 'Ibu Rumah Tangga', 'fitriummufadhil85@gmail.com', 'Banjaran RT 8 RW 4 Kecamatan Bojongaari Kabupaten Purbalingga', ['0214460303']],
            ['Arif Wicaksono', '3302030905920002', '0895357514456', 'Guru', 'arifsufyan288@gmail.com', 'Idha Alvianti', '3303054205910001', '085726459348', 'Guru', 'idhaalvi@gmail.com', 'Bojong, RT 01 RW 03 Kecamatan Purbalingga, Kabupaten Purbalingga', ['0214460304']],
            ['Muallidin', '5203131408910004', '087763264149', 'Guru', 'muallidinibnusyur@gmail.com', 'Ani Tri Umayani', '3003165903950002', '08741009602', 'IRT', 'aniummuusamah@gmail.com', 'Dusun Melung RT 04 RW 05 Desa Larangan Kecamatan Pengadegan Kabupaten Purbalingga', ['0214460305']],
            ['Fandiono', '3303062807830002', '085642880377', 'Perangkat desa', 'utsmanalfandiono83@gmail.com', 'Ria susanti', '2303024910860002', '081515822677', 'Ibu rumah tangga', 'iassusanti88@gmail.com', 'RT 1 RW 7 kecamatan Kalimanah, kabupaten Purbalingga', ['0214460306']],
            ['Rusdiyono', '3303113004750001', '082137579854', 'POLRI', 'yaminuwaies@gmail.com', 'Suci pertamasari', '3303115605830002', '082324300133', 'Ibu Rumah Tangga', 'rusdionosucipermatasari@gmail.com', 'Desa Karanggedang RT.04 RW.01 kecamatan karanganyar kabupaten purbalt', ['0214460307']],
            ['Aziz Zainur Rochman', '3302213009900001', '083839010798', 'Guru', 'azizzainur010@gmail.com', 'Siti Fathonah', '3302214502930001', '083836697264', 'IRT', 'sitifathonah8171@gmail.com', 'Desa Karangturi RT 06 RW 01. kecamatan Sumbang Kabupaten Banyumas', ['0214460308']],
            ['Sutaryo', '3304032609890001', '081285963000', 'Wiraswasta', 'srs.exhaust1@gmail.com', 'Iin nur Halimah', '3301074405910001', '089501374737', 'Ibu rumah tangga', 'iin. nur3093@gmail.com', 'Jompo RT 03 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214460309']],
            ['Alek Dianto', '3303081208870001', '081228252231', 'Pedagang', 'baemuis348@gmail.com', 'PURWATININGSIH', '3303084701920001', '081228252231', 'Ibu rumah tangga', 'baemuis348@gmail.com', 'Desa selaganggeng RT 02 RW 02, kecamatan mrebet, kabupaten Purbalingga', ['0214450246']],
            ['Ady Achadi', '3302272606710005', '0816695897', 'Dosen', 'adyachadi@unwiku.ac.id', 'Indar Martanti Ariningsih', '3302276903750002', '085291499698', 'Ibu Rumah Tangga', 'indarmartanti09@gmail.com', 'Jl. Riyanto GG. Mawar III no 265a, Kel. Sumampir, Kec. Purwokerto Utara, Kab. Banyumas', ['0214450247']],
            ['Slamet Faozi', '33030109060002', '085540515556', 'Pedagang', 'slametfaozi28@gmail.com', 'Rini nofitasari', '3303014111930001', '085700900215', 'Pedagang', 'rini_nofitasari93@yahoo.com', 'Pegandekan RT 001 RW 002, Kecamatan Kemangkon, Kabupaten Purbqlingga', ['0214430170', '0214450248']],
            ['indra Meiga Buana', '3303052305860003', '085875814053', 'Pedagang', 'indra.mb@gmail.com', 'yunia Triwulandari', '3303055406860001', '085799981421', 'usaha Laundry', 'yuniatriwulandari14@gmail.com', 'Griya Kalika B9 Kedungwuluh, Kalimanah Purbalingga', ['0214440190', '0214450249']],
            ['abdullah zaen', '3303060107800003', '081319839320', 'ustadz', 'a_abdirrahman@yahoo.co.id', 'aa tatariana ach dijana putra', '3303065712790003', '081318379721', 'ibu rumah tangga', null, 'kedungwuluh rt 8 rw 2 kalimanah purbalingga', ['0214450250']],
            ['BAMBANG ARDIYANTO', '3303020112600003', '087893195077', 'Peternak Ayam', 'danialabdulhoni@gmail.com', 'SAMIAH', '3303024105710002', '087893195077', 'Pedagang', 'danialabdulhoni@gmail.com', 'Desa Bukateja RT 04 RW 02 ,kec.Bukateja,kab.Purbalingga', ['0214450251']],
            ['Show Andita Katon', '3302190207870002', '082136340364', 'Karyawan', 'katon.baee@gmail.com', 'Umi Kulsum', '3327095107930005', '087775212797', 'Guru', 'katon.baee@gmail.com', 'Banjarsari Kidul RT 01 RW 04 Kecamatan Sokaraja Kabupaten Banyumas', ['0214450252', '0214460312']],
            ['Heri dwiyanto', '3303062306840001', null, 'Pedagang', null, 'Ririn ariastuti', '3303065206990002', '0882005766457', 'Pedagang', 'ririnzai@gmail.com', 'Kedungwuluh, karangwinong. Kec:kalimanah kab;purbalinggart02/04', ['0214450253']],
            ['Sugeng Prijono', '3303060104850001', '085725846275', 'Pedagang dan wirausaha', 'asyaplatik82@gmail.com', 'Nurlaela', '3303065904850001', '085728663088', 'Ibu rumah tangga', 'elafathan85@gmail.com', 'Klapasawit RT 02 RW 07,Kecamatan Kalimanah Kabupaten Purbalingga', ['0214450254']],
            ['Rakhmat Utama', '3328060101790012', '08812400791', 'Karyawan swasta', 'rakhmat.utama@gmail.com', 'Tri Kukuh Agustini', '3328064506800009', '0888066166', 'IRT', 'tkukuhagustini@gmail.', 'Kober RT 3 RW 9 Kec.Purwokerto barat Banyumas', ['0214450255']],
            ['Tiyas Wibowo', '3303052912880002', '08996688755', 'Karyawan swasta', 'tiyaswibowo96@gmail.com', 'Eni Rahayu', '3303054801930006', 'TidakPunya', 'Ibu Rumah Tangga', null, 'Purbalingga Lor Rt3 Rw3, kecamatan Purbalingga, kabupaten Purbalingga', ['0214430158', '0214450256']],
            ['Tegar hidayat', '33030051103900004', '085640750243', 'Pedagang', 'tegarhidayat1131990@gmail.com', 'Windiati', '3303064408880002', '085803701154', 'Ibu rumah tangga', 'tegarhidayato242@gmail.com', 'Klapasawit RT 03/05 kecamatan kalimanah kabupaten Purbalingga', ['0214450257']],
            ['Rudy Nugroho', '3303050701830003', '085228072456', 'Karyawan swasta', 'rudgulitpbg01@gmail.com', 'Heni Noviatin', '3303056311830001', '085228071887', 'Ibu Rumah Tangga', 'ibrahimray231183@gmail.com', 'Jln.Puring Rt 01 Rw 04 Nmr 9A', ['0214450258']],
            ['Sarip', '3304031504820007', '081227366884', 'Karyawan', 'sarifabufatih@gmail.com', 'Siti Mukhoyatun', '3304036201880002', '08220227309', 'Ibu Rumah Tangga', 'siti.mukhoyatun@gmail.com', 'Desa Blimbing RT.05/02 kec.mandirja', ['0214430154', '0214450259']],
            ['Afit Setiadi', '3303052406880002', '085713676679', 'Karyawan', 'afitsetiadi@gmail.com', 'Septi Wulandari', '3303055409890002', '085713907978', 'Ibu Rumah Tangga', 'nadhira.rs@gmail.com', 'Purbalingga Wetan RT 01 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', ['0214420135', '0214450260']],
            ['Bagus Pamungkas Pitaloka', '3304101406820012', '082147484913', 'Pengajar', 'dbaguse4748@gmail.com', 'Vinna Damayanti', '3304105806910001', '0895369459879', 'Pengajar', 'vinnadamayanti001@gmail.com', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214420134', '0214450261']],
            ['Nur Mukti Wibowo', '3303050805880001', '085808543932', 'Broker tanah dan rumah', 'mukti.wibowo88@gmail.com', 'Ria Yunita', '3303016206880001', '085876797177', 'Catering online', 'ryayunita.azzahra@gmail.com', 'Jetis rt 14 rw 05, Kecamatan Kemangkon, Kabupaten Purbalingga', ['0214450262']],
            ['Supyan', '3302220507850001', null, 'Pedagang', null, 'Memah Setiamah', '3302195507880004', '085292908280', 'Ibu rumah tangga', 'sofyanmemah@gmail.com', 'Banjar Anyar RT 01 RW 05, Kecamatan Sokaraja, Kabupaten Banyumas', ['0214450263']],
            ['Febrian Prabawa Hakim', '3303052602880001', '081326264191', 'PNS', 'ukasyahibnuna@gmail.com', 'Nurul Layli Mafthuhah', '3303074511890001', '082123415020', 'Perawat', 'laylimafthuhah@gmail.com', 'Karangcegak RT 03 RW 01 Kecamatan Kutasari Kabupaten Purbalingga', ['0214410089', '0214450264']],
            ['Eka Fatnurokhman', '3302271811810004', '085287927368', 'Dagang', 'featureka65@gmail.com', 'Nunik Setiyowati', '3302277011810004', '085876040235', 'Swasta', 'nunik.setiyowati.2018@gmail.com', 'Karangwangkal RT 03 RW 01 Purwokerto Utara', ['0214450265']],
            ['DWI NURHIDAYAH', '3303062305770001', '081902966544', 'Karyawan swasta', 'dwinur23577@gmail.com', 'Zaenatun nur\'aini', '3303066401840004', '085640240936', 'Karyawan swasta', 'zaenatun2401@gmail.com', 'Jl. Mawar 487 Rt. 4 Rw. 4 kalimantan wetan  kec. kalimantan kab.Purbalingga', ['0214440201']],
            ['Edi setiawan', '3303062002830004', '081327422222', 'Wiraswasta', 'haddid.di@gmail.com', 'Nafritin dwi ratnasari', '3303095111880003', '085876345192', '-', 'vievyt@gmail.com', 'Dukuhcakra rt4/rw4 kec. Kalimanah kab. Purbalingga', ['0214410083', '0214440202']],
            ['Gendon Puraba Fardhona', '3303152812900001', '082329298583', 'Wiraswasta', 'armadhona@gmail.com', 'Aisyah Zahidatuz Zahra\'', '3515064106950001', '082323210363', 'Ibu Rumah Tangga', 'aisyahzzahra@gmail.com', 'Padamara, RT 03 RW 03, Kecamatan Padamara, Kabupaten Purbalingga', ['0214440203', '0214460328']],
            ['Teguh Wardiyanto', '3303062709810001', '089510923327', 'Wiraswasta', null, 'Kusyuliati', '3303066503830002', '089637014760', 'IRT', 'kusyuliati@gmail.com', 'Kalimanah wetan RT 04 RW 07, kecamatan Kalimanah, kabupaten Purbalingga', ['0214440204']],
            ['Iwan Setiawan', '-', '08122851515', 'Dosen/Peneliti', 'esteween@gmail.com', 'Ayatul Fauziyah', '-', '085713698969', 'Ibu Rumah Tangga', 'ayatulfauziyah1@gmail.com', 'Babakan Asri Blok A12, Babakan, Kec. Kalimanah, Kab. Purbalingga', ['0214440205']],
            ['Mahmud', '3302141006900008', '085811139560', 'Karyawan warung makan', 'mahmudridlekosongdua@gmail.com', 'Restiani', '3303087009920001', '089526852508', 'Ibu rumah tangga', 'mahmudakhi019@gmail.com', 'Lambur RT 01 RW 01,kecamatan mrebet, Kabupaten purbalingga', ['0214420116', '0214440206']],
            ['Dwi Yugo Apriyanto', '3216221804820002', '0822214236746', 'Wirausaha', 'dwiyugo82@gmail.com', 'Susmiati', '3216225710810002', '088221771254', 'Ibu rumah tangga', null, 'Kalikajar RT 03 RW 08 kecamatan Kaligondang kabupaten Purbalingga', ['0214440207']],
            ['Sudino', '3303150308910001', '081280479703', 'Buruh harian lepas/serabutan', 'dinobersaudara03@gmail.com', 'Siti kurniati', '3303075605910004', '081280479703', 'Ibu rumah tangga', 'dinobersaudara03@gmail.com', 'Bojanegara rt 5 rw 1 padamara, Purbalingga ( sedang tinggal di kedungwuluh rt 6 rw 1)', ['0214440208', '0214460329']],
            ['Widi Utomo', '3275062212890010', '085292158052', 'Karyawan', 'akhwidiutomo@gmail.com', 'Anisa Novitasari', '3306044411890001', '085292158052', 'Guru tk', null, 'Karangpule RT 03 RW 02, Kecamatan Padamara, Kabupaten Purbalingga', ['0214410092', '0214440209']],
            ['kurniawan Pamuji', '3302202712880001', '082323179892', 'Tani', 'kurniawanpamuji27@gmail.com', 'Windi Anita Puspasari', '-', null, '-', null, 'Kramat Rt 05 Rw 01, Kecamatan Kembaran Kabupaten Banyumas', ['0214440210']],
            ['Dani Nur Sya\'ban', '3303051906810002', '085647945661', 'Dagang', 'alwireyhan19@gmail.com', 'Suntarni', '3324016802890002', '085647945661', 'Dagang', 'alwireyhan19@gmail.com', 'Purbalingga Kidul RT 01 RW 04, Kecamatan Purbalingga, Kabupaten Purbalingga', ['0214420114', '0214440211']],
            ['Anom Erlangga', '3303120703780003', '081353089678', 'Pekerja swasta', null, 'Sri Supriyatiningsih', '3303124205790003', '082242635133', 'Ibu Runah Tangga', null, 'Karang anayr rt02/01 Kec Karamganyar kab purbalingga', ['0214440212']],
            ['Saepuloh', '3329061002860001', '085726000375', 'Pegawai Swasta', 'ipung859@gmail.com', 'Rini Muliasari', '3303065401890004', '082134294097', 'Ibu Rumah Tangga + Tutor PKBM', 'rinimuliasari0505@gmail.com', 'Selabaya RT 02 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214440213', '0214460320']],
            ['Imam fajar subekhi', '3303140703870003', '085226970555', 'Wiraswasta', 'galerimartpurbalingga76@gmail.com', 'Anisa nurfitri', '3329036011920007', '082323723069', 'Wiraswasta', 'galerimartpurbalingga76@gmail.com', 'Bojongsari rt 02 rw 05 purbalingga', ['0214410099', '0214440214']],
            ['Mingan Sutomo', '3303061201810003', '081289997557', 'Karyawan pondok', null, 'Sri Partini', '3303065409810002', '081289997557', 'Ibu rumah tangga', null, 'Kedungwuluh RT 07 RW 01,Kecamatan Kalimanah,Kabupaten Purbalingga', ['0214440215']],
            ['Arif Tri Pamungkas', '3303012604830003', '08122954123', 'Supir', 'pamungkasariftri@gmail.com', 'Gita Pratiwi', '3303054705860001', '08158085400', 'Ibu Rumah tangga', 'tiwi.tywoel@gmail.com', 'Jl kenanga no 1 RT 3 RW 7 perumahan penambongan Purbalingga', ['0214440216']],
            ['Roni Eko Prastyono', '3303152810800004', '082313255402', 'Asn Guru', 'roniprasetyono80@guru.smk.belajar.id', 'Ambar Sriutami', '3303155611810003', '082134278687', 'ASN Guru', 'ambarutami75@gmail.com', 'Babakan 6/2 Kalimanah Purbalingga', ['0214440217']],
            ['Teguh pambudi', '3303141207790002', '081215410246', 'Padagang', 'pambudit648@gmail.com', 'Anik lusiani', '3303146708840002', '089542796366', 'Ibu rumah tangga', 'aniklusianianiklusiani@gmail.com', 'Carangmanggang RT 16 RW 06 karangbanjar bojongsari purbalingga', ['0214420113', '0214440218']],
            ['Koswara mohamad faiz', '3303141303720001', '081809415955', 'Pedagang', 'mf078061@gmail.com', 'Siswati', '3303146010800001', '081802815588', 'Ibu Rumah Tangga', null, 'Desa Karangbanjar Rt19 rw08 kecamatan bojongsari kabupaten Purbalingga', ['0214440219']],
            ['Rifqi Agung Mulyadi', '3302080506920002', '08972968222', 'Karyawan Swasta', 'isyarif31@gmail.com', 'Asih Fatonah', '3303104306930003', '089508982373', 'Ibu Rumah Tangga dan Privat Online', 'asihfatonah36@gmail.com', 'Kedungwuluh Rt 08 Rw 02', ['0214440220', '0214460322']],
            ['Sutrisno', '3303060709850001', '089637360588', 'Pedagang', 'inomase3@gmail.com', 'Sunartini', '3276024409910009', '089637360588', 'Ibu Rumah Tangga', 'inomase3@gmail.com', 'Grecol RT 05 RW 01 Kecamatan Kalimanah ,Kabupaten Purbalingga', ['0214430147']],
            ['Rokhanudin', '3303071908790001', '085848701806', 'Buruh', 'rohan nudin@ gmail.com', 'Rokhanah', '3303074701810001', '085725962454', 'Ibu Rumah Tangga', null, 'Ds.Walik Kutasari RT 16 RW 08 Kecamatan Kutasari, Kabupaten Purbalingga', ['0214430149']],
            ['Jupri Santoso', '3303060302840002', '085742540922', 'PNS', 'bankumpbg@gmail.com', 'An Nisaa Pratiwi', '3303914503870001', '085292505941', 'Guru TK', 'tiwisolehah@gmail.com', 'Kedungwuluh RT 06 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214430150']],
            ['Teguh Rahyono', '3275022301810014', '082327212554', 'Wirasuasta', 'teguhrahyono23@gmail.com', 'Ade Ernawati', '3201385907850002', '081296046421', 'Ibu rumah tangga', 'adeernawati530@gmail.com', 'Kedungwuluh RT 07 RW 02 Kecamatan  Kalimanah Kabupaten  Purbalingga', ['0214430151']],
            ['Sugiyanto', '3303060208850002', '083821253135', 'Servis AC', 'sugiyantoleader@gmail.com', 'Puji Listiani', '3303066807850001', '089508676796', 'Ibu rumah tangga ( GQ Tunas Ilmu )', 'liestiani28@gmail.com', 'Karang petir RT 01 RW 01, kecamatan Kalimanah, kabupaten Purbalingga', ['0214430152', '0214450266']],
            ['Atma Nurseto', '3302201111880001', '085727051663', 'Guru', 'nursetoatma@gmail.com', 'Dian Anisa Listya', '3302247006880002', '081568270248', 'Guru', 'diananisalistya@gmail.com', 'Ds. Suro RT 06/04, Karangjati, Kecamatan Kalibagor, Kabupaten Banyumas, Jawa Tengah, Indonesia 53193', ['0214460285', '0214460286']],
            ['Gusbada adi kusumpraja Iskandar', '3402012310830001', '085743129000', 'Peternak', 'gusbafa.adi@gmail.com', 'Ariyanti rosaliani', '3402015605840001', '085743371805', 'Ibu rumah tangga', 'ariyanti.rosaliani@gmail.com', 'Grumbul kikiran, desa Silado, Sumbang, banyumas', ['0214430155']],
            ['Ajis', '3303062008820003', '085228063495', 'Pedagang sempol ayam', 'abuhaikal754@gmail.com', 'Agustinidewisaputri', '3302194308890001', '087823631010', 'Ibu rumah tangga', 'ummahusain306@gmail.com', 'Kalikabong rt2/rw1.kec.kalimanah.kab.purbalingga.sekarang domisili klahang rt1/rw6.kec.sokaraja.kab.banyumas', ['0214430156']],
            ['Aris Dianto', '3303051304830004', '081327970602', 'Wiraswasta', 'haritsabyan122@gmail.com', 'Titin Agustriani', '3303044408880001', '085227227483', 'Ibu Rumah Tangga', 'titin.agz@gmail.com', 'Jl Komisaris Noto Soemarsono 124 b  RT 03/02 Purbalingga 53313', ['0214420122', '0214430157']],
            ['AGUNG PRIYAMBODO', '3303141307830001', '085747777075', 'Wiraswasta', 'indigo.agung@gmail.com', 'EVA SUSANTI', '3303065902910001', '085641009500', 'Ibu rumah tangga', 'mbaevasusanti@gmail.com', 'Klapasawit RT 04 RW 05, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214430160']],
            ['Arif Hidayatullah', '3302112404810005', '082135366338', 'Ustadz', 'abumamah@gmail.com', 'Dhurrotun Nasikah', '3302116412840002', '082135366338', 'Ibu Rumah Tangga', 'abumamah@gmail.com', 'Banyumas Rt 01/04, kecamatan Banyumas, Kabupaten Banyumas', ['0214410080', '0214430162']],
            ['Dwio Ratono', '3303062805710002', '081542728260', 'Swasta', null, 'Dwi Ambarsari', '3303064111750002', '082243510290', 'Pedagang', 'dwiambar116@gmail.com', 'Kalikabong RT 01/ RW 01, Kecamatan Kalimanah, Kabupaten PurbalinggaP', ['0214430163']],
            ['Darsun', '3302212711830002', '085929883236', 'Wiraswasta', 'unaisummu16@gmail.com', 'Suci Uswatun Hasanah', '3302216212820002', '085929883236', 'Ibu Rumah Tangga', 'unaisummu16@gmail.com', 'Kotayasa RT 08 RW 05, Kecamatan Sumbang, Kabupaten, Banyumas,', ['0214420123', '0214430164']],
            ['Anjar Triwibowo', '3301021606870007', '085777322111', 'Wirausaha', 'triwibowoanjar@gmail.com', 'Fitri Mulyani', '3302254602900002', '081390332133', 'Ibu Rumah Tangga', 'triwibowoanjar@gmail.com', 'Kedungwuluh 02/04 Kec.Kalimanah Kab.Purbalingga.', ['0214430166']],
            ['Mohammad Lathief Shidiq', '3303062805880002', '085227924913', 'Guru', 'bismillah.shidiq@gmail.com', 'Anita Rahayu', '3303046609960004', '082227068015', 'Ibu rumah tangga', 'alifahhasna09@gmail.com', 'Kedungwuluh RT 05 RW 01, Kalimanah, Purbalingga', ['0214420107']],
            ['Wahyu hidayat', '3303140411820001', '089654958996', 'Pedagang', 'wahyuhidayathidayat047@gmail.com', 'Siti khuswatun hasanah', '3303144708880003', '089647653800', 'Ibu rumah tangga', 'siti khuswatunhasanahsitu@gmail.com', 'Kedungwuluh rt 07 rw 01, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214420108', '0214460318']],
            ['Ali kurniadi', '3302191602770002', '08127553316', 'Berdagang', 'radjakaisar@gmail.com', 'Widiyantiningsih', '3302194706800002', '082227727991', 'Ibu rumah tangga', 'foreverrichxm@gmail.com', 'Klahang RT 02 RW 02 kecamatan Sokaraja Kabupaten Banyumas', ['0214420109']],
            ['Khorizun', '3304040104820008', '082133334556', 'Wirausaha', 'izunkita@gmail.com', 'Atun Fatmawati', '3304046204830006', '085226041166', 'Ibu Rumah Tangga', 'atunfatmawati22@gmail.com', 'Babakan RT 43 RW 11 Kec. Kalimanah Kab. Purbalingga', ['0214420111']],
            ['Imam Fajar Martha', '3303050403860001', '085726433665', 'Wiraswasta', 'imvee3d.art@gmail.com', 'Vita Listiani', '3302166011860001', '082337519483', 'PNS', 'vitalist20@gmail.com', 'RT 001 RW 005, Kel. Purbalingga Lor, Kec. Purbalingga, Kab. Purbalingga, Jawa Tengah', ['0214420143']],
            ['Adiyanto', '3304031510870005', '085219989715', 'Pedagang', 'kenzie.kr5@gmail.com', 'Pipit Safitri', '3304025604920003', '081292788816', 'Ibu Rumah Tangga', 'kradhyasta01@gmail.com', 'Kedungwuluh, RT 02 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214430193']],
            ['Ari Priyanto', '3303122206810003', '083160830572', 'Swasta', 'dzakirah.nur81@gmail.com', 'Nurdiyati', '3303125208810006', '0895402116677', 'Wiraswasta', 'dzakirah.nur81@gmail.com', 'Wirasana, RT 02/07 Purbalingga', ['0214420115']],
            ['Sapto Agus Setiono', '3215050208780006', '081282874185', 'Wiraswasta', 'agoest.st@gmail.com', 'Neng Rini Komalasari', '3215055805860005', '085724149983', 'Ibu Rumah Tangga', null, 'Desa Munjul Rt 12 RW 06 Kecamatan Kutasari Kabupaten Purbalingga', ['0214420117']],
            ['Nurhadi', '3303052205870004', '082265051924', 'Buruh', 'kembarancyty123ae@gmail.com', 'Erna yuniasih', '3303075206920003', 'O81325358633', '-', 'lengkongcyty123@gmail.com', 'Limbangan rt 10 rw 05, kecamatan kutasari, kabupaten purbalingga', ['0214420118']],
            ['Darmawan Tri Santoso', '3302261201860006', '082330030655', 'Wiraswasta', 'teponksubarkah@gmail.com', 'Nurani Luthfi', '3303155311870001', '082233880305', 'Ibu rumah tangga, freelance', 'utipatonah11@gmail.com', 'Perum Grita Perwira Asri, blok B no. 9, Babakan Purbalingga', ['0214420119']],
            ['Mega Fathurrahman', '3303061903880002', '085600001124', 'Serabutan', 'baelah.mega@gmail.com', 'Tri Windarti', '3303055911890004', '085600001124', 'Ibu rumah tangga', 'baelah.mega@gmail.com', 'Babakan RT 21 RW 06 kecamatan Kalimanah kabupaten Purbalingga', ['0214420120', '0214460324']],
            ['Victor Abdullah Zulkarnain', '3303050208880001', '081319999119', 'Swasta', 'victor07581@gmail.com', 'Laely Dwi Anjani', '3303056203890001', '082234343408', 'Ibu rumah tangga', 'anjazulkarnain@gmail.com', 'Jl Wiraguna no 16 purbalingga kidul, purbalingga.', ['0214420121']],
            ['Adi Priyanto', '3302160202900005', '0895411208000', 'Wirausaha', 'adipriyanto0290@gmail.com', 'Marlinah', '3301226606870002', '085600477400', 'Ibu Rumah Tangga', 'sednasaras87@gmail.com', 'Selabaya/ Ruko selabaya indah jl geriliya barat', ['0214420124']],
            ['Purwanto', '3303051503890001', '085826000320', 'Berdagang', 'ipurziidanp@gmail.com', 'Sukesih', '3303154407890001', '082178255578', 'Mengajar di rumah qur\'an', null, 'Karang jambe rt 02/rw 03.kec.padamara.kab.purbalingga', ['0214420125']],
            ['Priyono', '3303061112810004', '082115320975', 'PNS', 'priyono111281@gmail.com', 'Ristiana', '3303066212850005', '087817902886', 'Ibu Rumah Tangga', 'maspripbg99@gmail.com', 'Klapasawit RT 01 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214410082']],
            ['Untung subarman', '3303072705790001', '08989087707', 'Buruh bangunan', 'untungsubarman3@gmail.com', 'Ida mursyidah', '3303076406880001', '085864179538', 'Guru TK suwasta', 'ummuabdurrozaq61@gmail.com', 'Karanglewas RT 11 RW 05, kecamatan Kutasari,kabupaten Purbalingga', ['0214410084']],
            ['Andin Anggoro', '3303051301870001', '081232676414', 'Pegawai Negeri Sipil', 'aanggoro87@gmail.com', 'Ina Asmi Rachmani', '3303054705870001', '081252033559', 'Mengurus Rumah Tangga', 'asma.arrahmah@gmail.com', 'Perum. Berlian Karangsentul Blok F No. 3, RT 02 RW 05, Desa Bojanegara, Kecamatan Padamara, Kabupaten Purbalingga', ['0214410078']],
            ['Abdul Rachman Muslim', '3302203010760001', '081542880795', 'IT', 'dreamcorner@gmail.com', 'Maria Fita Marwati', '3302206604830008', '08112623325', 'IRT', 'vitalogiez@gmail.com', 'Perum Abdi Negara, Kresna IV No 2. Bojaneraga, Padamara - Purbalingga', ['0214410086']],
            ['Aulia riska nur khusna', '3303051303870001', '089619296997', 'Pedagang', 'desynvnv@gmail.com', 'Desi nur fiana', '3303145012920001', '0895348310937', 'IRT', 'desynvnv@gmail.com', 'Galuh RT 08 RW 04, Bojongsari, Purbalingga', ['0214410081']],
            ['Januar imam prabawanto', '3303140201740002', '08990628106', 'Tidak bekerja', 'januarip_1974@yahoo.co.id', 'Werdha adityasari putri', '3303145610790001', '08990628106', 'Ibu rumah tangga', 'januarip_1974@yahoo.co.id', 'Perum trajumalang blok C1, Kandang gampang RT 04/02, kec. Purbalingga, Purbalingga', ['0214410051']],
            ['Yuli Efendi', '3303062812810005', '085227223691', 'Wiraswasta', 'yulie1946@gmail.com', 'Siti Asiyah', '3303065305830002', '0895383067871', 'IBU rumah tangga', 'yulie1946@gmail.com', 'Selabaya RT 02 RW 04 kecamatan Kalimanah kabupaten Purbalingga', ['0214410085', '0214460316']],
            ['Heri Susanto', '3301122002850002', '081326991225', 'Wirausaha', 'hsusanto1912@gmail.com', 'Regi Willi Sartika', '3303154305910001', '082224420283', 'Wiraswasta', 'regiwsartika398@gmail.com', 'Sokawera RT 02 RW 04, Kec. Padamara Kab. Purbalingga', ['0214410088']],
            ['Agus Sutomo', '3304083108670001', '081327017546', 'Swasta', 'septianaratnawati@gmail.com', 'Septiana Ratnawati', '3304124909890003', '08988171169', 'Ibu Rumah Tangga', 'septianaratnawati@gmail.com', 'Kavling Tunas Ilmu Depok Kedungwuluh RT 3 RW 3 Kalimanah', ['0214450243']],
            ['Nurul Abdulloh', '3303141208820001', '088226880280', 'Pedagang', null, 'SURTIWI', '3303146704860001', '088216581589', 'Ibu rumah tangga', 'abdullahnurul718@gmail.com', 'Pekalongan,RT02 RW04 kecamatan Bojongsari, kabupaten Purbalingga', ['0214410091']],
            ['Dadiyono Pulung Nugroho', '3276060904800006', '081586809401', 'Karyawan Swasta', 'd_poeloeng@yahoo.com', 'Amelia Meliawati', '327606181110004', '081586809401', 'Ibu Rumah Tangga', 'd_poeloeng@yahoo.com', 'Graha Permata Selabaya No G14 Selabaya Kalimanah', ['0214440198']],
            ['Imam Choerun', '3303052212820002', '085227747444', 'PNS', 'kdmenjangan2019@gmail.com', 'Santu Mikael Kanti', '3303054404850004', null, 'Mengurus Rumah Tangga', null, 'Kedungmenjangan RT 01 RW 03, Kecamatan Purbalingga, Kabupaten Purbalingga', ['0214440199']],
            ['TM Nurdin', '3302102206820001', '081347106451', 'Mengajar', 'abuyazidnurdin@gmail.com', 'Fatihdaya Khoirani', '332106501860001', '085700578185', 'Ibu Rumah Tangga sambil mengajar privat', 'ummuyazidf2@gmail.com', 'Ponpes Tahfiz An-Naba, Jl. Karangjati, Desa Suro Rt06/Rw04, Kec. Kalibagor, Kab.Banyumas', ['0214440192']],
            ['Jamal', '3303050406650003', '085799401588', 'Wiraswasta', null, 'Khadijah', '3303054501740002', '0895322008589', 'IRT', 'muchafood.id@gmail.com', 'Karangkabur rt 01/ rw 02, kel, bojanegara, kec. Padamara, kab. Purbalingga', ['0214410079']],
            ['yuni setiyono', '3303071306870002', '08895465799', 'berdagang', 'afaninpbg@gmail.com', 'Rokhmah', '3303074307860005', '089508956347', 'pekerja rumah tangga', 'yudhantadavanpramana@gmail.com', 'karangreja RT012 RW006,kecamatan kutasari,kabupaten purbalingga', ['0214460310']],
            ['Aly subarkah', '3303010311930002', '085641654562', 'Berjualan online', 'alysubarkah63@gmail.com', 'Susy haryanti', '3303156710930001', '086l5875986239', 'Ibu rumah tangga', 'susyharyanti701@gmail.com', 'Domisili:Perum puri kalimanah blokA17', ['0214440235', '0214460311']],
            ['Firma Nur Aditya', '3303041005930002', '0895378176311', 'Pedagang', 'firmanuradityaaditya043@gmail.com', 'Trian purwanti', '3303026607910005', '085229406607', 'Dagang', 'trianpurwanti49@gmail.com', 'Penaruban RT 2 RW 2,KALIGONDANG, PURBALINGGA', ['0214460313']],
            ['Tofan Anggana Adi', '330306180590001', '081215050123', 'Pedagang', 'angganatopan123@gmail.com', 'Intan Hasti Utami', '3302186107930001', '0895327373771', 'Ibu Rumah Tangga', 'hasti.intan123@gmail.com', 'Jalan Jambu No. 5 Desa Kalimanah Wetan RT 04 RW 07 Kecamatan Kalimanah Kabupaten Purbalingga', ['0214460314']],
            ['Mulki Indana Zulfa', '3209180812860009', '0895341295031', 'ASN', 'admiportfolio@gmail.com', 'Siti Mutmainah Anwar', '3274034811860007', '085659670038', 'IRT', 'bundaiza2023@gmail.com', 'Griya Kalika blok C12A Sokawera Kecamatan Padamara, Kabupaten Purbalingga', ['0214430194', '0214430179', '0214460315']],
            ['Andi Kurniawan', '3303142907920002', '085727417779', 'Wiraswasta', 'andimaharani2003@gmail.com', 'Mei Maharani', '3303146405920002', '085747745774', 'Guru SD', 'me.mheiy@gmail.com', 'Desa Gembong, RT 03 RW 02, Kecamatan Bojongsari, Kabupaten Purbalingga', ['0214460317']],
            ['Arif Hidayatullah', '3302112404810005', '082135366338', 'Pengajar', 'abuumamamh@gmail.com', 'Durrotun Nasikah', '3302116412840002', '081392179935', 'Tidak bakerja', 'hobimasak@gmail.com', 'RT 1 RW 4 desa kalisube,kec Banyumas,kab,Banyumas', ['0214460319']],
            ['Dwi Nurhidayat', '3303051702950001', '089690782228', 'Pedagang', 'hidayat.microlux@gmail.com', 'Ruli Dwi Saputri', '3303056504970001', '0895385067106', 'ibu rumah tangga', 'rulidwi32@gmail.com', 'Kedungmenjangan Rt 04  Rw02 Kec. Purbalingga Kab. Purbalingga', ['0214460321']],
            ['Heri setyadi', '3303060104880001', '088238126676', 'Tetap bekerja', 'herisetyadi008@gmail.com', 'Nur Adibatul Wajihah', '3327095809970012', '85225279570', 'Mengajar di Kelompok tahfidz', 'herisetyadi008@gmail.com', 'Babakan rt10/02,Kalimanah, Purbalingga', ['0214460323']],
            ['Agus Priyanto', '3303092208820006', '081323576252', 'Pengajar', 'priyanto.agus4022@gmail', 'Irianti Purwaningsih', '3303056803850002', '0895421935328', 'Ibu rumah tangga', null, 'Perum Puri Babakan lama, no 76, RT 32 RW 08, Kecamatan Kalimanah, Purbalingga', ['0214440239', '0214460325']],
            ['Izandi', '330406261190004', '085774007400', 'Buruh', 'zndbr91@gmail.com', 'Sus Sabarniati', '3304124608940002', '085156008902', 'Ibu Rumah Tangga', null, 'Jl. Baturraden Timur. Gang Sadewa RT4 RW2 No. 7. Sumbang, Banyumas.', ['0214460326']],
            ['Budi Haryanto', '3303051301770003', '085385287879', 'Pedagang', 'zaenabyanto@gmail.com', 'Setianingsih', '3303055510850003', '089675722749', 'Pengajar TAUD & Ibu Rumah Tangga', 'setianing01.s@gmail.com', 'Kandang Gampang RT 03 RW 05, Kecamatan Purbalingga, Kabupaten Purbalingga', ['0214460327']],
            ['Jamhari', '3303010208730002', 'Tdkaktif/sdhtdkada', 'Buruh lepas', null, 'Indah wigati', '3303016402750001', '089658088499', 'Ibu rumah tangga  dan penjahit rumahan', 'indahummuyusuf75@gmail.com', 'Karang tengah rt 05/rw03, kemangkon, Purbalingga', ['0214450267']],
            ['Ndaru Satrio', '3302210509870001', '082281075295', 'Dosen', 'satrio.ndaru9@gmail.com', 'Nur Apriani', '3171046104890004', '08159230700', 'IRT', 'ia.apriani@gmail.com', 'Kedungwuluh RT 05 RW 02, Kalimanah, Purbalingga', ['0214450268']],
            ['Doni', '3303062012880003', '0895349865488', 'Dagang', 'kdoni7951@gmail.com', 'Nurhayati', '3327086901880081', '08971860090', 'Ibu rumah tangga', 'nurhayati127898@gmail.com', 'Klapasawit RT 2 RW 5, Kalimanah, purbalingga', ['0214450270']],
            ['Purnawan', '3303062007810003', 'tidakpunyahp', 'Buruh', null, 'Ita Susanti', '3303064204840002', '085640961141', 'Ibu Rumah Tangga', 'susantiita0204@gmail.com', 'Klapasawit RT 02 RW 01', ['0214450271']],
            ['Subhan kurniawan', '333051902780005', '081328746391', 'wiraswasta', 'subhankurniawan825@gmail.com', 'sari rohyati', '3303056804840008', '089678781158', 'ibu rumah tangga', 'sarirohyati83@gmail.com', 'Pagedangan RT 03 RW 06   Kecamatan Purbalingga kidul Kabupaten  Purbalingga', ['0214450272']],
            ['Aris Santosa', '3303150907790001', '085227852122', 'Wiraswasta', null, 'Mei Purwati', '3303156105850001', '085291822449?', 'Ibu Rumah Tangga dan guru', 'meypurwatipbg@gmail.com', 'Kedungwuluh RT 04 RW 01 Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214450273']],
            ['Romi wijaya', '3301010703910002', '082138030401', 'Wiraswasta', 'romiwijaya070391@gmail.com', 'Elsa Nurida', '3303074406910001', '082221044498', 'Ibu rumah tangga', 'elshanurida@gmail.com', 'Karangklesem RT 03 RW 02 kec. Kutasari kab. Purbalingga', ['0214450274']],
            ['Margo Priastowo', '3302192903790006', '081326309503', 'Serabutan', 'priastowomargo@gmail.com', 'Sita Arimbie', '3302194211850003', '081215872049', 'Ibu Rumah Tangga', 'maulidyarz14@gmail.com', 'Pengadegan rt08/rw04, Kec.Pengadegan, Kab.Purbalingga', ['0214450288']],
            ['Rizal Fauzk', '3303051205870002', '085173165491', 'PNS Guru', 'fauzirizal059@gmail.com', 'Wiwied Dewintari Haryani', '3303064205880002', '085159030158', 'IRT', 'junoque@gmail.com', 'Rt 08 Rw 04. Des karangsari Kalimanah', ['0214450275']],
            ['Trimo', '3303062301720002', '088212961253', 'Buruh Bangunan', null, 'Suciana', '3303064303910001', '088212961253', 'Ibu rumah tangga', null, 'Kedungwuluh RT 1 RW 2, kecamatan Kalimanah kabupaten Purbalingga', ['0214450276']],
            ['Afrizal', '3175071908840014', '08562324224', 'Pedagang Bensin ECER & ikan hias', 'afrizalstudio@gmail.com', 'Devi Nopiyanty S. Kep', '3214014711910005', '08562165550', 'Ibu rumah tangga', 'devinopiyanty@gmail.com', 'Pengalusan, RT 05 RW 01, kecamatan Mrebet, Kabupaten Purbalingga', ['0214450277']],
            ['Tugiono', '3303063006810001', '083114284766', 'Buruh', 'alifnurrizky.akbar00@gmail.com', 'Suratni', '3303066509850002', '083821973186', 'Ibu Rumah tangga', 'alifnurrizky.akbar00@gmail.com', 'Kedungwuluh RT 02 RW 04 Kecamatan Kalimanah Kabupaten Purbalingga', ['0214450278']],
            ['Muhamad Riana Yudianto', '3204051902930008', '087822994032', 'Guru', 'mrianayudianto@gmail.com', 'Gletika Halmaherani', '3204325209890007', '087722875749', 'Ibu Rumah Tangga', 'g.ummumaryam@gmail.com', 'Kavling Tunas Ilmu Blok B1 No.1 RT 04 RW 04  Desa Kedungwuluh, Kec Kalimanah, Kab Purbalingga', ['0214450279']],
            ['Rochiman', '3302200810880004', '081215444410', 'Wiraswasta', 'rochiman@gmail.com', 'Rusmiati', '3302275206950002', '085719811233', 'Ibu rumah tangga', 'miateguhjaya@gmail.com', 'Banteran RT 4 RW 5 kec sumbang kab banyumas', ['0214450280']],
            ['Rouf Aizat', '330306040190001', '082137645625', 'Pegawai BUMN', 'rouf.aizat@gmail.com', 'Amalia Utari', '3216025002940016', '081280946409', 'IRT', 'amaliaut76@gmail.com', 'Jl. Jati, Rt. 1/ Rw. 3, Desa Kalimanah Wetan, Kec. Kalimanah Purbalingga', ['0214460331']],
            ['Taufik Dermansyah', '3303141511810001', '081327097271', 'Karyawan swasta', 'taufikdermansyah@gmail.com', 'Retno Sumanti', '3303144906830003', '082136291172', 'Rumah Tangga', 'retnosumanti84@gmail.com', 'Kelurahan bojong Rt03Rw01 Kec. Purbalingga Kab. Purbalingga', ['0214450281']],
            ['Sarkono', '3303061905750003', '085738067587', 'Pedagang', 'pakjenggot804@gmail.com', 'Nurul Fatichach', '3303066305800002', '082325842090', 'Ibu rumah tangga', 'nurulpbg75@gmail.com', 'Klapasawit RT 02 RW 07, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214450282']],
            ['Eron Ferlando', '3303062303840002', '082153413644', 'Wiraswasta', 'eferlandiron@gmail.com', 'Hanum Ayuningtyas', '3374054805900004', '081225657143', 'Wiraswasta', 'ayhanum90@gmail.com', 'Jalan Mangga Timur A1 perumahan selabaya indah desa selabaya kecamatan kalimanah', ['0214450283']],
            ['Kholidun', '3201010106740018', '081215425200', 'Karyawan pondok (TI mart)', null, 'Eti Susanti', '3204174304800021', '085226318044', 'Guru TK', null, 'Kedungwuluh,RT 08 RW 01,kalimanah kabupaten Purbalingga', ['0214450284']],
            ['Marzuki Fajar', '3303141603850001', '081548602000', 'Pedagang', 'marzukifajar@gmail.comq', 'Anna Avionita', '3303055705880003', '085640306770', 'ibu rumah tangga', 'avionitaana@gmail.com', 'Kajongan RT 002 RW 002, Kecamatan Bojongsari, Kabupaten Purbalingga', ['0214440222']],
            ['Toto barkah setyadi', '3303051008880001', '085800763136', 'Guru', 'totobarkah24@gmail.com', 'Tabah rinaya', '3303066004890002', '085855720557', 'Mengurus rumah tangga', 'tabahkdwuluh@gmail.com', 'Kedungwuluh rt 04 rw 01, kecamatan kalimanah, kabupaten purbalingga', ['0214440223']],
            ['Ryan Baihaqi', '3303061407890004', '08112786630', 'Wiraswasta', 'pjmmobil89@gmail.com', 'Dwianinda Fegi Armitha', '3303054801900001', '087710459728', 'Ibu Rumah Tangga', 'annasya1309@gmail.com', 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Purbalingga', ['0214440224']],
            ['Ahmad Dewanto', '3302261012830008', '087725344900', 'Karyawan Swasta', 'dewanto10@gmail.com', 'IIS ROVINGATUN', '3303064503900003', '085876013459', 'Ibu Rumah Tangga', 'bundakusayang05@gmail.com', 'Kedungwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214420126', '0214440225']],
            ['Hermawan', '3303072010900001', '082135744100', 'Mubaligh', 'hermawan.abuarwanaila@gmail.com', 'Rahmi Destiani', '3303074412940003', '082135744100', '-', 'rahmidestiani@gmail.com', 'Desa karangcegak RT 12 RW 05, Kec. Kutasari, Kab. Purbalingga', ['0214460289']],
            ['Wiwit Yanuar Udin Nugroho', '3303060601860001', '0895322352124', 'Karyawan', 'nofikapuspitasari90@gmail.com', 'Nofika Puspitasari', '3303064602890002', '0895322352124', 'Ibu rumah tangga dan tutor PKBM', 'nofikapuspitasari90@gmail.com', 'Kedungwuluh RT 8 RW 1, kecamatan Kalimanah kabupaten purbalingga', ['0214440226']],
            ['Purnawan Wijonarko', '6402101207750004', '081347172737', 'Wiraswasta', null, 'Senkip Evamotik Akbar', '6402105302820003', '082322841150', 'Mengurus rumah tangga', 'senkip82@gmail.com', 'Karang jambe RT.2 RW.3 Kecamatan Padamara Kabupaten Purbalinggamra', ['0214440227']],
            ['Eko handoko', '3276011505840004', '085731133370', 'Berdagang', null, 'Lili susanti', '3302084709880001', '089677199715', 'Ibu rumahtangga', 'lilysusanti.han@gmail.com', 'Banteran RT 01 RW 05 kecamatan Purwokerto kabupaten Banyumas', ['0214440228']],
            ['Muhammad Fikri Saputro', '1803071709920003', '081335617292', 'Karyawan Swasta', 'fikri17saputro@gmail.com', 'Shila Anesh Sundari', '3303146105920001', '089507994870', 'Ibu Rumah Tangga', 'uhumaira36@gmail.com', 'Galuh RT 008 RW 004, Kecamatan Bojongsari, Kabupaten Purbalingga', ['0214440229']],
            ['Fadil Dikty Mustasyfa', '3304020911910001', '082340635615', 'Wiraswasta', null, 'Sofia Hikmawati', '3329035806970003', '0895377204485', 'Pengajar TK', 'sofala.forever@gmail.com', 'Kedungwuluh RT.01, RW.01. Kalimanah. Purbalingga', ['0214440230']],
            ['Firman Maolana Abdullah', '3302222212830001', '085226050070', 'Guru', 'firmanabdullah512@gmail.com', 'Ita Nur Arifiyah', '3324077010900002', '081229515057', 'IRT dan guru', 'firmanita265@gmail.com', 'Kedunhwuluh RT 03 RW 04, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214420139', '0214440231']],
            ['Pamula Panji Sanubari', '3303050701890003', '81548812016', 'Wirausaha', null, 'Uji Astuti', '3303045505890001', '081548812016', 'Ibu rumah tangga', 'asaujay@yahoo.com', 'Jl.Jend Sudirman gg.Baraba 23 Rt 03/03 Purbalingga Kidul - Purbalingga', ['0214440232']],
            ['Sumardi', '3303051409850002', '085747781223', 'Guru', 'maryamsarah34@gmail.com', 'Umu Habibah', '3303064901810001', '85726244426', 'Ibu Rumah Tangga', 'umu.habibah.ai@gmail.com', 'Jompo RT 01 RW 04 Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214440233']],
            ['Karsono', '3327033112810001', '081808161353', 'Swasta', 'onocornea@gmail.com', 'Sulis Tiyowati', '33022148802900004', '082210152735', 'Ibu rumah tangga', 'bintangasa28@gmail.com', 'Gandatapa RT 05 RW 06, kecamatan Sumbang, Kabupaten Banyumas', ['0214440234']],
            ['Husni Abdul Aziz Rais Al Habibi', '3303060406910001', '08988771080', 'Buruh pabrik kayu', 'husnialhabibi@gmail.com', 'Mega Triana', '3303064808930001', '089669662383', 'Ibu rumah tangga', 'alhabibimega@gmail.com', 'Kalimanah kulon RT 1 RW 2, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214440236']],
            ['Taufiqurrohman', '3303122607840001', '08572657234', 'Wiraswasta', 'taufiqruhama@gmail.com', 'Siti Wulandari', '3329064310960004', '085641669056', 'Ibu rumah tangga', 'wulanummuruhma@gmail.com', 'Pekiringan Dusun 5 RT 02/10 Pekiringan Karangmoncol Purbalingga', ['0214440237']],
            ['Aulia Rahman Hakim', '3305100201890002', '085227322889', 'PNS', 'rahmanhakim1989@gmail.com', 'Yuliati', '3305075508910003', '081228756633', 'Ibu Rumah Tangga', 'rahmanhakim1989@gmail.com', 'Desa Purbalingga Wetan RT 4 RW 9 Kec. Purbalingga, Kab. Purbalingga', ['0214440238']],
            ['Biyonic Banuyan Prabhita', '3303153110830001', '085640544580', 'Serabutan', 'prabhitabiyonic@gmail.com', 'An-Nafi Aisyah Zur\'ah', '3374116007950001', '085159493365', 'Rumah Tangga', 'anafi.aisyah95@gmail.com', 'Padamara RT. 003 RW. 003, Kec. Padamara, Kab. Purbalingga', ['0214440240']],
            ['Nurdiono', '3303153112850001', 'Hpbersamaistri', 'Petani, tukang traktor', 'sulifahsokawera@gmail.com', 'Sulifah', '3303154902770001', '0894643184', 'Ibu rumah tangga', 'sulifahsokawera@gmail.com', 'Sokawera Rt 01 RW 05,kecamatan Padamara, kabupaten Purbalingga', ['0214430169']],
            ['Pujan Sukito', '3305081509830003', '085602158298', 'Guru', 'tsaqifmuhammad40@gmail.com', 'Erfina Kurnia Sari', '3305086908870001', '085743825086', 'Tidak bekerja', 'tsaqifmuhammad40@gmail.com', 'Perumahan Puri Tama Indah blok Q4. Gemuruh. RT 2 RW 8, Padamara. Purbalingga', ['0214430171']],
            ['Igit setiawan', '3303050305870002', '082225339991', 'Dagang sangkar burung', 'igitzsangkar@gmail.com', 'Dyah Maretnowati', '3303054803890001', '082225339992', 'Ibu rumah tangga', null, 'Kandang gampang rt 03 rw 05 ,kecamatan /kabupaten Purbalingga', ['0214430172']],
            ['Era Wibowo', '3471133005750003', '088806667171', 'Swasta', 'eranitikan@gmail.com', 'Ira Maryati', '3471134410780002', '081914967171', 'Ibu Rumah Tangga', 'umi.bumburujak@gmail.com', 'Perum Griya Bantar Indah Blok J no.1 Rt 02 Rw 05 Bantarwuni Kembaran Banyumas', ['0214430173']],
            ['NURINTO', '3303081304890002', '088220139847', 'Jual beli barang bekas', 'nurinto049@gmail.com', 'Lellia Agustianingsih', '3303085608960003', '0895417625447', 'Ibu rumah tangga dan jualan es jajan', 'nurinto049@gmail.com', 'Mangunegara RT 6 RW 1 , Kecamatan Mrebet, Kabupaten Purbalingga', ['0214430174']],
            ['Liman Pujo Waluyo', '3303150101870001', '082325911416', 'Guru Swasta', 'faslijannah@gmail.com', 'Astri Andikarlina', '3303066109920002', '085691767822', 'Mengurus Rumah Tangga', null, 'Kedungwuluh RT 08 RW 02, Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214430175']],
            ['Arri Indiarto Merenda', '3303051302840003', '0811291913', 'Wiraswasta', 'arinme@gmail.com', 'Fitri Yulianingrum', '3303055207850001', '08561650572', 'Ibu Rumah Tangga', 'fitri.yulianingrum1985@gmail.com', 'Jl cempaka raya no 4 rt 006 rw 007 Perum Penambongan  Purbalingga', ['0214430176']],
            ['Iwan nurohman', '3303060209860001', 'Tidakada', 'Buruh harian lepas', null, 'Resti diyanti', '3303064911910002', '088221145480', 'Wira usaha', 'ummuhanifah1122@gmail.com', 'Grecol rt 04 rw 01,kecamatan kalimanah,kabupaten purbalingga', ['0214410101', '0214430177']],
            ['Tonny Bayu Aji', '3216080110780011', '089602154320', 'Peternak Ayam Telur', 'ynnotuyab@gmail.com', 'Meta Djahara', '3216085209840017', '081229931979', 'Ibu Rumah Tangga', 'mdjahara@yahoo.com', 'Bancar RT 003 RW 003, Kecamatan Purbalingga, Kabupaten Purbalingga', ['0214430178']],
            ['ERWIN SETIAWAN', '3303051301860006', '0895328144837', 'PEDAGANG BUBUR AYAM', null, 'IIS NOFIANI', '3303146211880001', '0895328144837', 'Ibu rumah tangga', null, 'Brobot RT 5 RW 2,Kecamatan Bojongsari, Kabupaten Purbalingga', ['0214430180']],
            ['Hayat Humaydi', '3303150606910001', '089674946711', 'Wiraswasta', 'hayathumaydi3@gmail.com', 'Ria Kartikasari', '3303065212910001', '089665861188', 'Pengajar Taud', 'riakartikasari92@gmail.com', 'Kalikabong RT 02 RW 04 kecamatan Kalimanah, Kabupaten Purbalingga', ['0214410093', '0214430181']],
            ['SUGITO', '3302102203900003', '081390730653', 'Pedagang', 'sugitoykhanf@gmail.com', 'YENI KURNIAWATI', '3302205705890003', '081578225868', 'Guru SD', null, 'karang tengah, rt 6 rw 2 kecamatan kembaran kabupaten banyumas', ['0214430182']],
            ['Affif sutriyono', '3303011711790002', '081536601942', 'Wiraswasta', 'afifsutriono8@gmail.com', 'Leli Robitoh', '3303017008880001', '085641746652', 'Wiraswasta', 'lelikhansa17@gmail.com', 'Karangtengah Rt 07 Rw 04, kecematan Kemangkon, Kabupaten Purbalingga', ['0214430183']],
            ['Kuat pamuji', '330306060183005', '082135843600', 'Pedagang', 'pamujikuat05@gmail.com', 'Nuning widyaningsih', '3303156202830006', '088980371036', 'Ibu rumah tangga', null, 'Bojanegara rt 03rw02, kecamatan padamara, kabupaten purbalingga', ['0214430184']],
            ['Niko Priyanto', '3303040104850001', '087737780051', 'Swasta', 'ibnulharist@gmail.com', 'Siti Dyah Jayanti', '3371015504880002', '087764691569', 'Ibu Rumah tangga', 'shofiyyahdyah1@gmail.com', 'Kaligondang RT 03 RW 04 kecamatan Kaligondang kabupaten Purbalingga', ['0214410106', '0214430185']],
            ['Eko Yulianto', '3303051407800001', '085712999892', 'Karyawan honorer', 'ekoyulianto_714@yahoo.co.id', 'Ani Wahyuni', '3303025108830002', '08163655180', 'Ibu Rumah Tangga', null, 'Kembangan Rt 04 Rw 05, Bukateja, Purbalingga', ['0214430186']],
            ['Iwan Widya Tinarto', 'tidak tahu', 'tidaktahu', 'tidak tahu', null, 'RETNO RIA OCTAFIANA', '3303055110850001', '081326324359', 'guru TK', 'kendedez358@gmail.com', 'Purbalingga lor RT 02 RW 03 , kecamatan Purbalingga, Kabupaten Purbalingga', ['0214430187']],
            ['SAHELAN', '3303061601790001', 'tidakada', 'Buruh dagang', null, 'Umi Solikhah', '3303064410840002', '082112310786', 'Pengajar TK Isalam Nashirus Sunnah Purbalingga', null, 'Babakan RT 15 RW 04, Kalimanah, Purbalingga', ['0214430188']],
            ['Rizal Fauzi', '3303051205870002', '085173165491', 'GURU SMK N Kutasari', 'fauzirizal059@gmail.com', 'Wiwied Dewintari Hadyani', '3303064205880002', '085159030158', 'IRT', 'junoque@gmail.com', 'Desa kr sari Rt 08 Rw 04 kalimanah Purbalingga', ['0214430189']],
            ['Riyan setiawan', '3303050102870004', '087715127391', 'Dagang', 'riyansetiawank18k@g mail.com', 'Sri martini', '3303096703910001', '0897665561715', 'Ngajar TAUD', 'sri2703martini@gmail.com', 'Purbalingga kidul RT 02,RW 01,Purbalingga kidul,Purbalingga', ['0214410103', '0214420128']],
            ['Iguh prasetianto', '3303061404710002', '089659932220', 'Wiraswasta', 'iguhprasetianto@gmail.com', 'Winarni', '3303066006730003', '081297606645', 'Ibu rumah tangga', 'dhiyaaqila09@gmail.com', 'Kedungwuluh RT 06 RW 01 kecamatan Kalimanah ,Kabupaten Purbalingga', ['0214420130']],
            ['Ari Wibowo', '3303070501850004', '085291525959', 'Pedagang', 'arsywibowo25@gmail.com', 'Susiati', '3303076004850003', '085865988832', 'Ibu Rumah Tangga', null, 'Dusun 2 Meri RT 15 RW 06, Kecamatan Kutasari, Kabupaten Purbalingga', ['0214420131']],
            ['Irwanto', '3303051312840002', '089685511756', 'Wiraswasta', 'irwanzhayya@gmail.com', 'Shinta Irani', '3303054411810002', '089508937473', 'Ibu Rumah Tangga', 'ishin4273@gmail.com', 'Gemuruh RT 01 RW 07, Kecamatan Padamara  Kabupaten Purbalingga', ['0214420132']],
            ['Mahfudin', '3303141405840001', '087844825016', 'Karyawan Swasta', 'abuxsafanah@gmail.com', 'Julastri', '3303114606850002', '085964230630', 'Guru TK', 'abuxkhaulah@gmail.com', 'Galuh RT 04 RW 03', ['0214420133']],
            ['Isto Wurio', '3303051307800005', '0885600636960', 'Ustadz', null, 'Reni Yuliyanti', '3303055807830003', '082328381600', 'Bidan', 'reniyulianti073@gmail.com', 'Kedungwuluh RT 2 RW 4,Kecamatan Kalimanah,Kabupaten Purbalingga', ['0214420136']],
            ['Eko Diyono', '3302100803780003', 'Tidakada', 'Membantu orang tua di pasar', null, 'Estining Pribadi', '3303056404870003', '081327933877', 'Ibu rumah tangga', 'estiningpribadi@gmail.com', 'Pekaja RT 05 RW 03 Kecamatan Kalibagor Kabupaten Banyumas', ['0214420138']],
            ['Susilo eko putro', '3303142808780002', '081391040369', 'Pengrajin kayu_', 'susiloekoputro3@gmail.com', 'Yuliati', '3303145507840001', '085706346365', 'Ibu rumah tangga', 'pagutan0213@gmail.com', 'Bojongsari rt02 rw13 bojongsari purbalingga', ['0214420141']],
            ['Tuwarno', '3303070603930002', '0882005655534', 'Petani', null, 'Rizki Saputri', '3303145205930002', '08980661250', 'Ibu rumah tangga', 'saputririzki119@gmail.com', 'Karang Banjar RT 16 RW 06, kecamatan Bojongsari, kabupaten Purbalingga', ['0214410095']],
            ['Okto Parmono', '3303051610860001', '0895622396996', 'Dagang', 'oktopbg12@gmail.com', 'Nani Juwariyah', '3303054805900003', '085700618861', '-', 'nani.juwariyah@gmail.com', 'Purbalingga Kidul RT 03 RW 06, Kecamatan Purbalingga, Kabupaten Purbalingga', ['0214410096']],
            ['Ruspriyanto, AMK', '3303152603840002', '0882006210885', 'Perawat', 'ruspri84@gmail.com', 'Ririn Windia Sari, AMK', '3303155512850002', '081327846373', 'PNS', 'ummusumayyahpbg2020@gmail.com', 'Sokawera RT 2 RW 4 kecamatan Padamara kabupaten Purbalingga', ['0214410097']],
            ['Eko prihayanto', '3303062001790004', '081391709081', 'Karyawan swasta', 'faisol pbg@gmail.com', 'Poniati', '3303065102830001', '085700919918', 'Karyawan swasta', 'faisol pbg @gmail.com', 'Klapasawit RT 1 RW 2 , kalimanah, purbalingga', ['0214410098']],
            ['Hari Waluyo', '3302202306800005', '08994499759', 'Wiraswasta', 'hariwaluyo51346@gmail.com', 'Reni Yustanti', '3302205911860005', 'Tidakada(hp1barengsamasuami)', 'Ibu rumah tangga', null, 'Sambeng Kulon RT 04 RW 01 kecamatan Kembaran kabupaten Banyumas', ['0214410100']],
            ['Yuniarto', '3303063006800002', '082221820408', 'Wiraswasta', 'yuniarto1249@gmail.com', 'Dwi Murwatiningsih', '3303065409830001', '085186867500', 'Ibu Rumah Tangga', 'dwimurwatiyuniar@gmail.com', 'Kedungwuluh RT 08 RW 02 Kecamatan Kalimanah, Kabupaten Purbalingga', ['0214410062']],
            ['Untung Prasetyo', '3671052608710003', '081225456304', 'Wiraswasta', 'pras_sut05@yahoo.com', 'Reni', '3671054505770021', '081218591170', 'Ibu rumah tangga', 'uncuren0505@gmail.com', 'Jl letkol isdiman no 01 rt/rw 01/04, kecamatan purbalingga kidul, kabupaten purbalingga', ['0214410102']],
            ['Udayanto Wibowo', '3303150904750004', '082136281956', 'Karyawan Swasta', 'wibowoudayanto@yahoo.com', 'Evi Nurnaningsih', '3303156912770001', '082227047639', 'Ibu rumah tangga', 'dobog888@gmail.com', 'Desa Bojanegara, RT 01 RW 03, Kecamatan Padamara, Kabupaten Purbalingga Jawa Tengah', ['0214410104']],
            ['Teguh Sukanto', '3303052401800001', '082227917382', 'Wiraswasta', 'sahteguh7@gmail.com', 'Lestari Suhartanti', '3303066004860002', '082227917382', 'Ibu Rumah Tangga', 'suhartanti@gmail.com', 'Desa Purbalingga Kidul, RT.002/RW.001, Kecamatan Purbalingga, Kabupaten Purbalingga', ['0214410059']],
            ['Mustofa', '3303060106790001', '085747541696', 'Kurir katering', 'alifmustofa79@gmail.com', 'Eka Trisnawati', '3303065806820001', '085799305952', 'Ibu rumah tangga', null, 'Jompo Rt 01 RW 04 kec kalimanah kab purbalingga', ['0214400048']],
        ];

        $credentials = [];
        $usedEmails = [];

        foreach ($families as [$namaAyah, $nikAyah, $hpAyah, $pekerjaanAyah, $emailAyah, $namaIbu, $nikIbu, $hpIbu, $pekerjaanIbu, $emailIbu, $alamat, $childrenNis]) {
            // Oldest child's NIS (first in the sorted list)
            $nisForUsername = $childrenNis[0];

            // Collect student IDs
            $childStudentIds = [];
            foreach ($childrenNis as $nis) {
                if (isset($studentMap[$nis])) {
                    $childStudentIds[] = $studentMap[$nis];
                }
            }

            // === Father ===
            if ($namaAyah) {
                $usernameAyah = 'A' . $nisForUsername;
                $emailAyahFinal = $emailAyah ?: $usernameAyah . '@kajian.griyaquran.web.id';
                $emailAyahFinal = $this->ensureUniqueEmail($emailAyahFinal, $usedEmails);
                $usedEmails[] = $emailAyahFinal;

                $userAyah = User::create([
                    'name'      => $namaAyah,
                    'username'  => $usernameAyah,
                    'email'     => $emailAyahFinal,
                    'password'  => $usernameAyah,
                    'role_id'   => $waliSantriRole->id,
                    'phone'     => $hpAyah,
                    'is_active' => true,
                ]);

                $parentAyah = ParentModel::create([
                    'user_id'    => $userAyah->id,
                    'type'       => 'father',
                    'nik'        => $nikAyah,
                    'occupation' => $pekerjaanAyah,
                    'address'    => $alamat,
                ]);

                foreach ($childStudentIds as $studentId) {
                    $parentAyah->students()->attach($studentId, [
                        'relationship'       => 'biological',
                        'is_primary_contact' => true,
                    ]);
                }

                $credentials[] = ['Ayah', $namaAyah, $usernameAyah, $usernameAyah, $emailAyahFinal, $hpAyah ?? '', implode(', ', $childrenNis)];
            }

            // === Mother ===
            if ($namaIbu) {
                $usernameIbu = 'B' . $nisForUsername;
                $emailIbuFinal = $emailIbu ?: $usernameIbu . '@kajian.griyaquran.web.id';
                $emailIbuFinal = $this->ensureUniqueEmail($emailIbuFinal, $usedEmails);
                $usedEmails[] = $emailIbuFinal;

                $userIbu = User::create([
                    'name'      => $namaIbu,
                    'username'  => $usernameIbu,
                    'email'     => $emailIbuFinal,
                    'password'  => $usernameIbu,
                    'role_id'   => $waliSantriRole->id,
                    'phone'     => $hpIbu,
                    'is_active' => true,
                ]);

                $parentIbu = ParentModel::create([
                    'user_id'    => $userIbu->id,
                    'type'       => 'mother',
                    'nik'        => $nikIbu,
                    'occupation' => $pekerjaanIbu,
                    'address'    => $alamat,
                ]);

                foreach ($childStudentIds as $studentId) {
                    $parentIbu->students()->attach($studentId, [
                        'relationship'       => 'biological',
                        'is_primary_contact' => false,
                    ]);
                }

                $credentials[] = ['Ibu', $namaIbu, $usernameIbu, $usernameIbu, $emailIbuFinal, $hpIbu ?? '', implode(', ', $childrenNis)];
            }
        }

        return $credentials;
    }

    /**
     * Ensure email uniqueness within this batch and database.
     */
    private function ensureUniqueEmail(string $email, array $usedEmails): string
    {
        $original = $email;
        $counter = 1;

        while (in_array($email, $usedEmails) || User::where('email', $email)->exists()) {
            $parts = explode('@', $original);
            $email = $parts[0] . $counter . '@' . ($parts[1] ?? 'kajian.griyaquran.web.id');
            $counter++;
        }

        return $email;
    }

    /**
     * Export credentials to CSV.
     */
    private function exportCredentials(array $credentials): void
    {
        $filename = 'credentials_wali_santri_' . now()->format('Y-m-d_His') . '.csv';
        $filepath = base_path($filename);

        $fp = fopen($filepath, 'w');
        fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
        fputcsv($fp, ['Type', 'Nama', 'Username', 'Password', 'Email', 'No HP', 'NIS Anak']);

        foreach ($credentials as $cred) {
            fputcsv($fp, $cred);
        }

        fclose($fp);

        $this->command?->info("📄 Credentials exported: {$filepath}");
    }
}
