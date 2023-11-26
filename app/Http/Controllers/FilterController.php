<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\Product;

class FilterController extends Controller
{
    public function filter(FilterRequest $request){

        $productsQuery = Product::query();

        if ($request->colors) $productsQuery->whereIn('colors_id', $request->colors);

        if ($request->genders) $productsQuery->whereIn('gender', $request->genders);

        if ($request->brands) $productsQuery->whereIn('gender', $request->brands);

        if ($request->min_price) $productsQuery->where('price', '>=', (int) $request->min_price);

        if ($request->max_price) $productsQuery->where('price', '<=', (int) $request->max_price);

        if ($request->sizes) {
            $productsQuery->whereHas('sizes', function ($query) use ($request) {
                $query->whereIn('sizes_id', $request->sizes);
            });
        }

        if ($request->categories) {
            $productsQuery->whereHas('categories', function ($query) use ($request) {
                $query->whereIn('categories_id', $request->categories);
            });
        }

        $products = $productsQuery->paginate(10);
        $products->load(['color', 'brand', 'images', 'categories', 'sizes']);
        return response()->json($products,200);
    }
}