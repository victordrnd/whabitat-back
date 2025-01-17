<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReservationAddVacanciers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function(Blueprint $table){
            $table->integer('adults')->after('departure_date')->default(0);
            $table->integer('children')->after('adults')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function(Blueprint $table){
            $table->dropColumn('adults');
            $table->dropColumn('children');
        });
    }
}
