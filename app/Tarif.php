<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    public $timestamps = false;

    protected $fillable = ['start', 'end', 'amount', 'single_night_amount', 'two_night_amount'];
}
