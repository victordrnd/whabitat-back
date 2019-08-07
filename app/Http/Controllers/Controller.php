<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static $HTTP_OK = 200;
    public static $HTTP_NOK = 422;
    public static $HTTP_VALIDATION_ERROR = 405;
    public static $HTTP_NOT_FOUND = 404;
    public static $HTTP_FORBIDDEN = 403;
    public static $HTTP_USER_NOT_FOUND = 406;
    public static $HTTP_TOKEN_NOT_CREATE = 500;
    public static $HTTP_TOKEN_ABSENT = 501;
    public static $HTTP_TOKEN_INVALID = 502;
    public static $HTTP_TOKEN_EXPIRED = 503;

    public static function responseJson($code, $description, $result = '', $notify = false)
    {

        if ($result == '') {

            $return = array('code' => $code, 'description' => $description, 'notify' => $notify);
            return \Response::json(array('return' => $return));
        } else {

            $return = array('code' => $code, 'description' => $description, 'notify' => $notify);
            return \Response::json(array('result' => $result, 'return' => $return));
        }
    }


    public function validationError($validator){
        $data = [
            'errors' => $validator->errors(),
            'message' => "Une erreur de validation s'est produite"
        ];

        return response()->json($data);
    }
}
