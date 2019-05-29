<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatorId extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::table('users',function($table){
      $table->integer('creator_id')->unsigned()->after('birth_date');
    });
    Schema::table('projects',function($table){
      $table->integer('creator_id')->unsigned()->after('status_id');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::table('users',function($table){
      $table->dropColumn('creator_id');
    });
    Schema::table('proejcts',function($table){
      $table->dropColumn('creator_id');
    });
  }
}
