<?php

namespace App\Models\Calendar\Repositories;

use App\Models\Turns\Entity\Turn;

class CalendarRepository
{
    public function run($startAt, $endAt): array
    {
        $calendarEvents = [];
        $eventTypes = collect(
            [
                [
                    'name' => 'Turns'
                ],
                [
                    'name' => 'TurnsRequested',
                ],
            ]
        );

        $userEventTypes = [
            'Turns',
            'TurnRequested',
        ];

        foreach ($eventTypes as $eventType) {
            if (in_array($eventType['name'], $userEventTypes)) {
                $calendarEvents = array_merge(
                    $calendarEvents,
                    $this->{'run'.$eventType['name']}(count($calendarEvents) + 1, $startAt, $endAt)
                );
            }
        }

        return $calendarEvents;
    }

    public function runTurns($startId, $startAt, $endAt): array
    {
        return (new Turn())
            ->whereBetween('date', [$startAt, $endAt])
            ->with('user', 'status')
            ->get()
            ->map(function ($turn, $key) use ($startId) {
                switch ($turn->status_id) {
                    case 1:
                        $color = "#F4FF8D";
                        $title = $turn->user->name.' - '.$turn->status->name;
                        break;
                    case 2:
                        $color = "#8DEDFF";
                        $title = $turn->user->name.' - '.$turn->status->name;
                        break;
                    case 3:
                        $color = "#CCFFCC";
                        $title = $turn->user->name.' - '.$turn->status->name.' - $'.$turn->payment;
                        break;
                }

                return [
                    'id'              => $startId + $key,
                    'db_id'           => $turn->id,
                    'description'     => 'Turno No.'.$turn->number,
                    'title'           => $title,
                    'start'           => $turn->date->format('Y-m-d'),
                    'textColor'       => '#000000',
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
                    'eventTypeSort'   => '2',
                    'eventType'       => '0',
                    'permission'      => 'turn.show',
                    'time'            => $turn->time,
                    'user_id'         => $turn->user_id,
                    'status_id'       => $turn->status_id,
                    'payment'         => $turn->payment,
                    'link'            => route('turns.show', $turn->id),
                    'clickable'       => true,
                ];
            })
            ->toArray();
    }
}
