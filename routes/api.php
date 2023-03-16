<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

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


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register');
    Route::post('forgotPassword','forgotPassword');
    Route::post('resetpassword','resetpassword')->name('password.reset');
    Route::middleware('auth:api')->group(function (){
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh'); 

        Route::group(['controller' => UserController::class], function () {
            // Route::get('', 'index')->middleware(['permission:view my profil|view all profil']);
            Route::put('updateNameEmail/{user}', 'updateNameEmail');
            Route::put('updatePassword/{user}', 'updatePassword');
            Route::put('changerole/{user}', 'changeRole');
        });

    });
});


//Routes for category:

// Route::apiResource('categories', CategoryController::class);

Route::group(['controller' => CategoryController::class, 'prefix' => 'categories','middleware'=>'auth:api'], function () {
    Route::get('', 'index')->middleware(['permission:view category']);
    Route::post('', 'store')->middleware(['permission:add category']);
    Route::get('/{category}', 'show')->middleware(['permission:view category']);
    Route::put('/{category}', 'update')->middleware(['permission:edit category']);
    Route::delete('/{category}', 'destroy')->middleware(['permission:delete category']);
});

//Routes for product:

Route::group(['controller' => ProductController::class, 'prefix' => 'products','middleware'=>'auth:api'], function () {
    Route::post('', 'store')->middleware(['permission:add product']);
    Route::put('/{product}', 'update')->middleware(['permission:edit All product|edit My product']);
    Route::delete('/{product}', 'destroy')->middleware(['permission:delete All product|delete My product']);
});

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/products/{product}', 'show');
    Route::get('/products/filter/category/{filter}','filterCategory');

});


// Routes for Role:
 

Route::group(['controller' => RoleController::class, 'prefix' => 'roles','middleware'=>'auth:api'], function () {
    Route::get('', 'index')->middleware(['permission:view role']);
    Route::post('', 'store')->middleware(['permission:add role']);
    Route::get('/{role}', 'show')->middleware(['permission:view role']);
    Route::put('/{role}', 'update')->middleware(['permission:edit role']);
    Route::delete('/{role}', 'destroy');
});


// Routes for Profil:

Route::group(['controller' => UserController::class, 'prefix' =>'users', 'middleware' => 'auth:api'], function () {
    Route::get('', 'index')->middleware(['permission:view my profil|view all profil']);
    // Route::put('updateNameEmail/{user}', 'updateNameEmail')->middleware(['permission:edit my profil|edit all profil']);
    // Route::put('updatePassword/{user}', 'updatePassword')->middleware(['permission:edit my profil|edit all profil']);
    Route::delete('/{user}', 'destroy')->middleware(['permission:delete my profil|delete all profil']);
    Route::put('changerole/{user}', 'changeRole')->middleware(['permission:change role user']);
});

