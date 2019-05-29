<?php

namespace App\Http\Middleware;
use JWTAuth;
use Closure;
use Exception;
use App\Response\JsonResponse;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
class JwtMiddleware
{
  /**
  * Handle an incoming request.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \Closure  $next
  * @return mixed
  */
  public function handle($request, Closure $next)
  {
    try{
      $user = JWTAuth::parseToken()->authenticate();
    }
    catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
      return JsonResponse::exception($e);
    }
    catch(\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
      return JsonResponse::exception($e);
    }
    catch(\Tymon\JWTAuth\Exceptions\JWTException $e){
      return JsonResponse::exception($e);
    }
    return $next($request);
  }
}
