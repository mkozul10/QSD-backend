<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
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

    public function Login(LoginRequest $request){
        try{
            if(Auth::attempt($request->only("email","password"))){
                $user = Auth::user();
                $token = $user->createToken("qsdWebShop")->accessToken;
                $user->role;


                return response()->json([
                    'user' => $user,
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ],200);
            }
        } catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
        return response()->json([
            'message' => 'Ivalid email or error'
        ],401);
    }

    public function Logout(Request $request)
{
    $request->user()->token()->revoke();
    return response()->json(['message' => 'Successfully logged out'], 400);
}

}
