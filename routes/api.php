<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\SizeController;

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
});
*/

Route::post('/register', [AuthController::class, 'Register']);
Route::post('/login', [AuthController::class, 'Login']);
Route::post('/requestValidationKey', [AuthController::class,'requestValidationKey']);
Route::post('/resetPassword', [AuthController::class,'resetPassword']);
Route::get('/sizes', [SizeController::class, 'sizes']);
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class,'Logout']);
    Route::post('/refresh', [AuthController::class,'Refresh']);
    Route::post('/changePassword', [AuthController::class,'changePassword']);
    Route::post('/addSize', [SizeController::class, 'addSize']);
    Route::put('/updateSize', [SizeController::class, 'updateSize']);
    Route::delete('/deleteSize/{id}', [SizeController::class,'deleteSize']);
});
