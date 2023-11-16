<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\Color;

class ColorController extends Controller
{
    public function colors(Request $request){
        $colors = DB::table("colors")
                ->select('*')
                ->get();

        if($colors->isEmpty()) {
            return response()->json([
                'msg'=> 'no data is found'
            ],404);
        }
        return response()->json([
            $colors
        ],200);            
    }

    public function addColor(Request $request){
        $request->validate([
            "name"=> ['required','unique:colors,name'],
            "hex_code"=> ['required','unique:colors,hex_code']
        ]);
        $user = Auth::user();
        if($user->roles_id === 1){
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }

        $color = $request->color;

        $created = Color::create([
            "name"=> $name,
            "hex_code"=> $hex_code,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            "message" => "Color successfully added.",
            "name" => $created
            //"hex_code" => $created
        ]);
    }

    public function updateColor(Request $request){
        $request->validate([
            "id" => 'required',
            "name" => ['unique:colors,name','required'],
            "hex_code"=> ['required','unique:colors,hex_code']
        ]);

        $user = Auth::user();

        if($user->roles_id === 1){
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }

        $color = DB::table('colors')
                ->where('id', $request->id)
                ->get();
        
        if($color->isEmpty()) {
            return response()->json([
                "message" => "Color with the given ID was not found."
            ],404);
        }

        $updated = Color::where('id',$request->id )->update([
            'name' => $request->name,
            'hex_code' => $request->hex_code,
            'updated_at' => Carbon::now(),
        ]);

        $data = DB::table('colors')
                ->where('id', $request->id)
                ->get();
                
        return response()->json([
            "message" => "Color successfully updated.",
            "name" => $data
        ],200);
    }

    public function deleteColor($id)
    {
        $validator = validator(['id' => $id], [
            'id' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = Auth::user();
        if($user->roles_id === 1){
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }

        $data = DB::table('colors')
                ->where('id', $id)
                ->get();
        if($data->isEmpty()) {
            return response()->json([
                "message" => "Color with the given ID was not found."
            ],404);
        }

        DB::table('colors')
                ->where('id', $data[0]->id)
                ->delete();

        return response()->json(['message' => "Color successfully deleted."],200);
    }
}




