<?php

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
})
*/


Route::post('/register', [AuthController::class, 'Register']);
Route::post('/login', [AuthController::class, 'Login']);
Route::post('/requestValidationKey', [AuthController::class,'requestValidationKey']);
Route::post('/resetPassword', [AuthController::class,'resetPassword']);


Route::get('/filterProducts', [FilterController::class,'filter']);
Route::post('/search', [SearchController::class,'search']);


Route::get('/sizes', [SizeController::class, 'sizes']);
Route::get('/colors', [ColorController::class, 'colors']);
Route::get('/categories', [CategoriesController::class, 'categories']);
Route::get('/brands', [BrandController::class, 'brands']);


Route::get('/getProducts', [ProductController::class,'getProducts']);
Route::get('/getProduct/{id}', [ProductController::class,'getProduct']);


Route::get('/users', [UserController::class, 'users']);
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class,'Logout']);
    Route::post('/refresh', [AuthController::class,'Refresh']);
    Route::post('/changePassword', [AuthController::class,'changePassword']);


    Route::middleware('isBanned')->post('/handleFavorite', [FavoriteController::class,'handleFavorite']);
    Route::middleware('isBanned')->get('/getFavorites', [FavoriteController::class,'getFavorites']);
    Route::middleware('isBanned')->post('/rateProduct', [ProductController::class,'rateProduct']);


    Route::middleware('isBanned')->post('/payment', [OrderController::class, 'payment']);
    Route::middleware('isBanned')->get('/getOrdersPerUser', [OrderController::class, 'getOrdersPerUser']);


    Route::get('/getUser', [UserController::class, 'getUser']);
    Route::middleware('isBanned')->post('/updateUser', [UserController::class, 'updateUser']);


    Route::middleware(['authorization', 'isBanned'])->group(function () {
        Route::post('/addSize', [SizeController::class, 'addSize']);
        Route::put('/updateSize', [SizeController::class, 'updateSize']);
        Route::delete('/deleteSize/{id}', [SizeController::class,'deleteSize']);

        Route::post('/addColor', [ColorController::class, 'addColor']);
        Route::put('/updateColor', [ColorController::class, 'updateColor']);
        Route::delete('/deleteColor/{id}', [ColorController::class,'deleteColor']);

        Route::post('/addCategory', [CategoriesController::class, 'addCategory']);
        Route::put('/updateCategory', [CategoriesController::class, 'updateCategory']);
        Route::delete('/deleteCategory/{id}', [CategoriesController::class,'deleteCategory']);

        Route::post('/addBrand', [BrandController::class, 'addBrand']);
        Route::put('/updateBrand', [BrandController::class, 'updateBrand']);
        Route::delete('/deleteBrand/{id}', [BrandController::class,'deleteBrand']);

        Route::post('/addProduct', [ProductController::class,'addProduct']);
        Route::post('/updateProduct', [ProductController::class,'updateProduct']);
        Route::delete('/deleteProduct/{id}', [ProductController::class,'deleteProduct']);
        Route::delete('/deleteImage/{id}', [ProductController::class,'deleteImage']);

        Route::delete('/deleteUser/{id}', [UserController::class,'deleteUser']);
        Route::post('/banUser/{id}', [UserController::class,'banUser']);
        Route::post('/updateRole', [UserController::class,'updateRole']);

        Route::post('/editRateProduct', [ProductController::class,'editRateProduct']);

        Route::middleware('isBanned')->post('/updateState', [OrderController::class, 'updateState']);

        Route::middleware('isBanned')->get('/getOrders', [OrderController::class, 'getOrders']);
});
});
