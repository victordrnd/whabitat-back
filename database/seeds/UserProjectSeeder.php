<?php

use Illuminate\Database\Seeder;
use App\User_projects;
class UserProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1;$i<=20;$i++){
          User_projects::create([
            'project_id' => random_int(1,8),
            'user_id' => random_int(1,20)
          ]);
        }
    }
}
