<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Image;
use App\Models\ProductCategory;
use App\Models\ProductSize;
use Auth;
use DB;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function addProduct(ProductRequest $request){
        $user = Auth::user();

        $files = $request->file('images');
        $categories = $request->input('categories');
        $sizes = $request->input('sizes');

        $created = Product::create([
            'name' => $request->input('name'),
            'description'=> $request->input('description'),
            'price'=> $request->input('price'),
            'gender'=> $request->input('gender'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'colors_id' => (int) $request->input('brand_id'),
            'brands_id' => (int) $request->input('color_id')
        ]);
        for( $i = 0; $i < count($categories); $i++ ){
            ProductCategory::create([
                'products_id' => $created->id,
                'categories_id' => (int) $categories[$i]
            ]);
        }

        for( $i = 0; $i < count($sizes); $i++ ){
            ProductSize::create([
                'products_id' => $created->id,
                'sizes_id' => $sizes[$i]['size_id'],
                'quantity' => $sizes[$i]['amount'],
            ]);
        }
        
        foreach ($files as $file) {
            $uniqueFileName = uniqid("$user->name") . '.' . $file->getClientOriginalExtension();
            $file->storeAs('images', $uniqueFileName);
            Image::create([
                'name' => $uniqueFileName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'products_id' => $created->id
            ]);
        }
        $created->color;
        $created->brand;
        $created->images;
        $created->categories;
        $created->sizes;
        return response()->json($created,200);
    }
}