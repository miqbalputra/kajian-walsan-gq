<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ParentIndex;
use App\Livewire\Admin\TeacherArchiveIndex;
use App\Livewire\Admin\TeacherAttendanceIndex;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\TeacherArchive;
use App\Models\User;
use App\Services\TeacherArchiveService;
use App\Services\TeacherAttendanceReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class TeacherArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['data-induk.enabled' => false, 'data-induk.read_only' => false, 'services.telegram_review.enabled' => false]);
    }

    public function test_archiving_preserves_identity_and_attendance_but_removes_teacher_from_active_views(): void
    {
        $admin = $this->user('admin');
        $teacher = $this->teacher();
        $activeTeacher = $this->teacher();
        $event = $this->event();
        $attendance = $this->attendance($event, $teacher);
        $class = ClassRoom::create(['name' => 'Kelas Guru', 'level' => '1', 'is_active' => true, 'teacher_id' => $teacher->user_id]);

        $archive = $this->archive($teacher, $admin);

        $this->assertTrue($teacher->fresh()->isTeacherArchived());
        $this->assertFalse($teacher->fresh()->isTeacher());
        $this->assertFalse($teacher->user->fresh()->is_active);
        $this->assertNull($class->fresh()->teacher_id);
        $this->assertSame($attendance->id, $archive->attendance_snapshot[0]['id']);
        $this->assertSame($teacher->user->name, $archive->teacher_name);
        $this->assertSame($admin->id, $archive->archived_by);
        $this->assertDatabaseHas('attendances', ['id' => $attendance->id, 'deleted_at' => null]);
        $this->assertSame([$activeTeacher->id], app(TeacherAttendanceReportService::class)->teachers()->modelKeys());
        $this->assertCount(0, app(TeacherAttendanceReportService::class)->attendanceRows(null, null));

        Livewire::actingAs($admin)->test(ParentIndex::class, ['typeFilter' => 'teacher'])
            ->assertDontSee($teacher->user->name)->assertSee('Arsip Guru');
        Livewire::test(TeacherAttendanceIndex::class)
            ->assertDontSee($teacher->user->name);
        Livewire::test(Dashboard::class)
            ->assertViewHas('teacherAttendanceTrendData', fn ($data) => $data['hadirFisik'] === [0] && $data['alpha'] === [1]);
        $this->actingAs($admin)->get(route('admin.teacher-archives.index'))
            ->assertOk()->assertSee($teacher->user->name)->assertSee('Resign untuk keperluan keluarga');
    }

    public function test_archived_dual_role_teacher_remains_a_guardian_and_archive_keeps_an_immutable_snapshot(): void
    {
        $admin = $this->user('admin');
        $teacher = $this->teacher('mother', role: 'wali_kelas');
        $event = $this->event();
        $this->attendance($event, $teacher);
        $originalName = $teacher->user->name;
        $archive = $this->archive($teacher, $admin);

        $this->assertTrue($teacher->user->fresh()->is_active);
        $this->assertTrue($teacher->user->fresh()->isWaliSantri());
        $this->assertTrue($teacher->fresh()->hasActiveChildren());
        $this->assertFalse($teacher->fresh()->isWaliTeacher());
        $this->assertSame(0, ParentModel::activeTeachers()->count());

        Livewire::actingAs($admin)->test(Dashboard::class)
            ->assertViewHas('attendanceTrendData', fn ($data) => $data['hadirFisik'] === [1])
            ->assertViewHas('teacherAttendanceTrendData', fn ($data) => $data['hadirFisik'] === [0] && $data['alpha'] === [0]);
        Livewire::test(ParentIndex::class)
            ->call('openEditModal', $teacher->id)->set('name', 'Nama Orang Tua Terbaru')->call('save')->assertHasNoErrors();
        $this->assertSame('Nama Orang Tua Terbaru', $teacher->user->fresh()->name);
        $this->assertSame($originalName, $archive->fresh()->teacher_name);
        $this->assertSame('mother', $teacher->fresh()->type);
        $this->assertTrue($teacher->fresh()->is_teacher);

        // New guardian transactions cannot modify the archived teacher history.
        $newEvent = $this->event(daysAgo: 0);
        $this->attendance($newEvent, $teacher);
        $this->assertCount(1, $archive->fresh()->attendance_snapshot);
        $this->actingAs($teacher->user->fresh())->get(route('wali-santri.dashboard'))->assertOk();
        $this->get(route('wali-kelas.dashboard'))->assertForbidden();
    }

    public function test_archived_teacher_records_cannot_be_edited_deleted_or_reactivated(): void
    {
        $admin = $this->user('admin');
        $teacher = $this->teacher();
        $attendance = $this->attendance($this->event(), $teacher);
        $archive = $this->archive($teacher, $admin);

        foreach ([
            fn () => $archive->fresh()->update(['reason' => 'Diubah']),
            fn () => $archive->fresh()->delete(),
            fn () => $teacher->fresh()->update(['occupation' => 'Diubah']),
            fn () => $teacher->update(['occupation' => 'Diubah dari halaman lama']),
            fn () => $teacher->fresh()->delete(),
            fn () => $teacher->user->fresh()->update(['is_active' => true]),
            fn () => $attendance->fresh()->update(['notes' => 'Diubah']),
            fn () => $attendance->fresh()->delete(),
        ] as $mutation) {
            $this->assertReadOnly($mutation);
        }
        $this->assertDatabaseCount('teacher_archives', 1);
        $this->assertSame('Resign untuk keperluan keluarga', $archive->fresh()->reason);
        $this->assertFalse($teacher->user->fresh()->is_active);
        $this->actingAs($teacher->user->fresh())->get(route('wali-santri.dashboard'))->assertForbidden();
    }

    public function test_admin_can_archive_from_teacher_page_with_validation_and_duplicate_submission_is_safe(): void
    {
        $admin = $this->user('admin');
        $teacher = $this->teacher();
        // Teacher employment is local Kajian data even with central identities locked.
        config(['data-induk.enabled' => true, 'data-induk.read_only' => true]);

        Livewire::actingAs($admin)->test(ParentIndex::class, ['typeFilter' => 'teacher'])
            ->call('openTeacherArchiveModal', $teacher->id)
            ->assertSet('showTeacherArchiveModal', true)
            ->call('archiveTeacher')->assertHasErrors(['teacherArchiveReason'])
            ->set('teacherArchiveReason', 'Resign untuk keperluan keluarga')
            ->set('teacherDepartedOn', today()->addDay()->toDateString())
            ->call('archiveTeacher')->assertHasErrors(['teacherDepartedOn'])
            ->set('teacherDepartedOn', today()->toDateString())
            ->call('archiveTeacher')->assertHasNoErrors()->assertSet('showTeacherArchiveModal', false);

        $archive = $this->archive($teacher, $admin);
        $this->assertDatabaseCount('teacher_archives', 1);
        $this->assertSame($teacher->id, $archive->parent_id);
    }

    public function test_archive_page_and_actions_are_admin_only_and_details_have_no_edit_controls(): void
    {
        $admin = $this->user('admin');
        $teacher = $this->teacher();
        $this->attendance($this->event(), $teacher);
        $archive = $this->archive($teacher, $admin);

        foreach (['kepsek', 'panitia', 'guru', 'wali_santri'] as $role) {
            $this->actingAs($this->user($role))->get(route('admin.teacher-archives.index'))->assertForbidden();
        }
        Livewire::actingAs($this->user('panitia'))->test(ParentIndex::class)
            ->call('openTeacherArchiveModal', $teacher->id)->assertForbidden();
        Livewire::actingAs($admin)->test(TeacherArchiveIndex::class)
            ->call('showDetail', $archive->id)->assertSee('Hanya Baca')->assertSee('Kajian Arsip Guru')
            ->assertDontSee('wire:click="save', false)->assertDontSee('wire:click="delete', false)
            ->call('closeDetail')->assertSet('selectedArchiveId', null)
            ->set('search', 'Nama tidak ditemukan')->assertDontSee($archive->teacher_name);
        Livewire::test(ParentIndex::class, ['typeFilter' => 'teacher'])
            ->call('openEditModal', $teacher->id)->assertForbidden();
    }

    private function assertReadOnly(callable $mutation): void
    {
        try {
            $mutation();
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('archive', $exception->errors());

            return;
        }
        $this->fail('A mutation of archived teacher data was allowed.');
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role_id' => Role::where('name', $role)->value('id'), 'is_active' => true]);
    }

    private function teacher(string $type = 'teacher', string $role = 'guru'): ParentModel
    {
        $user = $this->user($role);
        $sqliteTeacher = $type === 'teacher' && DB::getDriverName() === 'sqlite';
        if ($sqliteTeacher) {
            DB::statement('PRAGMA ignore_check_constraints = ON');
        }
        try {
            $parent = ParentModel::create(['user_id' => $user->id, 'type' => $type, 'is_teacher' => true, 'qr_code_string' => 'ARCHIVE-'.$user->id]);
        } finally {
            if ($sqliteTeacher) {
                DB::statement('PRAGMA ignore_check_constraints = OFF');
            }
        }
        if ($parent->isGuardian()) {
            $student = Student::create(['nis' => 'ARCHIVE-'.$user->id, 'name' => 'Anak Guru', 'is_active' => true, 'student_status' => 'active']);
            $parent->students()->attach($student->id);
        }

        return $parent;
    }

    private function event(int $daysAgo = 1): KajianEvent
    {
        return KajianEvent::create(['academic_year_id' => AcademicYear::active()->id, 'title' => 'Kajian Arsip Guru', 'date' => today()->subDays($daysAgo), 'time_start' => '08:00', 'time_end' => '10:00', 'status' => 'closed', 'category' => 'kajian']);
    }

    private function attendance(KajianEvent $event, ParentModel $teacher): Attendance
    {
        return Attendance::create(['parent_id' => $teacher->id, 'kajian_event_id' => $event->id, 'status' => Attendance::STATUS_HADIR_FISIK, 'method' => Attendance::METHOD_MANUAL, 'validation_status' => Attendance::VALIDATION_APPROVED]);
    }

    private function archive(ParentModel $teacher, User $admin): TeacherArchive
    {
        return app(TeacherArchiveService::class)->archive($teacher, today()->toDateString(), 'Resign untuk keperluan keluarga', $admin);
    }
}
