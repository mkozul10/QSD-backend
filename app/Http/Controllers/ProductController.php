<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRateProductRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\RateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductRating;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    private function handleFiles($files, $id){
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
        
        $this->handleFiles($files, $created->id);
        $created->categories()->sync($categories);
        $created->sizes()->sync($sizes);

        $created->load(['color', 'brand', 'images', 'categories', 'sizes']);
        return response()->json($created,200);
    }

    public function getProducts(Request $request){
        $request->validate([
            "page" => ['integer', 'min:1']
        ]);
        $perPage = $request->input('page', 10);
        $products = Product::with(['color','brand','images','categories','sizes', 'ratings'])
                    ->paginate($perPage);
        
        return response()->json($products,200);
    }

    public function getProduct($id){
        $result = $this->_productValidation($id);
        
        if($result) return $result;

        $product = Product::with(['color','brand','images','categories','sizes', 'ratings'])->find($id);
    
        return response()->json($product,200);
    }

    public function updateProduct(UpdateProductRequest $request){
        
        $id = $request->input('id');
        $files = $request->file('images');
        $categories = $request->input('categories');
        $sizes = $request->input('sizes');

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
        
        $product->update([
            'name' => $request->input('name'),
            'description'=> $request->input('description'),
            'price'=> $request->input('price'),
            'gender'=> $request->input('gender'),
            'updated_at' => Carbon::now(),
            'colors_id' => (int) $request->input('brand_id'),
            'brands_id' => (int) $request->input('color_id')
        ]);
        $this->handleFiles($files,$product->id);

        $product->categories()->sync($categories);
        $product->sizes()->sync($sizes);

        $product->load(['color', 'brand', 'images', 'categories', 'sizes']);
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

    public function deleteImage($id){
        $validator1 = validator(['id' => $id], [
            'id' => 'required|numeric|min:1',
        ]);
        if ($validator1->fails()) {
            return response()->json(['error' => $validator1->errors()], 422);
        }
        $validator2 = validator(['id' => $id], [
            'id' => ['exists:images,id']
        ]);
        if ($validator2->fails()) {
            return response()->json(["message" => "Image not found."], 404);
        }
        
        $image = Image::find($id);

        $related_product = Product::find($image->products_id);
        $related_images = $related_product->images;
        if(count($related_images) === 1 && $related_images[0]->id === $image->id){
            return response()->json(["message" => "You can't delete the last image"],403);
        }

        $filePath = 'images/' . $image->name;
        $image->delete();
        Storage::delete($filePath);

        return response()->json(["message" => "Image successfully deleted."],200);
    }

    public function rateProduct(RateProductRequest $request){
        $user = Auth::user();

        $condition = DB::table('products_ratings')
                        ->where('products_id', $request->product_id)
                        ->where('users_id', $user->id)
                        ->exists();
        if ($condition) {
            $rating = ProductRating::where('users_id', $user->id)
                        ->where('products_id', $request->product_id)
                        ->first();

            if(!$request->description){
                $rating->update([
                    'rating' => $request->rating
                ]);
            } else {
                $rating->update([
                    'rating' => $request->rating,
                    'review'=> $request->description
                ]);
            }
            return response()->json(['message'=> 'Rating updated successfully'],200);
        }

        if(!$request->description){   
            $created = ProductRating::create([
                'users_id' => $user->id,
                'products_id' => $request->product_id,
                'rating' => $request->rating
            ]);
        } else{
            $created = ProductRating::create([
                'users_id' => $user->id,
                'products_id' => $request->product_id,
                'rating' => $request->rating,
                'review'=> $request->description
            ]);
        }
        return response()->json(["message" => "Rating saved successfully."],200);
    }

    public function editRateProduct(EditRateProductRequest $request){
        $rating = ProductRating::find($request->rate_id);
        if(!$request->description){
            $rating->update([
                'rating' => $request->rating
            ]);
        } else {
            $rating->update([
                'rating' => $request->rating,
                'review'=> $request->description
            ]);
        }
        return response()->json([
            'message'=> 'Rating updated successfully'
        ],200);

    }
}