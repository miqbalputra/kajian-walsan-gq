<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\User;
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class AdminBackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_open_and_download_backups(): void
    {
        $admin = $this->userWithRole('admin');
        $panitia = $this->userWithRole('panitia');

        $this->actingAs($admin)
            ->get(route('admin.backups.index'))
            ->assertOk()
            ->assertSee('Backup SQL penuh')
            ->assertSee('Backup XLSX lengkap');

        $this->actingAs($panitia)
            ->get(route('admin.backups.index'))
            ->assertForbidden();

        $this->actingAs($panitia)
            ->get(route('admin.backups.sql'))
            ->assertForbidden();
    }

    public function test_admin_can_download_sql_and_xlsx_backups(): void
    {
        $admin = $this->userWithRole('admin');
        $directory = storage_path('app/testing-backups');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $sqlPath = $directory.DIRECTORY_SEPARATOR.'backup.sql';
        $xlsxPath = $directory.DIRECTORY_SEPARATOR.'backup.xlsx';
        file_put_contents($sqlPath, '-- full SQL backup');
        file_put_contents($xlsxPath, 'xlsx content');

        $backup = $this->createMock(DatabaseBackupService::class);
        $backup->expects($this->once())->method('createSqlBackup')->willReturn(['path' => $sqlPath, 'name' => 'backup.sql']);
        $backup->expects($this->once())->method('createXlsxBackup')->willReturn(['path' => $xlsxPath, 'name' => 'backup.xlsx']);
        $this->app->instance(DatabaseBackupService::class, $backup);

        $this->actingAs($admin)->get(route('admin.backups.sql'))->assertDownload('backup.sql');
        $this->actingAs($admin)->get(route('admin.backups.xlsx'))->assertDownload('backup.xlsx');

        @unlink($sqlPath);
        @unlink($xlsxPath);
        @rmdir($directory);
    }

    public function test_sql_backup_failure_returns_to_backup_page_without_download(): void
    {
        $backup = $this->createMock(DatabaseBackupService::class);
        $backup->expects($this->once())
            ->method('createSqlBackup')
            ->willThrowException(new \RuntimeException('mariadb-dump tidak tersedia'));
        $this->app->instance(DatabaseBackupService::class, $backup);

        $this->actingAs($this->userWithRole('admin'))
            ->from(route('admin.backups.index'))
            ->get(route('admin.backups.sql'))
            ->assertRedirect(route('admin.backups.index'))
            ->assertSessionHas('backupError');
    }

    public function test_xlsx_backup_contains_metadata_every_table_and_archived_data(): void
    {
        $user = $this->userWithRole('wali_santri');
        $parent = ParentModel::create([
            'user_id' => $user->id,
            'type' => 'father',
            'qr_code_string' => 'backup-test-'.$user->id,
        ]);
        $event = KajianEvent::create([
            'academic_year_id' => AcademicYear::value('id'),
            'title' => 'Kajian backup test',
            'date' => today(),
            'time_start' => '08:00',
            'time_end' => '09:00',
            'status' => 'closed',
        ]);
        $attendance = Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $parent->id,
            'status' => 'izin',
            'method' => 'manual',
            'validation_status' => 'approved',
        ]);
        $attendance->delete();

        $backup = app(DatabaseBackupService::class)->createXlsxBackup();
        $workbook = IOFactory::load($backup['path']);

        $this->assertNotNull($workbook->getSheetByName('Metadata'));
        $this->assertNotNull($workbook->getSheetByName('attendances'));

        $attendanceSheet = $workbook->getSheetByName('attendances');
        $headers = $attendanceSheet->rangeToArray('A1:'.$attendanceSheet->getHighestColumn().'1', null, true, false)[0];
        $this->assertContains('deleted_at', $headers);
        $this->assertSame(2, $attendanceSheet->getHighestRow());

        $metadata = $workbook->getSheetByName('Metadata')->toArray(null, true, true, false);
        $metadataTables = array_column(array_slice($metadata, 6), 0);
        $this->assertContains('attendances', $metadataTables);

        $workbook->disconnectWorksheets();
        @unlink($backup['path']);
    }

    public function test_sql_command_uses_complete_mariadb_dump_options_without_password_argument(): void
    {
        $service = new DatabaseBackupService;
        $method = new \ReflectionMethod($service, 'buildSqlDumpCommand');
        $method->setAccessible(true);
        $command = $method->invoke($service, '/usr/bin/mariadb-dump', [
            'host' => 'database',
            'port' => '3306',
            'username' => 'backup-user',
            'password' => 'secret-password',
            'database' => 'kajian',
            'charset' => 'utf8mb4',
        ], '/tmp/backup.sql');

        $this->assertStringContainsString('--single-transaction', $command);
        $this->assertStringContainsString('--routines', $command);
        $this->assertStringContainsString('--triggers', $command);
        $this->assertStringContainsString('--events', $command);
        $this->assertStringContainsString('--databases', $command);
        $this->assertStringNotContainsString('secret-password', $command);
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', $role)->value('id'),
            'is_active' => true,
        ]);
    }
}
