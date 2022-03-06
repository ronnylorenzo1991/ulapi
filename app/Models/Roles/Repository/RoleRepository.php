<?php

namespace App\Models\Roles\Repository;

use App\Models\Shared\Repository\SharedRepositoryEloquent;
use Spatie\Permission\Models\Role;

class RoleRepository extends SharedRepositoryEloquent
{
    public function __construct(
        Role $entity
    )
    {
        parent::__construct($entity);
    }

    public function store($name)
    {
        return Role::create(['name' => $name]);
    }

    public function addPermission($roleId, $permissionName, $activate) {
        $role = Role::findOrFail($roleId);
        $activate ? $role->givePermissionTo($permissionName) : $role->revokePermissionTo($permissionName);
    }
}
