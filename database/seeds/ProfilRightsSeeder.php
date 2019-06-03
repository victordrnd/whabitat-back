<?php

use Illuminate\Database\Seeder;
use App\Profil_rights;
class ProfilRightsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manager_rights = [1,2];
        foreach($manager_rights as $right){
            Profil_rights::create([
                'profil_id' => 1,
                'right_id' => $right 
            ]);
        }

        Profil_rights::create([
            'profil_id' => 2,
            'right_id' => 2
        ]);
        
    }
}
