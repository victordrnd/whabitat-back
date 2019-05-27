<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
class UserController extends Controller
{



  /**
  * Return all users from users table
  * @return array
  */
  public static function showAll(){
    return User::all();
  }






  /**
  * Return user identified by $id
  * @param int $id
  * @return array
  */
  public static function get($id){
    return User::find($id);
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
  public static function add(Request $request){
    $validatedData = $request->validate([
      'firstname'   => 'required',
      'lastname'    => 'required',
      'email'       => 'email|required',
      'birth_date'  => 'date|required'
    ]);

    return User::create([
      'firstname'   => $request->firstname,
      'lastname'    => $request->lastname,
      'email'       => $request->email,
      'birth_date'  => $request->birth_date
    ]);
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
  public static function update(Request $request){
    $validatedData = $request->validate([
      'id'          => 'required|exists:users,id',
      'firstname'   => 'required',
      'lastname'    => 'required',
      'email'       => 'email|required|unique:users',
      'birth_date'  => 'date|required'
    ]);
    User::where('id', $request->id)->update([
      'firstname'   => $request->firstname,
      'lastname'    => $request->lastname,
      'email'       => $request->email,
      'birth_date'  => $request->birth_date
    ]);
    return 'Done.';
  }




  /**
  * @param int $id
  * @return void
  */
  public static function delete(Request $request){
    $validatedData = $request->validate([
      'id' => 'required|exists:users,id'
    ]);
    User::destroy($request->id);
    return 'Done.';
  }
}
