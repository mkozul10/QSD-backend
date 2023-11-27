<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\Color;

class ColorController extends Controller
{
    public function colors(){
        $colors = DB::table("colors")
                ->select('*')
                ->get();

        return response()->json($colors,200);            
    }

    public function addColor(Request $request){
        $request->validate([
            "name"=> ['required','unique:colors,name'],
            "hex_code"=> ['required','unique:colors,hex_code']
        ]);

        $color = $request->name;
        $hex_code = $request->hex_code;

        $created = Color::create([
            "name"=> $color,
            "hex_code"=> $hex_code,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            "message" => "Color successfully added.",
            "name" => $created
        ]);
    }

    public function updateColor(Request $request){
        $request->validate([
            "id" => 'required',
            "name" => ['unique:colors,name','required'],
            "hex_code"=> ['required','unique:colors,hex_code']
        ]);

        $color = DB::table('colors')
                ->where('id', $request->id)
                ->first();
        
        if(!$color) {
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
                ->first();
                
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

        $data = DB::table('colors')
                ->where('id', $id)
                ->first();
        if(!$data) {
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




