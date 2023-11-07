<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthController extends Controller
{

    public function Register(RegisterRequest $request){
        try{
            $user = User::create([
                'name' => $request->first_name,
                'surname' => $request->last_name,
                'email' => $request->email,
                'password'=> Hash::make($request->password),
                'roles_id' => 1,
            ]);

            return response()->json([
                'message' => "Registration successful",
            ],200);
        }
        catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ],500);
        }
    }

}
