<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\AuthMail;
use App\Models\User;
use App\Models\UsersValidationKeys;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;

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
                DB::table('users_validation_keys')
                    ->where('users_id', '=', $user[0]->id)
                    ->select('*')
                    ->delete();
                $number = $this->generateNumber();
                DB::table('users_validation_keys')
                    ->insert([
                        'validation_key' => $number,
                        'users_id' => $user[0]->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                Mail::send('mail.validate',['number' => $number,'user' => $user[0]], function ($message) use ($user) {
                    $message->from('qsdwebshop@gmail.com', 'QSD WebShop')
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

    public function Logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function requestValidationKey(Request $request){
        $request->validate([
            "email" => "required|email|exists:users,email"
        ]);
        $user = DB::table("users")
                ->where("email", $request->email)
                ->first();
        if(!$user->status) return response()->json(["message" => "Your account is banned."],401);

        $oldKey = DB::table("users_validation_keys")
                    ->where('users_id', $user->id)
                    ->first();

        if($oldKey){
            DB::table("users_validation_keys")
                    ->where('users_id', $user->id)
                    ->delete();
        }

        $newKey = $this->generateNumber();
        UsersValidationKeys::create([
            'validation_key' => $newKey, 
            'users_id' => $user->id
        ]);

        Mail::send('mail.validate',['number' => $newKey,'user' => $user], function ($message) use ($user) {
            $message->from('qsdwebshop@gmail.com', 'QSD WebShop')
                ->to($user->email, $user->name) 
                ->subject('QSD Verification code');
        });

        return response()->json([
            "message" => "Validation key has been sent to your email address!"
        ],200);
    }

    public function Refresh(Request $request){
        $user = Auth::user();
        $request->user()->token()->revoke();
        $token = $user->createToken("qsdWebShop")->accessToken;
        $user = Auth::user();
        $user->role;
        return response()->json([
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'Bearer',
            ]
        ],200);
    }

    public function resetPassword(Request $request){
        $request->validate([
            "email"=> "required|email|exists:users,email",
            'password'=> ['required', 'confirmed', Password::min(8)->letters()->symbols()],
            "key" => ["required",'numeric','digits:6']
        ]);
        $keyFromDB = DB::table('users_validation_keys')
                    ->where('validation_key','=', $request->key)
                    ->first();
        if($keyFromDB !== null){
            if($keyFromDB->validation_key === (int)$request->key){
                if(Carbon::parse($keyFromDB->created_at)->addHours(2)->isPast()){
                    return response()->json([
                        "error" => "Validation key has expired."
                    ],401);
                }
                DB::table("users")
                    ->where("email", $request->email)
                    ->update(['password' => Hash::make($request->password)]);
                return response()->json([
                    "message" => "New password set successfully!"
                ],200);
            }
        }
        return response()->json([
            "error" => "Invalid validation key."
        ],400);
    }

    public function changePassword(Request $request){
        $request->validate([
            'old_password' => 'required',
            'new_password' => ['required', Password::min(8)->letters()->symbols()]
        ]);

        $user = Auth::user();
        if(Hash::check($request->old_password, $user->getAuthPassword())){
            if(Hash::check($request->new_password, $user->getAuthPassword())){
                return response()->json([
                    "message" => "New password must be different from than last one."
                ],400);
            }
            DB::table('password_resets')
                ->insert([
                    'users_id' => $user->id,
                    'old_password' => $request->old_password,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            DB::table("users")
                ->where("id", $user->id)
                ->update(['password' => Hash::make($request->new_password)]);
        
            return response()->json(["message" => "New password set successfully!"],200);
        }
        else return response()->json(["message" => "Wrong the old password."],403);
    }
}