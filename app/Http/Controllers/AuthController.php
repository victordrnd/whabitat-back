<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Response\JsonResponse;
use App\Http\Requests\Auth\SignInRequest;

class AuthController extends Controller
{
  public static function signin(SignInRequest $request)
  {
    $credentials = $request->only(['email', 'password']);
    if (!$token = auth()->attempt($credentials)) {
      return JsonResponse::unauthorized();
    }
    return AuthController::respondWithToken($request, $token);
  }

  protected static function respondWithToken(SignInRequest $request, $token)
  {
    $data =  [
      'user' => auth()->user(),
      'token' => $token,
    ];
    return JsonResponse::setData($data);
  }
}
