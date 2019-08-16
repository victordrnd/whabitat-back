<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['arrival_date', 'departure_date', 'guest_id', 'property_id']; 
}
