<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['arrival_date', 'departure_date','adults','children', 'guest_id', 'property_id','amount','setup_intent']; 


    public function guest(){
        return $this->belongsTo(User::class, 'guest_id');
    }
}
