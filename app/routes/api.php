<?php

use App\Http\Controllers\Api\Auth\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth/login')
    ->controller( UserAuthController::class)
    ->group(function() {


    Route::post('step-one', 'stepOne');
    Route::post('step-two', 'stepTwo');

});
