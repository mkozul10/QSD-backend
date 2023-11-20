<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\User;

class UserController extends Controller
{
    public function users(){
        $users = DB::table("users")
                ->select('*')
                ->get();

        return response()->json([
            $users
        ],200);            
    }

    public function getUser($id){

        $user = DB::table('users')
                ->where('id', $request->id)
                ->first();
        
        if(!$user) {
            return response()->json([
                "message" => "User with the given ID was not found."
            ],404);
        }

        $user = DB::table("users")
                ->select('*')
                ->where('id', $request->id)
                ->first();

        return response()->json([
            $user
        ],200);            
    }

    public function updateUser(Request $request){
        $request->validate([
            "id" => 'required',
            "email" => ['unique:users,email','required']
        ]);

        $user = DB::table('users')
                ->where('id', $request->id)
                ->first();
        
        if(!$user) {
            return response()->json([
                "message" => "User with the given ID was not found."
            ],404);
        }

        $updated = User::where('id',$request->id )->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'city' => $request->city,
            'address' => $request->address,
            'phone' => $request->phone,
            'zip_code' => $request->zip_code,
            'updated_at' => Carbon::now(),
        ]);

        $data = DB::table('users')
                ->where('id', $request->id)
                ->first();
                
        return response()->json([
            "message" => "User successfully updated.",
            "user" => $data
        ],200);

 
    }

    
}
