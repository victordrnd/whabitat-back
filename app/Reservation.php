<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['arrival_date', 'departure_date','vacanciers', 'guest_id', 'property_id','amount','setup_intent']; 
}
