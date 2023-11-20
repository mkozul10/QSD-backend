<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use Auth;

class ProductController extends Controller
{
    public function addProduct(ProductRequest $request){
        //file handling 
        $user = Auth::user();
        $files = $request->file('images');
        $fileDetails = [];
        foreach ($files as $file) {
            $uniqueFileName = uniqid("$user->name") . '.' . $file->getClientOriginalExtension();
            $file->storeAs('images', $uniqueFileName);
            $fileDetails[] = [
                'name' => $uniqueFileName,
                'extension' => $file->getClientOriginalExtension(),
                'path' => 'storage/images/' . $uniqueFileName
            ];
        }
        return response()->json($fileDetails);
    }
}
