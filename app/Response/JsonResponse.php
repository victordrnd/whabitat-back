<?php

namespace App\Response;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;

class JsonResponse extends Model
{
  /**************************************************
  * Accesseurs en écriture                          *
  **************************************************/
  public static function setData($value)
  {
    return JsonResponse::array($value, []);
  }

  public static function setError($error){
    return JsonResponse::array(null,$error);
  }



  public static function setMessage($message){
    return JsonResponse::array(null, [], $message);
  }



  public static function array($array, $error, $message = "Success")
  {
    if(is_null($array) && $message == "Success"){
      $array = [];
      $message = "Object not founded";
      $status_code = 420;
    }else{
      if(is_null($array)){
        $array = [];
      }
      $status_code = 200;
    }
    $data = [
      'errors' => $error,
      'result' => [
        'data' => $array,
        'message' => $message
      ],
      'status_code' => $status_code
    ];
    return response()->json($data, 200);
  }
}
