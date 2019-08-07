<?php

namespace App\Http\Middleware;

use Closure;
use app\User;
use App\Response\JsonResponse;

class CheckRight
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $rightToCheck)
    {
        $id = auth()->user()->id;
        $user = User::where('id', $id)->with('profil', 'profil.rights')->first();
        if($user->hasRight($rightToCheck)){
           return $next($request); 
        }else{
            return JsonResponse::unauthorized();
        }
    }
}
