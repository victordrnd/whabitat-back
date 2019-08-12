<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements JWTSubject
{
  use Notifiable;
  use Billable;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'firstname', 'lastname', 'email', 'password', 'phone', 'country'
  ];

  protected $hidden = ['password', 'created_at', 'updated_at'];



  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */


  public function getJWTIdentifier()
  {
    return $this->getKey();
  }


  public function getJWTCustomClaims()
  {
    return [];
  }

  


}
