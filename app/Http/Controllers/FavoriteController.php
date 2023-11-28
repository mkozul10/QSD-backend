<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteRequest;
use App\Models\User;
use Auth;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function handleFavorite(FavoriteRequest $request){
        $user = Auth::user();
        $favorite = Favorite::where("users_id", $user->id)->where("products_id", $request->product_id)
                    ->first();

        if(!$favorite){
            Favorite::create([
                "users_id"=> $user->id,
                "products_id"=> $request->product_id
            ]);
            return response()->json(["message" => "Favorite added successfully."],200);
        } else {
            $favorite->delete();
            return response()->json(["message" => "Favorite deleted successfully."],200);

        }
    }

    public function getFavorites(){
        $user = Auth::user();
        $columns = User::columns();
        $favorites = $user->load('favorites')
                    ->makeHidden($columns);

        return response()->json($user,200);
    }
}