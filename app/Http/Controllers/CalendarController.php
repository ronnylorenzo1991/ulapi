<?php

namespace App\Http\Controllers;

use App\Models\Calendar\Repositories\CalendarRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index(CalendarRepository $calendarRepository, Request $request)
    {
        $todayDate = Carbon::now()->format('d-m-Y');

        $startAt = Carbon::parse($request->get('start') ?: $todayDate)
                ->startOfMonth()
                ->format('Y-m-d').' 00:00:00';
        $endAt = Carbon::parse($request->get('end') ?: $todayDate)
                ->lastOfMonth()
                ->format('Y-m-d').' 23:59:59';

        $events = $calendarRepository->run($startAt, $endAt);

        return response()->json($events);
    }
}
