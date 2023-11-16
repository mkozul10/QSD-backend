<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function categories(Request $request){
        $categories = DB::table("categories")
                ->select('*')
                ->get();

        if($categories->isEmpty()) {
            return response()->json([
                'msg'=> 'no data is found'
            ],404);
        }
        return response()->json([
            $categories
        ],200);            
    }

    public function addCategory(Request $request){
        $request->validate([
            "name"=> ['required','unique:categories,name']
        ]);
        $user = Auth::user();
        if($user->roles_id === 1){
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }

        $name = $request->name;

        $created = Category::create([
            "name"=> $name,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            "message" => "Name successfully added.",
            "name" => $created
        ]);
    }

    public function updateCategory(Request $request){
        $request->validate([
            "id" => 'required',
            "name" => ['unique:categories,name','required']
        ]);

        $user = Auth::user();

        if($user->roles_id === 1){
            return response()->json([
                "message" => "Unauthorized"
            ],401);
        }

        $category = DB::table('categories')
                ->where('id', $request->id)
                ->get();
        
        if($category->isEmpty()) {
            return response()->json([
                "message" => "Category with the given ID was not found."
            ],404);
        }

        $updated = Category::where('id',$request->id )->update([
            'name' => $request->name,
            'updated_at' => Carbon::now(),
        ]);

        $data = DB::table('categories')
                ->where('id', $request->id)
                ->get();
                
        return response()->json([
            "message" => "Category successfully updated.",
            "name" => $data
        ],200);
    }

    public function deleteCategory($id)
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

        $data = DB::table('categories')
                ->where('id', $id)
                ->get();
        if($data->isEmpty()) {
            return response()->json([
                "message" => "Category with the given ID was not found."
            ],404);
        }

        DB::table('categories')
                ->where('id', $data[0]->id)
                ->delete();

        return response()->json(['message' => "Category successfully deleted."],200);
    }
}
