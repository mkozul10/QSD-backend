<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\Size;

class SizeController extends Controller
{
    public function sizes(Request $request){
        $sizes = DB::table("sizes")
                ->select('*')
                ->get();

        if($sizes->isEmpty()) {
            return response()->json([
                'msg'=> 'no data is found'
            ],404);
        }
        return response()->json([
            $sizes
        ],200);            
    }

    public function addSize(Request $request){
        $request->validate([
            "size"=> ['required','unique:sizes,size']
        ]);
        $user = Auth::user();
        if($user->roles_id === 1){
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }

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

        $user = Auth::user();

        if($user->roles_id === 1){
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }

        $size = DB::table('sizes')
                ->where('id', $request->id)
                ->get();
        
        if($size->isEmpty()) {
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
                ->get();
                
        return response()->json([
            "message" => "Size successfully updated.",
            "size" => $data
        ],200);
    }
}
