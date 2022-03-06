<?php
namespace Database\Seeders;

use App\Models\Users\Entity\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'SuperAdministrator',
            'email' => 'administration@ulapi.com',
            'email_verified_at' => now(),
            'password' => Hash::make('default@ulapi'),
            'created_at' => now(),
            'updated_at' => now()
        ])->assignRole('administrador');
    }
}
