<?php

namespace App\Http\Controllers;

use App\Models\Roles\Repository\RoleRepository;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAll(Request $request)
    {
        $sortData = $request->get('sort') ? preg_split("/[\s|]+/", $request->get('sort')) : [];

        $sortBy = 'id';
        $sortDir = 'desc';

        if (!empty($sortData)) {
            $sortBy = $sortData[0];
            $sortDir = $sortData[1];
        }

        $perPage = (int)$request->get('per_page');
        $page = (int)$request->get('page');

        $roles = $this->roleRepository->getAll($sortBy, $sortDir, $perPage, $page, 'permissions');

        return json_encode($roles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => "required|unique:roles",
        ]);

        try {
            $this->roleRepository->store($request->name,);

            return json_encode([
                'message' => 'Rol actualizado satisfactoriamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un problema al guardar los datos'
            ], 400);
        }
    }

    public function update($id, Request $request)
    {
        $request->validate([
            "name' => 'required|unique:roles,$id",
        ]);

        $role = [
            'name' => $request->name,
        ];

        try {
            $this->roleRepository->update($role, $id);

            return json_encode([
                'message' => 'Rol actualizado satisfactoriamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un problema al guardar los datos'
            ], 400);
        }
    }

    public function addPermission($id, Request $request)
    {
        $permissionName = $request->permissionName;
        $activate = $request->activate;

        try {
            $this->roleRepository->addPermission($id, $permissionName, $activate);

            return json_encode([
                'message' => 'Permiso añadido correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un problema al guardar los datos'
            ], 400);
        }
    }

    public function delete($id)
    {
        try {
            $this->roleRepository->delete($id);

            return json_encode([
                'message' => 'Rol eliminado satisfactoriamente'
            ]);

        } catch (\Exception $e) {
            return json_encode([
                'message' => 'Hubo un problema al eliminar los datos'
            ]);
        }
    }
}
