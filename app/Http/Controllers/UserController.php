<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Users\Entity\User;
use App\Models\Users\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\Users\Entity\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        return view('users.index');
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

        $perPage = (int) $request->get('per_page');
        $page = (int) $request->get('page');

        $users = $this->userRepository->getAll($sortBy, $sortDir, $perPage, $page, 'roles');

        return json_encode($users);
    }

    public function store(UserRequest $request)
    {
        $user = [
            'name'     => $request->get('name'),
            'email'    => $request->get('email'),
            'password' => Hash::make('default123456*'),
        ];

        try {
            $user = $this->userRepository->store($user);
            $user->assignRole($request->get('role', 2));
            return json_encode([
                'message' => 'Usuario guardado satisfactoriamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un problema al guardar los datos'
            ], 400);
        }
    }

    public function update(UserRequest $request, $id)
    {
        $user = [
            'name'  => $request->get('name'),
            'email' => $request->get('email'),
        ];

        if (isset($request->password)) {
            $user['password'] = $request->password;
        }

        try {
            $this->userRepository->update($user, $id);
            $user = User::findOrFail($id);
            $user->syncRoles($request->get('role', 2));
            return json_encode([
                'message' => 'Usuario actualizada satisfactoriamente'
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
            $this->userRepository->delete($id);

            return json_encode([
                'message' => 'Usuario eliminada satisfactoriamente'
            ]);

        } catch (\Exception $e) {
            return json_encode([
                'message' => 'Hubo un problema al eliminar los datos'
            ]);
        }
    }
}
