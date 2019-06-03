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

  public static function unauthorized(){
    return JsonResponse::array(null, "Not Allowed", "Unauthorized");
  }
  public static function exception($e){
    $data = [
      'errors' => $e->getFile(). ' on line : '.$e->getLine(),
      'result' => [
        'data' => [],
        'message' => $e->getMessage()
      ],
      'status_code' => 401
    ];
    return response()->json($data, 401);
  }


  public static function setError($error){
    return JsonResponse::array(null,$error);
  }



  public static function setMessage($message){
    return JsonResponse::array(null, [], $message);
  }



  public static function array($array, $error, $message = "Success")
  {
    $status_code = 200;
    if(!is_array($error)){
      if($error == "Not Allowed"){
        $status_code = 401;
      }
      $error = array($error);
    }
    if(is_null($array) && $message == "Success"){
      $array = [];
      $message = "Object not founded";
      $status_code = 422;
    }else{
      if(is_null($array)){
        $array = [];
      }
    }
    $data = [
      'errors' => $error,
      'result' => [
        'data' => $array,
        'message' => $message
      ],
      'status_code' => $status_code
    ];
    return response()->json($data, $status_code);
  }
}
