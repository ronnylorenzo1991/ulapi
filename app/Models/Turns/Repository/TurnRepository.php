<?php

namespace App\Models\Turns\Repository;

use App\Models\Shared\Repository\SharedRepositoryEloquent;
use App\Models\Turns\Entity\Turn;
use Carbon\Carbon;

class TurnRepository extends SharedRepositoryEloquent
{
    public function __construct(
        Turn $entity
    )
    {
        parent::__construct($entity);
    }

    public function getTurnNumber()
    {
        $lastTurnNumber = Turn::orderBy('id', 'desc')->first()->number ?? 0;

        return $lastTurnNumber + 1;
    }

    public function getAll($sortBy, $sortDir, $perPage, $page, $relationships = null) {
        return Turn::with($relationships)
            ->join('users', 'users.id', 'turns.user_id')
            ->join('turn_statuses', 'turn_statuses.id', 'turns.status_id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }

    public function getTurnsByMonth($params) {
        $monthLabels = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
        $startYear = date('Y', strtotime($params['startDate']));
        $endYear = date('Y', strtotime($params['endDate']));

        if ($startYear === $endYear) {
            $startMonth = (int)date('n', strtotime($params['startDate']));
            $endMonth = (int)date('n', strtotime($params['endDate']));

            while ($startMonth <= $endMonth) {
                $labels[] = $monthLabels[$startMonth - 1];
                $num_days = cal_days_in_month(CAL_GREGORIAN, $startMonth, $startYear);
                $startDate = date('1/'.$startMonth.'/'.$startYear);
                $endDate = date($num_days.'/'.$startMonth.'/'.$endYear);

                $query = $this->entity->whereBetween('date', [
                    Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d').' 00:00',
                    Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d').' 23:59'
                ]);

                $totals[] = $query->get()->count();
                $startMonth++;
            }
        } else {
            while ($startYear <= $endYear) {
                $labels[] = $startYear;
                $startDate = date('1/1/'.$startYear);
                $endDate = date('12/31/'.$startYear);

                $query = $this->entity->whereBetween('date', [
                    Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d').' 00:00',
                    Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d').' 23:59'
                ]);

                $totals[] = $query->get()->count();
                $startYear++;
            }
        }

        return [$totals, $labels];
    }
}
