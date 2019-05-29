<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Response\JsonResponse;
use App\Http\Requests\Users\AddUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\DeleteUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
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
    return JsonResponse::setData(User::where('id', $id)->with('projects')->get());
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

    $data = User::create([
      'firstname'   => $request->firstname,
      'lastname'    => $request->lastname,
      'email'       => $request->email,
      'password'    => Hash::make($request->password),
      'birth_date'  => $request->birth_date,
      'creator_id'  => auth()->user()->id
    ]);
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
    foreach ($request->all() as $proprietyname => $value) {
      if(Schema::hasColumn('users', $proprietyname)){
        User::where('id', $request->id)->update([
          $proprietyname => $value
        ]);
      }
    }
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
