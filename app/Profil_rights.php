<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Profil_rights extends Pivot
{
    protected $fillable = ['profil_id', 'right_id'];
    public $hidden = ['created_at', 'updated_at'];
}
