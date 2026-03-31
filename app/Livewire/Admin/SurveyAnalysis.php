<?php

namespace App\Livewire\Admin;

use App\Models\KajianEvent;
use App\Models\KajianFeedback;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class SurveyAnalysis extends Component
{
    use WithPagination;

    public $filterEvent = '';
    public $filterRating = '';
    public $filterDateStart = '';
    public $filterDateEnd = '';
    public $activeTab = 'stats'; // stats, list

    protected $queryString = [
        'filterEvent' => ['except' => ''],
        'filterRating' => ['except' => ''],
        'filterDateStart' => ['except' => ''],
        'filterDateEnd' => ['except' => ''],
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filterEvent', 'filterRating', 'filterDateStart', 'filterDateEnd'])) {
            $this->resetPage();
        }
    }

    public function getEventsProperty()
    {
        return KajianEvent::orderByDesc('date')->get();
    }

    public function getStatsProperty()
    {
        $query = KajianFeedback::query();

        if ($this->filterEvent) {
            $query->where('kajian_event_id', $this->filterEvent);
        }
        if ($this->filterRating) {
            $query->where('rating', $this->filterRating);
        }
        if ($this->filterDateStart) {
            $query->whereDate('created_at', '>=', $this->filterDateStart);
        }
        if ($this->filterDateEnd) {
            $query->whereDate('created_at', '<=', $this->filterDateEnd);
        }

        $allFeedbacks = $query->get();
        $totalResponses = $allFeedbacks->count();
        $averageRating = $totalResponses > 0 ? round($allFeedbacks->avg('rating'), 2) : 0;

        // Multi-criteria averages
        $materiAvg = 0;
        $operasionalAvg = 0;

        if ($totalResponses > 0) {
            $materiSum = 0;
            $operasionalSum = 0;
            $feedbacksWithExtra = 0;

            foreach ($allFeedbacks as $f) {
                if ($f->extra_feedback) {
                    $materiSum += $f->extra_feedback['materi'] ?? 0;
                    $operasionalSum += $f->extra_feedback['operasional'] ?? 0;
                    $feedbacksWithExtra++;
                }
            }

            if ($feedbacksWithExtra > 0) {
                $materiAvg = round($materiSum / $feedbacksWithExtra, 2);
                $operasionalAvg = round($operasionalSum / $feedbacksWithExtra, 2);
            }
        }

        $ratingCounts = [
            5 => $allFeedbacks->where('rating', '>=', 4.5)->count(),
            4 => $allFeedbacks->whereBetween('rating', [3.5, 4.4])->count(),
            3 => $allFeedbacks->whereBetween('rating', [2.5, 3.4])->count(),
            2 => $allFeedbacks->whereBetween('rating', [1.5, 2.4])->count(),
            1 => $allFeedbacks->where('rating', '<', 1.5)->count(),
        ];

        // Monthly Trend (6 months) - compatible with both MySQL and SQLite
        $driver = DB::getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? 'strftime("%Y-%m", created_at)'
            : 'DATE_FORMAT(created_at, "%Y-%m")';

        $trendData = KajianFeedback::select(
            DB::raw($monthExpr . ' as month'),
            DB::raw('AVG(rating) as avg_rating'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        return [
            'total' => $totalResponses,
            'average' => $averageRating,
            'ratingCounts' => $ratingCounts,
            'trendData' => $trendData,
            'materiAvg' => $materiAvg,
            'operasionalAvg' => $operasionalAvg,
            'materiConclusion' => $this->getConclusion($materiAvg),
            'operasionalConclusion' => $this->getConclusion($operasionalAvg),
        ];
    }

    private function getConclusion($score)
    {
        if ($score <= 0)
            return ['text' => 'Belum ada data', 'color' => 'gray', 'icon' => 'hourglass_empty'];
        if ($score >= 4.5)
            return ['text' => 'Sangat Memuaskan', 'color' => 'emerald', 'icon' => 'verified'];
        if ($score >= 4.0)
            return ['text' => 'Sudah Baik', 'color' => 'blue', 'icon' => 'thumb_up'];
        if ($score >= 3.0)
            return ['text' => 'Cukup Berjalan', 'color' => 'amber', 'icon' => 'info'];
        if ($score >= 2.0)
            return ['text' => 'Perlu Perbaikan', 'color' => 'orange', 'icon' => 'warning'];
        return ['text' => 'Evaluasi Total', 'color' => 'red', 'icon' => 'report'];
    }

    public function render()
    {
        $query = KajianFeedback::with(['kajianEvent', 'user.parentProfile'])
            ->orderByDesc('created_at');

        if ($this->filterEvent) {
            $query->where('kajian_event_id', $this->filterEvent);
        }
        if ($this->filterRating) {
            $query->where('rating', $this->filterRating);
        }
        if ($this->filterDateStart) {
            $query->whereDate('created_at', '>=', $this->filterDateStart);
        }
        if ($this->filterDateEnd) {
            $query->whereDate('created_at', '<=', $this->filterDateEnd);
        }

        return view('livewire.admin.survey-analysis', [
            'feedbacks' => $query->paginate(15),
            'stats' => $this->stats,
            'events' => $this->events,
        ])->layout('components.layouts.admin', ['title' => 'Analisis Survey Kepuasan']);
    }
}
