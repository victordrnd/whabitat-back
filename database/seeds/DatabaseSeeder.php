<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*$this->call([
          ProfilTableSeeder::class
        ]);


        $this->call([
          RightTableSeeder::class
        ]);*/
        $this->call([
          ProfilRightsSeeder::class
        ]);
    }
}
