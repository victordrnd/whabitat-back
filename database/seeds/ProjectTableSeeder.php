<?php

use Illuminate\Database\Seeder;
use App\Project;
class ProjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        for($i =0; $i<10; $i++){
          Project::create([
            'label' => 'Projet '.$i,
            'progress' => $i+20,
            'status_id' => random_int(1,4)
          ]);
        }
    }
}
