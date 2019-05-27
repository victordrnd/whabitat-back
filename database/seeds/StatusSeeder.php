<?php

use Illuminate\Database\Seeder;
use App\Status;
class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Status::create([
        'label' => 'En cours'
      ]);

      Status::create([
        'label' => 'Terminé'
      ]);
      Status::create([
        'label' => 'Actif'
      ]);
      Status::create([
        'label' => 'Annulé'
      ]);
    }
}
