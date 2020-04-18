<?php

use Illuminate\Database\Seeder;
use App\Tarif;
class TableTarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Tarif::where('id', '!=', 'null')->delete();
        Tarif::create([
            'start' => '2020-04-02',
            'end' => '2020-07-03',
            'amount' => '222',
            'two_night_amount' => '333',
            'single_night_amount' => '444'
        ]);

        Tarif::create([
            'start' => '2020-07-04',
            'end' => '2020-08-29',
            'amount' => '242',
            'two_night_amount' => '363',
            'single_night_amount' => '484'
        ]);

        Tarif::create([
            'start' => '2020-08-30',
            'end' => '2020-12-18',
            'amount' => '202',
            'two_night_amount' => '303',
            'single_night_amount' => '202'
        ]);

        Tarif::create([
            'start' => '2020-12-19',
            'end' => '2021-01-03',
            'amount' => '222',
            'two_night_amount' => '333',
            'single_night_amount' => '444'
        ]);
    }
}
