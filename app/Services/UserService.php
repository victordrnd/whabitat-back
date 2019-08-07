<?php

namespace App\Services;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserService{

    public function create($request){
        return User::create([
            'firstname'   => $request->firstname,
            'lastname'    => $request->lastname,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'birth_date'  => $request->birth_date,
            'profil_id' => 3,
            'creator_id'  => auth()->user()->id
          ]);
    }
}