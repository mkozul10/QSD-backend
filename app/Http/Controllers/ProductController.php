<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\ProductCategory;
use App\Models\ProductSize;
use Auth;
use DB;
use Carbon\Carbon;

class ProductController extends Controller
{

    private function _productValidation($id){
        $validator1 = validator(['id' => $id], [
            'id' => 'required|numeric|min:1',
        ]);
        if ($validator1->fails()) {
            return response()->json(['error' => $validator1->errors()], 422);
        }

        $validator2 = validator(['id' => $id], [
            'id' => 'exists:products,id',
        ]);
        if ($validator2->fails()) {
            return response()->json(["message" => "Product with the given ID was not found."], 404);
        }
    }
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

    public function getProducts(Request $request){
        $request->validate([
            "page" => ['integer', 'min:1']
        ]);
        $perPage = $request->input('page', 10);
        $products = Product::with(['color','brand','images','categories','sizes'])
                    ->paginate($perPage);
        
        return response()->json($products,200);
    }

    public function getProduct($id){
        $result = $this->_productValidation($id);
        
        if($result) return $result;

        $product = Product::with(['color','brand','images','categories','sizes'])->find($id);
    
        return response()->json($product,200);
    }
}