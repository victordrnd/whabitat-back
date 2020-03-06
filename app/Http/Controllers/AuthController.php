<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Stripe;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $validator  = Validator::make($request->all(), [
      'email' => 'required|exists:users,email|email',
      'password' => 'required|string',
    ]);
    if ($validator->fails()) {
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
    $validator  = Validator::make($request->all(), [
      'firstname' => 'required|string',
      'lastname' => 'required|string',
      'email' => 'required|unique:users,email|email',
      'password' => 'required|string',
      'phone' => 'required|string',
      'country' => 'required|string'
    ]);
    if ($validator->fails()) {
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
    $customer = \Stripe\Customer::create([
      "email" => $user->email,
      "name" => $user->lastname . ' ' . $user->firstname,
      "phone" => $user->phone,
      "description" => "Compte ".$user->country
    ]);
    $user->stripe_id = $customer->id;
    $user->save();
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


  public function logout(){
    JWTAuth::parseToken()->invalidate();
    return $this->responseJson(200, 'Le token a été invalidé correctement');
  }

  public function getCurrentUser()
  {
    return $this->responseJson(200, "l'utilisateur a été retournée",auth()->user());
  }
}
