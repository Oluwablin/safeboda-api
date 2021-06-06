<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\HttpLogger\Middlewares\HttpLogger;
use App\Http\Controllers\v1\Auth\AuthController;
use App\Http\Controllers\v1\Auth\LoginController;
use App\Http\Controllers\v1\Promo\PromoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['HttpLogger'], 'prefix' => 'v1', 'namespace' => 'v1'], function ($router) {

    /** TESTING */
    Route::get('/test', function() {
        return 'Hello Safeboda test';
    });

    /*** REGISTRATION FOR USERS ***/
    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function ($router) {

        Route::post('/register', [AuthController::class, 'register']);

        Route::post('/login', [AuthController::class, 'login']);
    });

    /*** AUTHENTICATION FOR USERS ***/
    Route::group(['middleware' => ['auth'], 'prefix' => 'auth', 'namespace' => 'Auth'], function ($router) {

        Route::post('/logout', [LoginController::class, 'logout']);

        Route::get('/me', [LoginController::class, 'me']);
    });

    /*** PROMO MODULE ***/
    Route::group(['middleware' => ['auth'], 'prefix' => 'promos', 'namespace' => 'Promo'], function ($router) {

        Route::get('/', [PromoController::class, 'index']);

        Route::post('/create', [PromoController::class, 'store']);

        Route::put('/update/{id}', [PromoController::class, 'update']);

        Route::delete('/delete/{id}', [PromoController::class, 'destroy']);

        Route::get('/active', [PromoController::class, 'active']);

        Route::get('/expired', [PromoController::class, 'expired']);

        Route::post('/deactivate/{id}', [PromoController::class, 'deactivate']);

        Route::get('/deactivated', [PromoController::class, 'deactivated']);
    });


    Route::post('promos/verify/{code}', [PromoController::class, 'show']);


    /**
     * THIS SHOULD ALWAYS BE THE ENDING OF THIS PAGE
     */
    Route::fallback(function () {
        return response()->json([
            'error' => true,
            'message' => 'Route don\'t exist',
            'data' => null
        ], 404);
    });

});
