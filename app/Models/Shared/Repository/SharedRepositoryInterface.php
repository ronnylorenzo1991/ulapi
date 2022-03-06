<?php

namespace App\Models\Shared\Repository;

interface SharedRepositoryInterface
{
    public function find($id);

    public function findOrFail($id);
}
