<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Response\JsonResponse;
use App\Http\Requests\Users\AddUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\DeleteUserRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
Use App\Services\UserService;

class UserController extends Controller
{



  /**
  * Return all users from users table
  * @return array
  */
  public static function showAll(){
    return JsonResponse::setData(User::with('projects')->get());
  }






  /**
  * Return user identified by $id
  * @param int $id
  * @return array
  */
  public static function get($id){
    try{
      $user = User::where('id', $id)->with(['projects'])->firstOrFail();

    }catch(ModelNotFoundException $e){
      return JsonResponse::exception($e);
    }
    return JsonResponse::setData($user);
  }





  /**
  * add a user to the user table
  * @param int $id
  * @param string $firstname
  * @param string $lastname
  * @param string $email
  * @param date $birth_date
  * @return array
  */
  public static function add(AddUserRequest $request){

    $data = new \App\Services\UserService;
    $data = $data->create($request);
    return JsonResponse::setData($data);
  }





  /**
  * update an existing user
  * @param int $id
  * @param string $firstname
  * @param string $lastname
  * @param string $email
  * @param date $birth_date
  * @return array
  */
  public static function update(UpdateUserRequest $request){
    // foreach ($request->all() as $proprietyname => $value) {
    //   if(Schema::hasColumn('users', $proprietyname)){
    //     User::where('id', $request->id)->update([
    //       $proprietyname => $value
    //     ]);
    //   }
    // }

    $data = User::findOrFail($request->id);
    $data->parseUserRequest($request);
    $data->save();

    return JsonResponse::setData(User::find($request->id));
  }




  /**
  * @param int $id
  * @return void
  */
  public static function delete(DeleteUserRequest $request){
    User::destroy($request->id);
    return JsonResponse::setMessage('Done.');
  }
}
