<?php

namespace App\Http\Controllers;

use App\Models\Alerts\Entity\AlertActions;
use App\Models\Alerts\Entity\AlertTrigger;
use App\Models\Cameras\Entity\Camera;
use App\Models\Users\Entity\User;
use App\Models\Weights\Entity\Weight;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DefaultController extends Controller
{
    public function getLists(Request $request)
    {
        $request->validate([
            'lists' => 'required',
        ]);


        $results = [];

        foreach (json_decode($request->get('lists')) as $item) {
            switch ($item) {
                case 'roles':
                    $results[$item] = Role::all()->toArray();
                    break;
                case 'permissions':
                    $results[$item] = Permission::all()->toArray();
                    break;
                case 'users':
                    $results[$item] = User::all()->toArray();
                    break;
                case 'clients':
                    $users = User::with('roles')->get();
                    $results[$item] = $users->filter(fn ($user) => $user->hasRole('cliente'))->pluck('name', 'id');
                    break;
            }
        }

       return json_encode(['lists' => $results]);
    }
}
