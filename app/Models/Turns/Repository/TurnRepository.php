<?php

namespace App\Models\Turns\Repository;

use App\Models\Shared\Repository\SharedRepositoryEloquent;
use App\Models\Turns\Entity\Turn;

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
}
