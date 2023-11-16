<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\Brand;

class BrandController extends Controller
{
    public function brands(Request $request){
        $brands = DB::table("brands")
                ->select('*')
                ->get();

        if($brands->isEmpty()) {
            return response()->json([
                'msg'=> 'no data is found'
            ],404);
        }
        return response()->json([
            $brands
        ],200);            
    }

    public function addBrand(Request $request){
        $request->validate([
            "name"=> ['required','unique:brands,name']
        ]);
        $user = Auth::user();
        if($user->roles_id === 1){
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }

        $name = $request->name;

        $created = Brand::create([
            "name"=> $name,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            "message" => "Name successfully added.",
            "name" => $created
        ]);
    }

    public function updateBrand(Request $request){
        $request->validate([
            "id" => 'required',
            "name" => ['unique:brands,name','required']
        ]);

        $user = Auth::user();

        if($user->roles_id === 1){
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }

        $brand = DB::table('brands')
                ->where('id', $request->id)
                ->get();
        
        if($brand->isEmpty()) {
            return response()->json([
                "message" => "Brand with the given ID was not found."
            ],404);
        }

        $updated = Brand::where('id',$request->id )->update([
            'name' => $request->name,
            'updated_at' => Carbon::now(),
        ]);

        $data = DB::table('brands')
                ->where('id', $request->id)
                ->get();
                
        return response()->json([
            "message" => "Brand successfully updated.",
            "name" => $data
        ],200);
    }

    public function deleteBrand($id)
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

        $data = DB::table('brands')
                ->where('id', $id)
                ->get();
        if($data->isEmpty()) {
            return response()->json([
                "message" => "Brand with the given ID was not found."
            ],404);
        }

        DB::table('brands')
                ->where('id', $data[0]->id)
                ->delete();

        return response()->json(['message' => "Brand successfully deleted."],200);
    }
}
