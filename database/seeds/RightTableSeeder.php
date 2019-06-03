<?php

use Illuminate\Database\Seeder;
use App\Right;
class RightTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Right::create([
            'label' => 'voir tous les projets',
            'code' => 'seeAllProject'
        ]);

        Right::create([
            'label' => "modifier le status d'un projet",
            'code' => 'editProjectStatus'
        ]);
    }
}
