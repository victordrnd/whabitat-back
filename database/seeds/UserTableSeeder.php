<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\User;
class UserTableSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run()
  {
    for($i=0; $i<20;$i++){
      User::create([
        'firstname' => Str::random(10),
        'lastname' => Str::random(10),
        'email' => Str::random(10).'@gmail.com',
        'birth_date' => date("Y-m-d", time())
      ]);
    }

  }
}
