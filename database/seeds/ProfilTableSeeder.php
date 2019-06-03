<?php

use Illuminate\Database\Seeder;
use App\Profil;
class ProfilTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Profil::create([
            'label' => 'Manager'
        ]);

        Profil::create([
            'label' => 'Chef de projet'
        ]);

        Profil::create([
            'label' => 'Collaborateur'
        ]);
    }
}
