<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\Size;

class SizeController extends Controller
{
    public function sizes(){
        $sizes = DB::table("sizes")
                ->select('*')
                ->get();

        return response()->json([
            $sizes
        ],200);            
    }

    public function addSize(Request $request){
        $request->validate([
            "size"=> ['required','unique:sizes,size']
        ]);

        $size = $request->size;

        $created = Size::create([
            "size"=> $size,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            "message" => "Size successfully added.",
            "size" => $created
        ]);
    }

    public function updateSize(Request $request){
        $request->validate([
            "id" => 'required',
            "size" => ['unique:sizes,size','required']
        ]);

        $size = DB::table('sizes')
                ->where('id', $request->id)
                ->first();
        
        if(!$size) {
            return response()->json([
                "message" => "Size with the given ID was not found."
            ],404);
        }

        $updated = Size::where('id',$request->id )->update([
            'size' => $request->size,
            'updated_at' => Carbon::now(),
        ]);

        $data = DB::table('sizes')
                ->where('id', $request->id)
                ->first();
                
        return response()->json([
            "message" => "Size successfully updated.",
            "size" => $data
        ],200);
    }

    public function deleteSize($id)
    {
        $validator = validator(['id' => $id], [
            'id' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $data = DB::table('sizes')
                ->where('id', $id)
                ->first();
        if(!$data) {
            return response()->json([
                "message" => "Size with the given ID was not found."
            ],404);
        }

        DB::table('sizes')
                ->where('id', $data[0]->id)
                ->delete();

        return response()->json(['message' => "Size successfully deleted."],200);
    }
}
