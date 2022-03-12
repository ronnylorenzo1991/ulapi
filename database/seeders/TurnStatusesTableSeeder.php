<?php

namespace Database\Seeders;

use App\Models\TurnStatuses\Entity\TurnStatus;
use Illuminate\Database\Seeder;

class TurnStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TurnStatus::create([
            'name'       => 'Pendiente',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        TurnStatus::create([
            'name'       => 'Confirmado',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        TurnStatus::create([
            'name'       => 'Realizado',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
