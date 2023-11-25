<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function categories(){
        $categories = DB::table("categories")
                ->select('*')
                ->get();

        return response()->json($categories,200);            
    }

    public function addCategory(Request $request){
        $request->validate([
            "name"=> ['required','unique:categories,name']
        ]);

        $name = $request->name;

        $created = Category::create([
            "name"=> $name,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            "message" => "Category successfully added.",
            "name" => $created
        ]);
    }

    public function updateCategory(Request $request){
        $request->validate([
            "id" => 'required',
            "name" => ['unique:categories,name','required']
        ]);

        $category = DB::table('categories')
                ->where('id', $request->id)
                ->first();
        
        if(!$category) {
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
                ->first();
                
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

        $data = DB::table('categories')
                ->where('id', $id)
                ->first();
        if(!$data) {
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
