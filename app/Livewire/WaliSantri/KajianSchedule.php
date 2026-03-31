<?php

namespace App\Livewire\WaliSantri;

use App\Models\KajianEvent;
use Carbon\Carbon;
use Livewire\Component;

class KajianSchedule extends Component
{
    public $currentMonth;
    public $currentYear;
    public $calendarDays = [];
    public $events = [];
    public $selectedEvent = null;
    public $showEventDetail = false;

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->generateCalendar();
    }

    public function generateCalendar()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $date->daysInMonth;
        $startDayOfWeek = $date->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

        $this->calendarDays = [];

        // Fill previous month days
        $prevMonthDate = (clone $date)->subMonth();
        $daysInPrevMonth = $prevMonthDate->daysInMonth;
        for ($i = $startDayOfWeek - 1; $i >= 0; $i--) {
            $this->calendarDays[] = [
                'day' => $daysInPrevMonth - $i,
                'month' => $prevMonthDate->month,
                'year' => $prevMonthDate->year,
                'current_month' => false,
            ];
        }

        // Current month days
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $this->calendarDays[] = [
                'day' => $i,
                'month' => $this->currentMonth,
                'year' => $this->currentYear,
                'current_month' => true,
            ];
        }

        // Fill next month days to complete the 6x7 grid (42 days)
        $nextMonthDate = (clone $date)->addMonth();
        $remainingDays = 42 - count($this->calendarDays);
        for ($i = 1; $i <= $remainingDays; $i++) {
            $this->calendarDays[] = [
                'day' => $i,
                'month' => $nextMonthDate->month,
                'year' => $nextMonthDate->year,
                'current_month' => false,
            ];
        }

        $this->loadEvents();
    }

    public function loadEvents()
    {
        $startDate = Carbon::create($this->calendarDays[0]['year'], $this->calendarDays[0]['month'], $this->calendarDays[0]['day'])->toDateString();
        $endDate = Carbon::create($this->calendarDays[41]['year'], $this->calendarDays[41]['month'], $this->calendarDays[41]['day'])->toDateString();

        $this->events = KajianEvent::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($event) {
                return $event->date->toDateString();
            })
            ->toArray();
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->generateCalendar();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->generateCalendar();
    }

    public function showDetail($eventId)
    {
        $this->selectedEvent = KajianEvent::find($eventId);
        $this->showEventDetail = true;
    }

    public function render()
    {
        return view('livewire.wali-santri.kajian-schedule')
            ->layout('components.layouts.wali-santri');
    }
}
