<?php

namespace App\Models\Users\Repository;

use App\Models\Shared\Repository\SharedRepositoryEloquent;
use App\Models\Users\Entity\User;

class UserRepository extends SharedRepositoryEloquent
{
    public function __construct(
        User $entity
    ) {
        parent::__construct($entity);
    }
}
