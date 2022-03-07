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
                return [
                    'id'              => $startId + $key,
                    'description'     => 'Turno Pendiente No.'.$turn->number,
                    'title'           => $turn->user->name.' - Pendiente',
                    'start'           => $turn->date->format('Y-m-d'),
                    'textColor'       => '#000000',
                    'backgroundColor' => '#d5e8fa',
                    'borderColor'     => '#d5e8fa',
                    'eventTypeSort'   => '2',
                    'eventType'       => '0',
                    'permission'      => 'turn.show',
                    'link'            => route('turns.show', $turn->id),
                ];
            })
            ->toArray();
    }
}
