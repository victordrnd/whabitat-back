<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_projects extends Model
{
  public $timestamps = false;
  
  protected $fillable = ['user_id', 'project_id'];
}
