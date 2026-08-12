<?php

namespace App\Livewire\Admin;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\PromotionBatch;
use App\Services\PromotionService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PromotionWizard extends Component
{
    public int $step = 1;

    public $sourceAcademicYearId = '';

    public $targetAcademicYearId = '';

    public string $targetYearName = '';

    public string $targetStartDate = '';

    public string $targetEndDate = '';

    public array $classMapping = [];

    public array $studentDecisions = [];

    public array $previewRows = [];

    public array $summary = [];

    public array $warnings = [];

    public function mount(): void
    {
        $source = AcademicYear::active() ?? AcademicYear::orderByDesc('name')->first();
        $this->sourceAcademicYearId = $source?->id ?? '';
        $this->setDefaultTargetYear($source);
    }

    public function prepareMapping(PromotionService $promotionService): void
    {
        $this->validate([
            'sourceAcademicYearId' => ['required', 'exists:academic_years,id'],
            'targetYearName' => ['required', 'string', 'max:20', 'regex:/^\d{4}\/\d{4}$/'],
            'targetStartDate' => ['required', 'date'],
            'targetEndDate' => ['required', 'date', 'after:targetStartDate'],
        ]);

        $source = AcademicYear::findOrFail($this->sourceAcademicYearId);
        if ($this->targetAcademicYearId && (int) $this->targetAcademicYearId === (int) $source->id) {
            $this->addError('targetAcademicYearId', 'Tahun tujuan harus berbeda dari tahun sumber.');

            return;
        }

        $sourceClasses = ClassRoom::orderBy('level')->orderBy('name')->get();
        $targetClasses = ClassRoom::where('is_active', true)->orderBy('level')->orderBy('name')->get();
        $this->classMapping = $promotionService->suggestClassMapping($sourceClasses, $targetClasses);
        $this->step = 2;
    }

    public function preparePreview(PromotionService $promotionService): void
    {
        $this->validateMapping();

        $preview = $promotionService->preview((int) $this->sourceAcademicYearId, $this->normalizedMapping(), $this->studentDecisions);
        $this->previewRows = $preview['rows'];
        $this->summary = $preview['summary'];
        $this->warnings = $preview['warnings'];

        if (! $this->studentDecisions) {
            $this->studentDecisions = collect($this->previewRows)
                ->mapWithKeys(fn (array $row) => [
                    $row['student_id'] => [
                        'action' => $row['action'],
                        'target_class_id' => $row['target_class_id'] ?? '',
                    ],
                ])->all();
        }

        $this->step = 3;
    }

    public function refreshPreview(PromotionService $promotionService): void
    {
        $this->studentDecisions = collect($this->studentDecisions)->map(function ($decision) {
            return [
                'action' => $decision['action'] ?? 'defer',
                'target_class_id' => $decision['target_class_id'] ?? '',
            ];
        })->all();

        $preview = $promotionService->preview((int) $this->sourceAcademicYearId, $this->normalizedMapping(), $this->studentDecisions);
        $this->previewRows = $preview['rows'];
        $this->summary = $preview['summary'];
        $this->warnings = $preview['warnings'];
    }

    public function applyPromotion(PromotionService $promotionService)
    {
        $this->validateMapping();

        try {
            $source = AcademicYear::findOrFail($this->sourceAcademicYearId);
            $target = $this->targetAcademicYearId
                ? AcademicYear::findOrFail($this->targetAcademicYearId)
                : AcademicYear::firstOrCreate(
                    ['name' => $this->targetYearName],
                    [
                        'start_date' => $this->targetStartDate,
                        'end_date' => $this->targetEndDate,
                        'is_active' => false,
                    ]
                );

            $batch = $promotionService->apply(
                $source,
                $target,
                $this->normalizedMapping(),
                $this->studentDecisions,
                auth()->id()
            );

            session()->flash('message', "Kenaikan berhasil diterapkan untuk {$batch->changes()->count()} siswa. Tahun ajaran {$target->name} sekarang aktif.");

            return redirect()->route('admin.promotion.index');
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        }
    }

    public function rollbackBatch(int $batchId, PromotionService $promotionService): void
    {
        try {
            $batch = PromotionBatch::findOrFail($batchId);
            $promotionService->rollback($batch);
            session()->flash('message', 'Batch kenaikan berhasil di-rollback. Tahun ajaran sumber kembali aktif.');
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        }
    }

    public function backToMapping(): void
    {
        $this->step = 2;
    }

    public function resetWizard(): void
    {
        $this->step = 1;
        $this->classMapping = [];
        $this->studentDecisions = [];
        $this->previewRows = [];
        $this->summary = [];
        $this->warnings = [];
    }

    public function render()
    {
        return view('livewire.admin.promotion-wizard', [
            'academicYears' => AcademicYear::orderByDesc('name')->get(),
            'classes' => ClassRoom::orderBy('level')->orderBy('name')->get(),
            'latestBatch' => PromotionBatch::with(['sourceAcademicYear', 'targetAcademicYear', 'initiator'])
                ->where('status', 'applied')
                ->latest('id')
                ->first(),
        ])->layout('components.layouts.admin', ['title' => 'Kenaikan Kelas']);
    }

    private function validateMapping(): void
    {
        $this->validate([
            'sourceAcademicYearId' => ['required', 'exists:academic_years,id'],
            'targetYearName' => ['required', 'string', 'max:20', 'regex:/^\d{4}\/\d{4}$/'],
            'targetStartDate' => ['required', 'date'],
            'targetEndDate' => ['required', 'date', 'after:targetStartDate'],
        ]);

        foreach ($this->classMapping as $sourceClassId => $targetClassId) {
            if ($targetClassId !== '' && $targetClassId !== null) {
                $this->validateOnly('classMapping.'.$sourceClassId, ["classMapping.{$sourceClassId}" => 'nullable|exists:classes,id']);
            }
        }
    }

    private function normalizedMapping(): array
    {
        return collect($this->classMapping)
            ->mapWithKeys(fn ($target, $source) => [(int) $source => $target === '' ? null : (int) $target])
            ->all();
    }

    private function setDefaultTargetYear(?AcademicYear $source): void
    {
        if (! $source) {
            return;
        }

        if (preg_match('/^(\d{4})\/(\d{4})$/', $source->name, $matches)) {
            $start = (int) $matches[2];
            $end = $start + 1;
            $this->targetYearName = $start.'/'.$end;
            $this->targetStartDate = $start.'-07-01';
            $this->targetEndDate = $end.'-06-30';
        } else {
            $this->targetYearName = '';
        }
    }
}
