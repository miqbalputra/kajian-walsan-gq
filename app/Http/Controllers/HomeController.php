<?php

namespace App\Http\Controllers;

use App\Models\KajianEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get next/current kajian
        $nextKajian = KajianEvent::with('academicYear')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time_start')
            ->first();

        // Get ongoing kajian (today and status open)
        $ongoingKajian = KajianEvent::where('date', now()->toDateString())
            ->where('status', 'open')
            ->first();

        // Get last completed kajian for stats
        $lastKajian = KajianEvent::where('date', '<', now()->toDateString())
            ->orWhere(function ($query) {
                $query->where('date', now()->toDateString())
                    ->where('status', 'closed');
            })
            ->orderBy('date', 'desc')
            ->first();

        // Calculate time status for next kajian
        $timeStatus = null;
        $countdownMinutes = null;

        if ($nextKajian) {
            $timeStartParsed = Carbon::parse($nextKajian->time_start);
            $eventDateTime = Carbon::parse($nextKajian->date->format('Y-m-d') . ' ' . $timeStartParsed->format('H:i:s'));
            $now = Carbon::now();

            if ($eventDateTime->isToday()) {
                $diffMinutes = $now->diffInMinutes($eventDateTime, false);

                if ($diffMinutes > 0 && $diffMinutes <= 120) {
                    // Starting within 2 hours
                    $timeStatus = 'soon';
                    $countdownMinutes = $diffMinutes;
                } elseif ($diffMinutes <= 0 && $diffMinutes > -180) {
                    // Currently ongoing (within 3 hours of start)
                    $timeStatus = 'ongoing';
                } elseif ($diffMinutes > 120) {
                    $timeStatus = 'today';
                }
            } elseif ($eventDateTime->isTomorrow()) {
                $timeStatus = 'tomorrow';
            } else {
                $timeStatus = 'upcoming';
            }
        }

        // Use ongoing kajian if available
        $currentKajian = $ongoingKajian ?? $nextKajian;
        if ($ongoingKajian) {
            $timeStatus = 'ongoing';
        }

        return view('welcome', [
            'currentKajian' => $currentKajian,
            'timeStatus' => $timeStatus,
            'countdownMinutes' => $countdownMinutes,
            'lastKajian' => $lastKajian,
            'eventTimestamp' => ($nextKajian && $timeStatus === 'soon')
                ? Carbon::parse($nextKajian->date->format('Y-m-d') . ' ' . Carbon::parse($nextKajian->time_start)->format('H:i:s'))->timestamp * 1000
                : null,
        ]);
    }
}
