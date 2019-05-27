<?php

namespace App\Response;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
class Reponse extends Model
{
    public static function ThrowResponse(){
      return response()->json([
        
      ])
    }
}
