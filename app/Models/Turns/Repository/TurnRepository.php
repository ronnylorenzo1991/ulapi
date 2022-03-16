<?php

namespace App\Models\Turns\Repository;

use App\Models\Shared\Repository\SharedRepositoryEloquent;
use App\Models\Turns\Entity\Turn;
use App\Models\Users\Entity\User;

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
}
