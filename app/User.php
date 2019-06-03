<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable implements JWTSubject
{
  use Notifiable;
  use SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'firstname', 'lastname', 'email', 'birth_date', 'profil_id', 'password', 'creator_id'
  ];
  protected $softDelete = true;

  protected $hidden = ['password', 'profil_id', 'creator_id', 'created_at', 'updated_at', 'deleted_at','pivot'];

  public function projects()
  {
    return $this->belongsToMany(Project::class, 'user_projects', 'user_id', 'project_id');
  }


  public function profil()
  {
    return $this->hasOne(Profil::class, 'id', 'profil_id');
  }

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

  public function parseUserRequest($request){
      $this->firstname  = $request->get('firstname');
      $this->lastname   = $request->get('lastname');
      $this->email      = $request->get('email');
      $this->birth_date = $request->get('birth_date');
      $this->password   = Hash::make($request->password);
      $this->creator_id = auth()->user()->id;
  }

  public function hasRight($rightToCheck)
  {
    foreach ($this->profil->rights as $right) {
      if ($right->code === $rightToCheck) {
        return true;
      }
    }
    return false;
  }
}
