<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $validator  = Validator::make($request->all(),[
      'email' => 'required|exists:users,email|email',
      'password' => 'required|string',
    ]);
    if($validator->fails()){
      //return Controller::responseJson(422, 'test',$validator->errors());
      return $this->validationError($validator);
    }
    $credentials = $request->only(['email', 'password']);
    if (!$token = auth()->attempt($credentials)) {
      return $this->responseJson(Controller::$HTTP_UNAUTHORIZED, 'Les identifiants sont incorrects');
    }
    $data =  [
      'user' => auth()->user(),
      'token' => $token,
    ];
    return $this->responseJson(200, 'Identifiants valides', $data);
  }


  public function signup(Request $request)
  {
    $validator  = Validator::make($request->all(),[
      'firstname' => 'required|string',
      'lastname' => 'required|string',
      'email' => 'required|unique:users,email|email',
      'password' => 'required|string',
      'phone' => 'required|string',
      'country' => 'required|string'
    ]);
    if($validator->fails()){
      //return Controller::responseJson(422, 'test',$validator->errors());
      $errors = [
        'errors' => $validator->errors(),
        'message' => 'erreur de validation'
      ];
      return response()->json($errors);
    }
    $password = $request->password;
    $request->merge(['password' => Hash::make($password)]);
    $user = User::create($request->all());
    //$user->createAsStripeCustomer();

    $credentials = [
      'email' => $request->email,
      'password' =>  $password
    ];
    if (!$token = auth()->attempt($credentials)) {
      return $this->responseJson(Controller::$HTTP_NOK, 'Les identifiants sont incorrects');
    }
    $data =  [
      'user' => auth()->user(),
      'token' => $token,
    ];
    return $this->responseJson(Controller::$HTTP_OK, 'Identifiants valides', $data);
  }


  public function getCurrentUser(){
    return auth()->user();
  }
}
