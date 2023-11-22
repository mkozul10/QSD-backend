<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\ProductCategory;
use App\Models\ProductSize;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
    private function _checkForExistingPivot($categories, $sizes, $products_id){
        $check1 = [];
        $check2 = [];
        $result = [];
        for( $i = 0; $i < count($categories); $i++ ){
            $condition = DB::table('categories_products')
                        ->where('products_id', (int) $products_id)
                        ->where('categories_id', (int) $categories[$i])
                        ->exists();
            $check1[] = $condition;
        }

        for( $i = 0; $i < count($sizes); $i++ ){
            $condition = DB::table('products_sizes')
                        ->where('products_id', (int) $products_id)
                        ->where('sizes_id', (int) $sizes[$i]['size_id'])
                        ->exists();
            $check2[] = $condition;
        }

        if (in_array(true, $check1)) $result[] = false;
        else $result[] = true;

        if (in_array(true, $check2)) $result[] = false;
        else $result[] = true;

        return $result;
           
    }

    private function _createPivots($id, $categories, $sizes, $files){
        for( $i = 0; $i < count($categories); $i++ ){
            ProductCategory::create([
                'products_id' => $id,
                'categories_id' => (int) $categories[$i]
            ]);
        }

        for( $i = 0; $i < count($sizes); $i++ ){
            ProductSize::create([
                'products_id' => $id,
                'sizes_id' => $sizes[$i]['size_id'],
                'quantity' => $sizes[$i]['amount'],
            ]);
        }
        
        $user = Auth::user();
        foreach ($files as $file) {
            $uniqueFileName = uniqid("$user->name") . '.' . $file->getClientOriginalExtension();
            $file->storeAs('images', $uniqueFileName);
            Image::create([
                'name' => $uniqueFileName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'products_id' => $id
            ]);
        }
    }
    public function addProduct(ProductRequest $request){

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
        
        $this->_createPivots((int) $created->id, $categories, $sizes, $files);
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

    public function updateProduct(UpdateProductRequest $request){
        
        $id = $request->input('id');
        $files = $request->file('images');
        $categories = $request->input('categories');
        $sizes = $request->input('sizes');

        if(!$files) return response()->json([
            "message" => "You must add new image to product."
        ],400);

        $numOfImages = Image::where('products_id', '=', $id)->count();
        $addedFiles = count($files);
        if($numOfImages === 5) return response()->json(["message" => "You have 5 images stored, you can add only 0 more."]);
        else if($numOfImages + $addedFiles > 5){
            $difference = 5 - $numOfImages;
            return response()->json([
                "message" => "You have $numOfImages images stored, you can add only $difference more."
            ],400);
        } 

        $product = Product::find($id);

        $result = $this->_checkForExistingPivot($categories, $sizes, $product->id);

        if(!$result[0]) return response()->json([
            "message" => "This product is already in added category"
        ],400);

        if(!$result[1]) return response()->json([
            "message" => "This product already have this size"
        ],400);
        
        $this->_createPivots((int) $id, $categories, $sizes, $files);
        $product->update([
            'name' => $request->input('name'),
            'description'=> $request->input('description'),
            'price'=> $request->input('price'),
            'gender'=> $request->input('gender'),
            'updated_at' => Carbon::now(),
            'colors_id' => (int) $request->input('brand_id'),
            'brands_id' => (int) $request->input('color_id')
        ]);

        $product->color;
        $product->brand;
        $product->images;
        $product->categories;
        $product->sizes;
        return response()->json([
            'message' => "Successfully updated product.",
            $product
        ],200);
    }

    public function deleteProduct($id){
        $result = $this->_productValidation($id);
        
        if($result) return $result;

        $product = Product::find($id);

        $images = $product->images;

        foreach ($images as $image) {
            $filePath = 'images/' . $image->name;    
            Storage::delete($filePath);
        }

        $product->delete();

        return response()->json([
            "message" => "Product successfully deleted."
        ],200);
    }
}