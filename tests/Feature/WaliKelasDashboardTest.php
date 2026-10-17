<?php

namespace Tests\Feature;

use App\Livewire\WaliKelas\Dashboard;
use App\Livewire\WaliKelas\ReportIndex;
use App\Livewire\WaliKelas\StudentProfile;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\GuardianFollowUpCase;
use App\Models\GuardianFollowUpEntry;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\AttendanceFinalizationService;
use App\Services\GuardianAttendanceReportService;
use App\Services\WaliKelasReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class WaliKelasDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    private ClassRoom $class;

    private Student $student;

    private ParentModel $father;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 10, 18)->setTime(10, 0));
        $this->teacher = User::factory()->create(['name' => 'Ustaz Kelas A', 'role_id' => Role::where('name', 'wali_kelas')->value('id')]);
        $this->class = ClassRoom::create(['name' => 'Kelas A', 'teacher_id' => $this->teacher->id, 'is_active' => true]);
        $this->student = $this->student($this->class, 'Ahmad Kelas A');
        $this->father = $this->parent($this->student, 'father', 'Ayah Ahmad');
        $this->actingAs($this->teacher);
    }

    private function student(ClassRoom $class, string $name): Student
    {
        return Student::create(['name' => $name, 'nis' => 'NIS-'.str()->random(8), 'class_id' => $class->id, 'is_active' => true, 'student_status' => 'active']);
    }

    private function parent(Student $student, string $type, string $name): ParentModel
    {
        $parent = ParentModel::create(['type' => $type, 'user_id' => User::factory()->create(['name' => $name, 'phone' => '081234567890'])->id]);
        $parent->students()->attach($student->id, ['relationship' => 'biological', 'is_primary_contact' => true]);

        return $parent;
    }

    private function event(string $title = 'Kajian Oktober', int $daysAgo = 1): KajianEvent
    {
        $event = KajianEvent::create(['academic_year_id' => AcademicYear::active()->id, 'title' => $title, 'date' => today()->subDays($daysAgo), 'time_start' => '08:00', 'time_end' => '09:00', 'status' => 'open']);
        $event->targetClasses()->attach($this->class->id);

        return $event;
    }

    private function close(KajianEvent $event): void
    {
        app(AttendanceFinalizationService::class)->close($event);
    }

    private function attendance(KajianEvent $event, ParentModel $parent, string $status = 'hadir_fisik', string $validation = 'approved', ?Student $student = null): Attendance
    {
        return Attendance::create(['kajian_event_id' => $event->id, 'parent_id' => $parent->id, 'student_id' => ($student ?? $this->student)->id, 'status' => $status, 'validation_status' => $validation, 'method' => 'manual']);
    }

    private function values(string $status = 'in_progress', string $result = 'Sudah dihubungi oleh wali kelas.'): array
    {
        return ['status' => $status, 'contacted_at' => now()->subMinute()->format('Y-m-d H:i'), 'channel' => 'phone', 'result' => $result, 'next_action' => 'Hubungi kembali.', 'next_follow_up_at' => today()->addDay()->format('Y-m-d')];
    }

    public function test_dashboard_defaults_to_last_closed_event_and_shows_only_own_students(): void
    {
        $otherClass = ClassRoom::create(['name' => 'Kelas rahasia B']);
        $other = $this->student($otherClass, 'Santri rahasia B');
        $this->parent($other, 'mother', 'Ibu rahasia B');
        $closed = $this->event();
        $this->close($closed);
        $this->event('Kajian mendatang', -2);

        Livewire::test(Dashboard::class)->assertSet('kajianId', (string) $closed->id)
            ->assertSee('Ahmad Kelas A')->assertSee('Ayah Ahmad')->assertDontSee('Santri rahasia B')->assertDontSee('Ibu rahasia B');
        $this->get(route('wali-kelas.dashboard'))->assertOk()->assertDontSee('user-scalable=no', false)->assertDontSee('fonts.googleapis.com', false)->assertDontSee('id="kajian-pwa-install-middleware"', false);
    }

    public function test_counts_guardians_once_per_event_even_when_they_have_siblings(): void
    {
        $sibling = $this->student($this->class, 'Adik Ahmad');
        $this->father->students()->attach($sibling->id, ['relationship' => 'biological']);
        $mother = $this->parent($this->student, 'mother', 'Ibu Ahmad');
        $event = $this->event();
        $this->attendance($event, $this->father);
        $this->attendance($event, $mother, 'hadir_online');
        $this->close($event);
        $service = app(WaliKelasReportService::class);
        $data = $service->dataset();
        $summary = $service->summary($data['rows']);
        $this->assertSame(2, $summary['total']);
        $this->assertSame(2, $summary['present']);
        $this->assertEquals(100, $summary['rate']);
        $this->assertCount(3, $data['rows']);
    }

    public function test_pending_rejected_izin_and_open_events_are_never_automatic_alpha(): void
    {
        foreach (['pending', 'rejected', 'izin'] as $name) {
            $event = $this->event($name);
            $this->attendance($event, $this->father, $name === 'izin' ? 'izin' : 'hadir_online', $name === 'izin' ? 'approved' : $name);
            $this->close($event);
        }
        $this->event('Masih terbuka');
        $this->event('Masa depan', -2);
        $service = app(WaliKelasReportService::class);
        $data = $service->dataset();
        $this->assertCount(0, $data['attention']);
        $summary = $service->summary($data['rows']);
        $this->assertSame(0, $summary['present']);
        $this->assertSame(1, $summary['pending']);
        $this->assertSame(1, $summary['rejected']);
        $this->assertSame(1, $summary['izin']);
    }

    public function test_uncertain_legacy_roster_does_not_invent_absences(): void
    {
        $event = $this->event('Kajian lama tanpa snapshot', 60);
        $event->update(['status' => 'closed']);
        $data = app(WaliKelasReportService::class)->dataset();
        $this->assertSame('incomplete', $data['rows']->first()['status']);
        $this->assertCount(0, $data['attention']);
        $this->assertNull(app(WaliKelasReportService::class)->summary($data['rows'])['rate']);
    }

    public function test_snapshot_excludes_non_targeted_students_and_preserves_historical_class(): void
    {
        $event = $this->event('Riwayat kelas lama');
        $this->close($event);
        $newTeacher = User::factory()->create(['role_id' => $this->teacher->role_id]);
        $newClass = ClassRoom::create(['name' => 'Kelas baru', 'teacher_id' => $newTeacher->id]);
        $this->student->update(['class_id' => $newClass->id]);
        $this->get(route('wali-kelas.students.show', $this->student))->assertNotFound();
        $this->actingAs($newTeacher);
        $data = app(WaliKelasReportService::class)->dataset($this->student->id);
        $this->assertSame('Kelas A', $data['rows']->first()['class_name']);
        $this->assertSame('alpha', $data['rows']->first()['status']);
        $this->get(route('wali-kelas.students.show', $this->student))->assertOk()->assertSee('Ahmad Kelas A');
    }

    public function test_shared_parent_does_not_expose_sibling_in_another_class(): void
    {
        $outside = $this->student(ClassRoom::create(['name' => 'Kelas luar']), 'Nama saudara privat');
        $this->father->students()->attach($outside->id, ['relationship' => 'biological']);
        $event = $this->event();
        $this->attendance($event, $this->father, student: $outside);
        $this->close($event);
        Livewire::test(ReportIndex::class)->assertSee('Ayah Ahmad')->assertDontSee('Nama saudara privat')->assertDontSee('Kelas luar');
        $this->get(route('wali-kelas.students.show', $outside))->assertNotFound();
    }

    public function test_reading_dashboard_and_profile_creates_no_followup_or_attendance_records(): void
    {
        $this->close($this->event());
        $before = Attendance::count();
        $this->get(route('wali-kelas.dashboard'))->assertOk();
        $this->get(route('wali-kelas.students.show', $this->student))->assertOk();
        $this->get(route('wali-kelas.reports'))->assertOk();
        $this->assertDatabaseCount('guardian_follow_up_cases', 0);
        $this->assertDatabaseCount('guardian_follow_up_entries', 0);
        $this->assertDatabaseCount('attendances', $before);
    }

    public function test_one_absence_creates_virtual_case_and_old_cases_survive_filters(): void
    {
        $old = $this->event('Alfa lama', 45);
        $this->close($old);
        $new = $this->event('Kajian terbaru');
        $this->attendance($new, $this->father);
        $this->close($new);
        Livewire::test(Dashboard::class)->set('kajianId', (string) $new->id)->set('status', 'present')
            ->assertSee('Alfa lama')->assertSee('Belum ditindaklanjuti');
        $this->assertCount(1, app(WaliKelasReportService::class)->dataset()['attention']);
    }

    public function test_followup_is_append_only_preserves_presence_and_can_be_completed(): void
    {
        $event = $this->event();
        $attendance = $this->attendance($event, $this->father, 'alpha');
        $this->close($event);
        $before = $attendance->fresh()->getAttributes();
        $service = app(WaliKelasReportService::class);
        $first = $service->saveFollowUp($this->student->id, $this->father->id, $event->id, $this->values());
        $service->saveFollowUp($this->student->id, $this->father->id, $event->id, $this->values('completed', 'Komunikasi selesai dan jadwal dipahami.'));
        $this->assertDatabaseCount('guardian_follow_up_cases', 1);
        $this->assertDatabaseCount('guardian_follow_up_entries', 2);
        $this->assertSame('completed', $first->fresh()->status);
        $this->assertSame($before, $attendance->fresh()->getAttributes());
        $this->assertCount(0, $service->dataset()['attention']);
        Livewire::test(StudentProfile::class, ['student' => $this->student->id])->set('tab', 'followups')->assertSee('Komunikasi selesai dan jadwal dipahami.')->assertSee($this->teacher->name);
    }

    public function test_completing_case_requires_nonblank_result_and_handles_old_deadline(): void
    {
        $event = $this->event();
        $this->close($event);
        Livewire::test(StudentProfile::class, ['student' => $this->student->id])
            ->call('openFollowUp', $this->father->id, $event->id)->set('followStatus', 'completed')->set('result', '   ')
            ->call('saveFollowUp')->assertHasErrors(['result']);
        $this->assertDatabaseCount('guardian_follow_up_cases', 0);
        Livewire::test(StudentProfile::class, ['student' => $this->student->id])
            ->call('openFollowUp', $this->father->id, $event->id)->set('followStatus', 'completed')
            ->set('nextFollowUpAt', '2020-01-01')->set('result', 'Selesai dibicarakan bersama wali.')
            ->call('saveFollowUp')->assertHasNoErrors();
        $this->assertNull(GuardianFollowUpCase::first()->next_follow_up_at);
    }

    public function test_manipulated_parent_or_event_ids_cannot_create_case(): void
    {
        $outside = $this->student(ClassRoom::create(['name' => 'Kelas B']), 'Santri B');
        $parent = $this->parent($outside, 'father', 'Ayah B');
        $event = $this->event();
        $this->close($event);
        Livewire::test(StudentProfile::class, ['student' => $this->student->id])->call('openFollowUp', $parent->id, $event->id)->assertNotFound();
        $this->assertDatabaseCount('guardian_follow_up_cases', 0);
    }

    public function test_assignment_is_rechecked_after_component_mount_and_on_exports(): void
    {
        $event = $this->event();
        $this->close($event);
        $profile = Livewire::test(StudentProfile::class, ['student' => $this->student->id]);
        $report = Livewire::test(ReportIndex::class);
        $this->class->update(['teacher_id' => null]);
        $profile->call('openFollowUp', $this->father->id, $event->id)->assertNotFound();
        $report->call('exportExcel')->assertNotFound();
    }

    public function test_replacement_teacher_can_continue_case_without_changing_previous_author(): void
    {
        $event = $this->event();
        $this->close($event);
        $service = app(WaliKelasReportService::class);
        $service->saveFollowUp($this->student->id, $this->father->id, $event->id, $this->values());
        $replacement = User::factory()->create(['name' => 'Wali kelas pengganti', 'role_id' => $this->teacher->role_id]);
        $this->class->update(['teacher_id' => $replacement->id]);
        $this->actingAs($replacement);
        $service->saveFollowUp($this->student->id, $this->father->id, $event->id, $this->values('completed'));
        $this->assertEqualsCanonicalizing([$this->teacher->name, $replacement->name], GuardianFollowUpEntry::pluck('author_name')->all());
    }

    public function test_overdue_cases_are_sorted_before_older_non_overdue_cases(): void
    {
        $old = $this->event('Kasus lama', 30);
        $new = $this->event('Kasus lewat tenggat', 2);
        $this->close($old);
        $this->close($new);
        $values = $this->values();
        $values['next_follow_up_at'] = today()->subDay()->toDateString();
        app(WaliKelasReportService::class)->saveFollowUp($this->student->id, $this->father->id, $new->id, $values);
        $attention = app(WaliKelasReportService::class)->dataset()['attention'];
        $this->assertSame($new->id, $attention->first()['event_id']);
        $this->assertTrue($attention->first()['overdue']);
    }

    public function test_filters_reset_pagination_and_exports_use_the_same_filtered_rows(): void
    {
        $event = $this->event();
        $this->close($event);
        $component = Livewire::test(ReportIndex::class)->set('paginators.page', 3)->set('search', 'Ahmad');
        $component->assertSet('paginators.page', 1)->set('status', 'alpha')->call('exportExcel')->assertFileDownloaded();
        $component->call('exportPdf')->assertFileDownloaded();
        $component->set('status', 'pending')->assertSee('Belum ada catatan yang sesuai');
    }

    public function test_no_class_empty_class_and_other_roles_have_clear_states(): void
    {
        $this->class->update(['teacher_id' => null]);
        $this->get(route('wali-kelas.dashboard'))->assertOk()->assertSee('Belum ada kelas penugasan');
        $this->class->update(['teacher_id' => $this->teacher->id]);
        $this->student->update(['is_active' => false]);
        $this->get(route('wali-kelas.dashboard'))->assertOk()->assertSee('Belum ada santri aktif di kelas ini');
        $otherRole = Role::where('name', 'panitia')->value('id');
        $this->actingAs(User::factory()->create(['role_id' => $otherRole]))->get(route('wali-kelas.dashboard'))->assertForbidden();
    }

    public function test_query_count_is_bounded_when_the_roster_grows(): void
    {
        $this->close($this->event());
        DB::enableQueryLog();
        app(WaliKelasReportService::class)->dataset();
        $small = count(DB::getQueryLog());
        DB::disableQueryLog();
        for ($i = 0; $i < 15; $i++) {
            $this->student($this->class, 'Santri tambahan '.$i);
        }
        DB::flushQueryLog();
        DB::enableQueryLog();
        app(WaliKelasReportService::class)->dataset();
        $large = count(DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertLessThanOrEqual($small + 2, $large, 'Queries must not grow once per student or per event.');
    }

    public function test_profile_includes_previous_academic_year_without_opening_other_students(): void
    {
        $oldYear = AcademicYear::create(['name' => '2023/2024', 'start_date' => '2023-07-01', 'end_date' => '2024-06-30', 'is_active' => false]);
        $old = $this->event('Kajian tahun sebelumnya', 800);
        $old->update(['academic_year_id' => $oldYear->id]);
        $this->close($old);
        $this->close($this->event('Kajian tahun aktif'));
        Livewire::test(StudentProfile::class, ['student' => $this->student->id])->set('tab', 'history')
            ->assertSee('Kajian tahun sebelumnya')->assertSee('Kajian tahun aktif')
            ->set('academicYearId', (string) $oldYear->id)->assertSee('Kajian tahun sebelumnya')->assertDontSeeHtml('<h3>Kajian tahun aktif</h3>');
    }

    public function test_future_and_running_events_do_not_reduce_default_closed_event_rate(): void
    {
        $this->event('Belum dimulai', -1);
        $component = Livewire::test(Dashboard::class);
        $this->assertNull($component->viewData('summary')['rate']);
        $this->assertSame(0, $component->viewData('summary')['total']);
    }

    public function test_known_legacy_enrollment_and_family_can_support_an_absence(): void
    {
        $event = $this->event('Riwayat dengan enrollment', 20);
        StudentEnrollment::create(['student_id' => $this->student->id, 'academic_year_id' => $event->academic_year_id, 'class_id' => $this->class->id, 'class_name' => $this->class->name, 'status' => 'enrolled', 'started_at' => today()->subYear()]);
        DB::table('parent_student')->where('parent_id', $this->father->id)->update(['created_at' => now()->subYear()]);
        $event->update(['status' => 'closed']);
        $this->assertSame('alpha', app(WaliKelasReportService::class)->dataset()['rows']->first()['status']);
    }

    public function test_student_added_after_frozen_roster_is_not_invented_as_an_absentee(): void
    {
        $event = $this->event();
        $this->close($event);
        $later = $this->student($this->class, 'Santri baru setelah kajian');
        $this->parent($later, 'mother', 'Ibu santri baru');
        $data = app(WaliKelasReportService::class)->dataset($later->id);
        $this->assertCount(0, $data['rows']);
        $this->assertCount(0, $data['attention']);
    }

    public function test_completing_one_absence_does_not_close_another_event_case(): void
    {
        $first = $this->event('Kasus pertama', 15);
        $second = $this->event('Kasus kedua');
        $this->close($first);
        $this->close($second);
        app(WaliKelasReportService::class)->saveFollowUp($this->student->id, $this->father->id, $first->id, $this->values('completed'));
        $remaining = app(WaliKelasReportService::class)->dataset()['attention'];
        $this->assertCount(1, $remaining);
        $this->assertSame($second->id, $remaining->first()['event_id']);
    }

    public function test_directly_tampered_form_cannot_save_another_guardians_case(): void
    {
        $event = $this->event();
        $this->close($event);
        $other = $this->parent($this->student(ClassRoom::create(['name' => 'Kelas luar']), 'Santri luar'), 'mother', 'Wali luar');
        Livewire::test(StudentProfile::class, ['student' => $this->student->id])
            ->set('followParentId', $other->id)->set('followEventId', $event->id)->set('contactedAt', now()->subMinute()->format('Y-m-d\TH:i'))
            ->set('result', 'Percobaan catatan')->call('saveFollowUp')->assertNotFound();
        $this->assertDatabaseCount('guardian_follow_up_cases', 0);
    }

    public function test_service_reading_and_followups_do_not_change_other_roles_reports(): void
    {
        $event = $this->event();
        $this->close($event);
        $before = app(GuardianAttendanceReportService::class)->rowsForEvent($event->fresh())->pluck('derived_status', 'guardian_id');
        app(WaliKelasReportService::class)->saveFollowUp($this->student->id, $this->father->id, $event->id, $this->values('completed'));
        $after = app(GuardianAttendanceReportService::class)->rowsForEvent($event->fresh())->pluck('derived_status', 'guardian_id');
        $this->assertSame($before->all(), $after->all());
    }

    public function test_profile_student_id_cannot_be_changed_from_the_browser(): void
    {
        $outside = $this->student(ClassRoom::create(['name' => 'Kelas lain']), 'Santri privat');
        $this->expectException(CannotUpdateLockedPropertyException::class);
        Livewire::test(StudentProfile::class, ['student' => $this->student->id])->set('studentId', $outside->id);
    }

    public function test_csv_and_pdf_contain_only_the_filtered_authorized_rows(): void
    {
        $mother = $this->parent($this->student, 'mother', 'Ibu Ahmad');
        $event = $this->event();
        $this->attendance($event, $mother, 'izin');
        $this->close($event);
        $component = Livewire::test(ReportIndex::class)->set('status', 'alpha')->call('exportExcel')->assertFileDownloaded();
        $csv = base64_decode($component->effects['download']['content']);
        $this->assertStringContainsString('Ayah Ahmad', $csv);
        $this->assertStringNotContainsString('Ibu Ahmad', $csv);
        $pdf = view('reports.wali-kelas-pdf', ['rows' => app(WaliKelasReportService::class)->filter(app(WaliKelasReportService::class)->dataset()['rows'], status: 'alpha'), 'class' => $this->class])->render();
        $this->assertStringContainsString('Ayah Ahmad', $pdf);
        $this->assertStringNotContainsString('Ibu Ahmad', $pdf);
    }

    public function test_class_report_is_ordered_by_kajian_date_before_student_name(): void
    {
        $second = $this->student($this->class, 'Zaid');
        $this->parent($second, 'father', 'Ayah Zaid');
        $old = $this->event('Pertemuan lama', 20);
        $latest = $this->event('Pertemuan terbaru');
        $this->close($old);
        $this->close($latest);
        $events = app(WaliKelasReportService::class)->dataset()['rows']->pluck('event_id')->all();
        $this->assertSame([$latest->id, $latest->id, $old->id, $old->id], $events);
    }
}
