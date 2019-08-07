<?php

use Illuminate\Http\Request;
Illuminate\Foundation\Http\FormRequest;
abstract class BaseRequest extends Request{

    public function expectsJson(){
        return true;
    }

    public function wantsJson(){
        return true;
    }

    public function response(array $errors)
    {
        return $this->respond([
                'status_code'   => 400 ,                                 
                'errors'          => array_map(function($errors){         
                        foreach($errors as $key=>$value){
                            return $value;                           
                        }                       
                    },$errors),
                'message' => 'erreurs de validation'
            ]);
    }

    public function respond($data , $headers=[] ){
        return \Response::json($data);
    }
}