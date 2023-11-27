<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Product;

class SearchController extends Controller
{
    public function search(SearchRequest $request){
        
        $productsQuery = Product::query();

        $search = $request->name;

        $productsQuery->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('gender', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%")
                ->orWhereHas('brand', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orWhereHas('color', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orWhereHas('sizes', function ($query) use ($search) {
                    $query->where('size', 'like', "%$search%");
                })
                ->orWhereHas('categories', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
        });
        
        $products = $productsQuery->paginate(10);
        $products->load(['color', 'brand', 'images', 'categories', 'sizes']);
        return response()->json($products,200);
    }
}
