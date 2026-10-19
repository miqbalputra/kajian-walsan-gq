<?php

namespace Tests\Feature;

use App\Livewire\Admin\ClassIndex;
use App\Livewire\Admin\ParentIndex;
use App\Livewire\WaliSantri\Dashboard;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceFinalizationService;
use App\Services\ClassTeacherAssignmentService;
use App\Services\TeacherArchiveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class CombinedTeacherDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['data-induk.enabled' => false, 'data-induk.read_only' => false, 'services.telegram_review.enabled' => false]);
    }

    public function test_admin_can_assign_a_teacher_with_the_checkbox_without_changing_the_account_role(): void
    {
        $admin = $this->user('admin');
        $teacher = $this->profile('teacher', role: 'guru');
        $class = $this->schoolClass('Kelas Binaan');

        Livewire::actingAs($admin)->test(ParentIndex::class, ['typeFilter' => 'teacher'])
            ->call('openEditModal', $teacher->id)
            ->assertSee('Juga sebagai Wali Kelas')->assertSet('isClassTeacher', false)
            ->set('isClassTeacher', true)->call('save')->assertHasErrors('teacherClassId')
            ->set('teacherClassId', (string) $class->id)->call('save')->assertHasNoErrors();

        $this->assertSame($teacher->user_id, $class->fresh()->teacher_id);
        $this->assertTrue($teacher->user->fresh()->hasRole('guru'));
        $this->assertTrue($teacher->user->fresh()->isWaliKelas());
        $this->actingAs($teacher->user->fresh())->get(route('dashboard'))->assertRedirect(route('wali-santri.dashboard'));
        $this->get(route('wali-santri.dashboard'))->assertOk()->assertSee('Presensi Saya')->assertSee('Kelas Saya');
    }

    public function test_admin_can_create_one_account_with_teacher_parent_and_class_teacher_roles(): void
    {
        $admin = $this->user('admin');
        $assignedClass = $this->schoolClass('Kelas Binaan Baru');
        $childClass = $this->schoolClass('Kelas Anak Sendiri');
        $child = $this->student($childClass, 'Anak Akun Gabungan');

        Livewire::actingAs($admin)->test(ParentIndex::class, ['typeFilter' => 'teacher'])
            ->call('openCreateModal')->set('name', 'Ibu Guru Tiga Peran')
            ->set('username', 'guru-tiga-peran')->set('email', 'guru.tiga.peran@gmail.com')
            ->set('password', 'KataSandiGabungan123!')->set('type', 'mother')
            ->set('selectedChildren', [$child->id])->set('isClassTeacher', true)
            ->set('teacherClassId', (string) $assignedClass->id)
            ->call('save')->assertHasNoErrors()->assertSet('showModal', false);

        $user = User::where('username', 'guru-tiga-peran')->firstOrFail();
        $parent = $user->parentProfile;
        $this->assertSame('mother', $parent->type);
        $this->assertTrue($parent->isTeacher());
        $this->assertTrue($parent->students->contains('id', $child->id));
        $this->assertSame($user->id, $assignedClass->fresh()->teacher_id);
        $this->assertTrue($user->hasRole('wali_santri'));
        $this->assertTrue($user->isGuru());
        $this->assertTrue($user->isWaliSantri());
        $this->assertTrue($user->isWaliKelas());
        $this->assertSame(1, ParentModel::where('user_id', $user->id)->count());
    }

    public function test_triple_role_dashboard_keeps_personal_attendance_and_only_shows_assigned_class_data(): void
    {
        $class = $this->schoolClass('Kelas yang Dibina');
        $otherClass = $this->schoolClass('Kelas Anak Guru');
        $teacher = $this->profile('mother', $otherClass, role: 'wali_santri');
        $guardian = $this->profile('father', $class, isTeacher: false, name: 'Wali Kelas Binaan');
        $this->profile('father', $otherClass, isTeacher: false, name: 'Wali Kelas Lain Rahasia');
        $otherStudent = Student::where('class_id', $otherClass->id)->firstOrFail();
        $event = $this->event();
        $this->attendance($event, $teacher);
        $this->attendance($event, $guardian);
        app(AttendanceFinalizationService::class)->close($event);
        app(ClassTeacherAssignmentService::class)->assign($teacher->user, $class->id);

        $this->actingAs($teacher->user->fresh())->get(route('dashboard'))->assertRedirect(route('wali-santri.dashboard'));
        Livewire::test(Dashboard::class)->assertSee('Guru')->assertSee('Wali Kelas')->assertSee('Orang Tua')
            ->assertSee('Riwayat Kehadiran')->assertSee($otherClass->name)
            ->call('selectDashboardSection', 'kelas')->assertSet('dashboardSection', 'kelas')
            ->assertSee('Wali Kelas Binaan')->assertDontSee('Wali Kelas Lain Rahasia')
            ->call('selectDashboardSection', 'presensi')->assertSee('Riwayat Kehadiran');
        $this->get(route('wali-kelas.dashboard'))->assertOk();
        $this->get(route('wali-kelas.students.show', $otherStudent->id))->assertNotFound();
        $this->assertSame(1, Attendance::where('parent_id', $teacher->id)->where('kajian_event_id', $event->id)->count());
    }

    public function test_regular_teacher_and_guardian_cannot_open_an_unassigned_class_panel(): void
    {
        foreach ([['teacher', true, 'guru'], ['father', false, 'wali_santri']] as [$type, $isTeacher, $role]) {
            $profile = $this->profile($type, isTeacher: $isTeacher, role: $role);
            Livewire::actingAs($profile->user)->test(Dashboard::class)
                ->assertDontSee('Kelas Saya')
                ->set('dashboardSection', 'kelas')->assertSet('dashboardSection', 'presensi')
                ->call('selectDashboardSection', 'kelas')->assertForbidden();
            $this->get(route('wali-kelas.dashboard'))->assertForbidden();
        }
    }

    public function test_unchecking_class_teacher_revokes_class_access_while_preserving_teacher_access(): void
    {
        $admin = $this->user('admin');
        $teacher = $this->profile('teacher', role: 'guru');
        $class = $this->schoolClass('Kelas Sebelum Dilepas');
        app(ClassTeacherAssignmentService::class)->assign($teacher->user, $class->id);

        Livewire::actingAs($admin)->test(ParentIndex::class, ['typeFilter' => 'teacher'])
            ->call('openEditModal', $teacher->id)->assertSet('isClassTeacher', true)
            ->set('isClassTeacher', false)->call('save')->assertHasNoErrors();

        $this->assertNull($class->fresh()->teacher_id);
        $this->assertTrue($teacher->fresh()->isTeacher());
        $this->actingAs($teacher->user->fresh())->get(route('wali-kelas.dashboard'))->assertForbidden();
        $this->get(route('wali-santri.dashboard'))->assertOk()->assertDontSee('Kelas Saya');
    }

    public function test_occupied_class_assignment_rolls_back_profile_changes(): void
    {
        $admin = $this->user('admin');
        $teacher = $this->profile('teacher', role: 'guru');
        $otherTeacher = $this->profile('teacher', role: 'guru');
        $class = $this->schoolClass('Kelas Sudah Ada Wali');
        app(ClassTeacherAssignmentService::class)->assign($otherTeacher->user, $class->id);
        $originalName = $teacher->user->name;

        Livewire::actingAs($admin)->test(ParentIndex::class, ['typeFilter' => 'teacher'])
            ->call('openEditModal', $teacher->id)->set('name', 'Nama yang Harus Dibatalkan')
            ->set('isClassTeacher', true)->set('teacherClassId', (string) $class->id)
            ->call('save')->assertHasErrors('teacherClassId');

        $this->assertSame($originalName, $teacher->user->fresh()->name);
        $this->assertSame($otherTeacher->user_id, $class->fresh()->teacher_id);
    }

    public function test_class_management_accepts_teacher_and_parent_teacher_accounts_but_rejects_regular_guardians(): void
    {
        $admin = $this->user('admin');
        $teacher = $this->profile('mother', role: 'wali_santri');
        $guardian = $this->profile('father', isTeacher: false);
        $class = $this->schoolClass('Kelas dari Menu Kelas');

        Livewire::actingAs($admin)->test(ClassIndex::class)
            ->call('openEditModal', $class->id)->assertSee($teacher->user->name)
            ->set('teacher_id', $guardian->user_id)->call('save')->assertHasErrors('teacher_id')
            ->set('teacher_id', $teacher->user_id)->call('save')->assertHasNoErrors();
        $this->assertSame($teacher->user_id, $class->fresh()->teacher_id);
        $this->assertTrue($teacher->user->fresh()->hasRole('wali_santri'));
    }

    public function test_archiving_a_triple_role_teacher_removes_class_panel_and_keeps_parent_access(): void
    {
        $admin = $this->user('admin');
        $class = $this->schoolClass('Kelas Guru Resign');
        $teacher = $this->profile('mother', $class, role: 'wali_santri');
        app(ClassTeacherAssignmentService::class)->assign($teacher->user, $class->id);
        app(TeacherArchiveService::class)->archive($teacher, today()->toDateString(), 'Resign', $admin);

        $this->assertNull($class->fresh()->teacher_id);
        // A stale external assignment must not restore archived teacher access.
        DB::table('classes')->where('id', $class->id)->update(['teacher_id' => $teacher->user_id]);
        $this->actingAs($teacher->user->fresh())->get(route('wali-santri.dashboard'))->assertOk()->assertSee('Orang Tua')->assertDontSee('Kelas Saya');
        $this->get(route('wali-kelas.dashboard'))->assertForbidden();
        $this->assertTrue($teacher->user->fresh()->is_active);
        $this->assertFalse($teacher->fresh()->isTeacher());
    }

    public function test_legacy_class_teacher_role_can_use_personal_dashboard_and_the_existing_class_dashboard(): void
    {
        $teacher = $this->profile('teacher', role: 'wali_kelas');
        $class = $this->schoolClass('Kelas Akun Wali Lama');
        app(ClassTeacherAssignmentService::class)->assign($teacher->user, $class->id);

        $this->actingAs($teacher->user)->get(route('dashboard'))->assertRedirect(route('wali-santri.dashboard'));
        $this->get(route('wali-santri.dashboard'))->assertOk()->assertSee('Presensi Saya')->assertSee('Kelas Saya');
        $this->get(route('wali-kelas.dashboard'))->assertOk();
    }

    public function test_teacher_role_account_with_a_parent_profile_keeps_all_three_capabilities(): void
    {
        $class = $this->schoolClass('Kelas Tiga Peran Guru');
        $teacher = $this->profile('father', $class, role: 'guru');
        app(ClassTeacherAssignmentService::class)->assign($teacher->user, $class->id);
        $user = $teacher->user->fresh();

        $this->assertTrue($user->hasRole('guru'));
        $this->assertTrue($user->isGuru());
        $this->assertTrue($user->isWaliSantri());
        $this->assertTrue($user->isWaliKelas());
        $this->actingAs($user)->get(route('wali-santri.dashboard'))->assertOk()->assertSee('Orang Tua');
        $this->get(route('wali-kelas.dashboard'))->assertOk();
    }

    public function test_legacy_profiles_are_marked_as_teachers_without_changing_their_parent_type_or_archives(): void
    {
        $teacher = $this->profile('father', isTeacher: false, role: 'wali_kelas');
        $archived = $this->profile('mother', isTeacher: false, role: 'wali_kelas');
        DB::table('parents')->where('id', $archived->id)->update(['teacher_archived_at' => now()]);

        (require database_path('migrations/2026_10_19_000002_mark_legacy_class_teacher_profiles.php'))->up();

        $this->assertTrue($teacher->fresh()->isTeacher());
        $this->assertSame('father', $teacher->fresh()->type);
        $this->assertFalse($archived->fresh()->is_teacher);
        $this->assertTrue($archived->fresh()->isTeacherArchived());
    }

    public function test_first_visit_of_legacy_teacher_without_profile_creates_one_personal_profile_only(): void
    {
        $user = $this->user('wali_kelas');
        // The existing teacher enum migration alters MySQL only.
        $sqlite = DB::getDriverName() === 'sqlite';
        if ($sqlite) {
            DB::statement('PRAGMA ignore_check_constraints = ON');
        }
        try {
            $this->actingAs($user)->get(route('wali-santri.dashboard'))->assertOk()->assertSee('Guru');
            $this->get(route('wali-santri.dashboard'))->assertOk();
        } finally {
            if ($sqlite) {
                DB::statement('PRAGMA ignore_check_constraints = OFF');
            }
        }
        $this->assertSame(1, ParentModel::where('user_id', $user->id)->count());
        $this->assertTrue($user->fresh()->parentProfile->isTeacher());
    }

    private function user(string $role, ?string $name = null): User
    {
        return User::factory()->create(array_filter(['role_id' => Role::where('name', $role)->value('id'), 'is_active' => true, 'name' => $name], fn ($value) => $value !== null));
    }

    private function schoolClass(string $name): ClassRoom
    {
        return ClassRoom::create(['name' => $name, 'level' => '1', 'is_active' => true]);
    }

    private function student(ClassRoom $class, string $name): Student
    {
        return Student::create(['nis' => 'COMBINED-'.str()->random(10), 'name' => $name, 'class_id' => $class->id, 'is_active' => true, 'student_status' => 'active']);
    }

    private function profile(string $type, ?ClassRoom $class = null, bool $isTeacher = true, string $role = 'wali_santri', ?string $name = null): ParentModel
    {
        $user = $this->user($role, $name);
        $sqliteTeacher = $type === 'teacher' && DB::getDriverName() === 'sqlite';
        if ($sqliteTeacher) {
            DB::statement('PRAGMA ignore_check_constraints = ON');
        }
        try {
            $parent = ParentModel::create(['user_id' => $user->id, 'type' => $type, 'is_teacher' => $isTeacher, 'qr_code_string' => 'COMBINED-'.$user->id]);
        } finally {
            if ($sqliteTeacher) {
                DB::statement('PRAGMA ignore_check_constraints = OFF');
            }
        }
        if ($class && $parent->isGuardian()) {
            $parent->students()->attach($this->student($class, 'Santri '.$user->name)->id);
        }

        return $parent;
    }

    private function event(): KajianEvent
    {
        return KajianEvent::create(['academic_year_id' => AcademicYear::active()->id, 'title' => 'Kajian Dashboard Gabungan', 'date' => today(), 'time_start' => '08:00', 'time_end' => '10:00', 'status' => 'open', 'category' => 'kajian']);
    }

    private function attendance(KajianEvent $event, ParentModel $parent): Attendance
    {
        return Attendance::create(['parent_id' => $parent->id, 'kajian_event_id' => $event->id, 'status' => Attendance::STATUS_HADIR_FISIK, 'method' => Attendance::METHOD_MANUAL, 'validation_status' => Attendance::VALIDATION_APPROVED]);
    }
}
