<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
    protected $fillable = ['label', 'code'];
    public $hidden = ['created_at', 'updated_at', 'pivot'];
}
