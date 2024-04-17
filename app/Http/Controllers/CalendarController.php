<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Absence;
use App\Models\Calendar;
use Illuminate\Http\Request;
use App\Models\CalendarArchive;
use Illuminate\Support\Facades\Log;

class CalendarController
{
    public function getMonthData()
    {
        $days = [];
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $daysInMonth = Carbon::create($currentYear, $currentMonth, 1)->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($currentYear, $currentMonth, $day);
            $days[] = [
                'date' => $date->toDateString(),
                'isWeekend' => in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
            ];
        }


        return response()->json($days);

    }

    public function getAbsenceTypes()
    {
        $types = Absence::all();
        return response()->json($types);
    }



    public function insertAbsence(Request $request)
    {
    $absence = $request->input('absence');
    $start_date = Carbon::parse($absence['start_date'])->startOfDay();
    $end_date = Carbon::parse($absence['end_date'])->startOfDay();
    $absence_id = $absence['absence_id'];

    $start = Carbon::parse($start_date)->startOfDay();
    $end = Carbon::parse($end_date)->startOfDay();


    if ($absence_id == 1) {
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                return response()->json(['error' => 'weekend']);
            }
        }
    }

    if ($absence_id == 2) {
        $count = Calendar::where('absence_id', 2)->count();
        if ($count + $start->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, $end) > 7) {
            return response()->json(['error' => 'moreThan7Days']);
        }
    }



    for ($date = $start; $date->lte($end); $date->addDay()) {
        $entry = Calendar::whereDate('date', $date)->first();

        if ($entry) {
            $entry->update(['absence_id' => $absence_id]);
        } else {
            $newEntry = Calendar::create(['date' => $date, 'absence_id' => $absence_id]);
        }
    }
    return response()->json(['success' => 'Absence inserted']);

    }


    public function getAbsences()
    {
        $calendars = Calendar::with(['absence' => function($query) {
            $query->select('id', 'name');
        }])->get();

        return response()->json($calendars);
    }


    public function toArchive(){
        $calendarsToArchive = Calendar::all();

        $highestArchiveNumber = CalendarArchive::max('archive_number');
        $newArchiveNumber = $highestArchiveNumber + 1;

        foreach ($calendarsToArchive as $calendar) {
            CalendarArchive::create([
                'date' => $calendar->date,
                'absence_id' => $calendar->absence_id,
                'archive_number' => $newArchiveNumber,
            ]);
        }

        return response()->json(['success' => 'Calendars archived']);
    }

    public function getArchive() {
        $archives = CalendarArchive::with(['absence' => function($query) {
            $query->select('id', 'name');
        }])->get();

        return response()->json($archives);
    }


}


