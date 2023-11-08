<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\AuthMail;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    private function generateNumber(){
        $min = 100000;
        $max = 999999;

        $randomNumber = rand($min, $max);
        $randomNumber = str_pad($randomNumber, 6, '0', STR_PAD_LEFT);
        return $randomNumber;
    }

    private function deleteKey($key){
        DB::table('users_validation_keys')
            ->where('validation_key', $key)
            ->delete();
    }

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
        if(Auth::attempt($request->only("email","password"))){
            $user = DB::table('users')
                    ->where('email', '=', $request->email)
                    ->get();
            if($request->has('key')){

                $key = (int)$request->key;
                
                $keyFromDB = DB::table('users_validation_keys')
                    ->where('users_id', '=', $user[0]->id)
                    ->select('validation_key','created_at')
                    ->first();
                if($keyFromDB !== null){
                    if($key === $keyFromDB->validation_key){
                        $createdAt = Carbon::parse($keyFromDB->created_at)->addHours(2);
                        if(!$createdAt->isPast()){
                            $this->deleteKey($keyFromDB->validation_key);

                            if($user[0]->status){
                                $user = Auth::user();
                                $token = $user->createToken("qsdWebShop")->accessToken;
                                $user->role;
                                return response()->json([
                                    'user' => $user,
                                    'authorization' => [
                                        'token' => $token,
                                        'type' => 'Bearer',
                                    ]
                                ],200);
                            }
                        }
                        else{
                            $this->deleteKey($keyFromDB->validation_key);
                            return response()->json([
                                "error"=> "Validation key has expired."
                            ],401);
                        }
                    } else {
                        return response()->json([
                            "error" => "Invalid validation key."
                        ],400);
                    }
                }
                else return response()->json(["error" => "Invalid validation key."],400);
            } else {
                $number = $this->generateNumber();
                DB::table('users_validation_keys')
                    ->insert([
                        'validation_key' => $number,
                        'users_id' => $user[0]->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                Mail::send('mail.validate',['number' => $number,'user' => $user], function ($message) use ($user) {
                    $message->from('qsdshop@gmail.com', 'QSD WebShop')
                        ->to($user[0]->email, $user[0]->name) 
                        ->subject('QSD Verification code');
                });

                return response()->json([
                    'message' => 'Verification code sent to your email'
                ]);
            }
        } else {
            return response()->json([
                "message" => "There was an error with your email or password. Please try again."
            ],401);
        }
        
    }

    public function Logout(Request $request)
{
    $request->user()->token()->revoke();
    return response()->json(['message' => 'Successfully logged out'], 200);
}

}
