<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $fillable = ['label'];
    public $hidden = ['created_at', 'updated_at'];

    public function rights(){
        //
        return $this->belongsToMany(Right::class,'profil_rights','profil_id', 'right_id');
    }
}
