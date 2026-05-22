<?php

namespace App\Livewire\Kepsek;

use App\Livewire\Admin\SurveyAnalysis as AdminSurveyAnalysis;
use App\Models\KajianFeedback;

class SurveyAnalysis extends AdminSurveyAnalysis
{
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
        ])->layout('components.layouts.kepsek', ['title' => 'Hasil Survey']);
    }
}
