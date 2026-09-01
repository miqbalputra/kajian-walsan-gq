<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Services\DataIndukRosterSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DataIndukRosterSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_then_updates_the_local_student_read_model(): void
    {
        $class = ClassRoom::create(['name' => 'Mustawa 1', 'level' => '1', 'is_active' => true]);
        config()->set('data-induk.enabled', true);
        config()->set('data-induk.base_url', 'https://data-induk.test/api/v1');
        config()->set('data-induk.api_key', 'mdi_test_key');
        config()->set('data-induk.class_map', ['class-master-uuid' => $class->id]);

        Http::fake([
            'https://data-induk.test/api/v1/integrations/students*' => Http::sequence()
                ->push(['data' => [[
                    'id' => 'student-master-uuid',
                    'nis' => '001122',
                    'fullName' => 'Abdullah Santri',
                    'gender' => 'L',
                    'classId' => 'class-master-uuid',
                ]]])
                ->push(['data' => [[
                    'id' => 'student-master-uuid',
                    'nis' => '001122',
                    'fullName' => 'Abdullah Santri Diperbarui',
                    'gender' => 'L',
                    'classId' => 'class-master-uuid',
                ]]]),
        ]);

        $created = app(DataIndukRosterSyncService::class)->sync();

        $this->assertSame(1, $created['created']);
        $this->assertDatabaseHas('students', [
            'data_induk_id' => 'student-master-uuid',
            'nis' => '001122',
            'name' => 'Abdullah Santri',
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);

        $updated = app(DataIndukRosterSyncService::class)->sync();

        $this->assertSame(1, $updated['updated']);
        $this->assertSame('Abdullah Santri Diperbarui', Student::where('data_induk_id', 'student-master-uuid')->value('name'));
    }
}
