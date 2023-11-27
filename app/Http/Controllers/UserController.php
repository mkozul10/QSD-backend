<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests\UserRequest;
use App\Http\Requests\updateRoleRequest;

class UserController extends Controller
{
    
    public function users(){

      $count = User::where('roles_id', '3')->count();
      $users = User::with('role')->get();

        return response()->json([
            "Number of superAdmins"=> $count,
            "Users" => $users
        ],200);            

    }


    public function getUser(){

        $count = User::where('roles_id', '3')->count();

        $user = Auth::user();
        $user->role;
        
        return response()->json([
            $user
    
        ],200);            

    }


public function updateUser(UserRequest $request){

    $request->validated();

    $updated = Auth::user()->update([
        'name' => $request->first_name,
        'surname' => $request->last_name,
        'email' => $request->email,
        'city' => $request->city,
        'address' => $request->address,
        'phone' => $request->phone,
        'zip_code' => $request->zip_code,
        'updated_at' => Carbon::now(),
    ]);

    
    $data = Auth::user();
    $data->role;
            
    return response()->json([
        "message" => "User successfully updated.",
        "user" => $data
    ],200);

}

public function deleteUser($id){
    $user = User::find($id);

    if($user){
        $user->delete();
        return response()->json([
            "message" => "User successfully deleted",
        ],200);
    }
    
    else{
        return response()->json([
            "message" => "User not found",
        ],404);
    }
}



public function banUser($id) {
    $user = User::find($id);
    

    if(!$user){
        return response()->json([
            "message" => "User not found.",
        ],404);
    }
  
    if($user-> roles_id == 3)
    {
        //ako je ispod dvije funkcije, status mu se ne mijenja ali pise da mijenja, dok ako je iznad obratna je prica(mijenja status, dok pise da ne mijenja)
        return response()->json([
            'status' =>3,
            "message" => "Super admin cannot be banned.",
        ],403);
    }

    if($user->status == 0)
    {
        $user->update([
            'status' => 1
        ]);
    
        return response()->json([
            "message" => "User successfully unbanned",
        ],200);
    }

    else
    {
        $user->update([
            'status' => 0
        ]);
    
        return response()->json([
            "message" => "User successfully banned",
        ],200);
    }

  
        
}

public function updateRole(updateRoleRequest $validiraj){

    $validiraj->validated();

    $user = User::find($validiraj->user_id);

    $count = User::where('roles_id', '3')->count();

    if($user->roles_id == 3) {
        if ($count == 1)
         {
            return response()->json([
                'roles_id' =>3,
                "message" => "Last superAdmin canot be changed",
        
                    ],403);
         }
        
    }

    if(!$user){

        return response()->json([
            "message" => "No user was found with that id",
            
        ],404);
    }

     else
     {
        $user->update([
            'roles_id' => $validiraj->role_id
           
        ]);

        return response()->json([
            "message" => "Role suscesfuly updated"
        ],200);

    }
}

}
