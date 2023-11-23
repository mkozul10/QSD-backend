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




class UserController extends Controller
{
    
    public function users(){

      $users = User::with('role')->get();

        return response()->json([
            $users
        ],200);            

    }


    public function getUser(){

        $user = Auth::user();
        $user->role;
        
        return response()->json([
            $user
    
        ],200);            

    }

    public function updateUser(Request $request){

        $validator = Validator::make($request->all(),[
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'email' => ['required',Rule::unique('users'),'string'],
            'phone' => 'string',
            'city' => 'string',
            'address' => 'string',
            'zip_code' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }


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

    public function banUser($id){
                
        $validator = validator(['id' => $id], [
            'id' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

          $data = DB::table('users')
                ->where('id', $id)
                ->first();
        if(!$data) {
            return response()->json([
                "message" => "User with the given ID was not found."
            ],404);
        }

    }

    public function updateRole(){
                
    }

    
}
