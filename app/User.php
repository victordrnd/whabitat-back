<?php

namespace App;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
  use Notifiable;

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'firstname', 'lastname', 'email', 'birth_date', 'password', 'creator_id'
  ];

  public function projects(){
    return $this->belongsToMany(Project::class,'user_projects','project_id','user_id');
  }

  /**
  * The attributes that should be hidden for arrays.
  *
  * @var array
  */
  protected $hidden = ['password'];

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }


  public function getJWTCustomClaims()
  {
    return [];
  }

}
