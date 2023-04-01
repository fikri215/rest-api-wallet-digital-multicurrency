<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => 'api', 'prefix' => 'v1/user'], function () {
    Route::get('get-users', 'API\UserController@fetchAllUser');
    Route::get('get-user', 'API\UserController@getUser');
    Route::post('create', 'API\UserController@store');
    Route::post('update', 'API\UserController@update');
    Route::post('delete', 'API\UserController@destroy');
});

Route::group(['middleware' => 'api', 'prefix' => 'v1/wallet'], function () {
    Route::get('get-user-wallet', 'API\WalletController@getWallet');
    Route::patch('change-currency', 'API\WalletController@changeCurrency');
    Route::post('topup', 'API\WalletTransactionController@topup');
    Route::post('transfer', 'API\WalletTransactionController@transfer');
});

Route::group(['middleware' => 'api', 'prefix' => 'v1/exchange-rate'], function () {
    Route::post('create', 'API\CurrencyExchangeRateController@setExchangeRate');
    Route::post('update', 'API\CurrencyExchangeRateController@updateExchangeRate');
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('authenticate', 'AuthController@authenticate')->name('api.authenticate');
    Route::post('register', 'AuthController@register')->name('api.register');
    Route::post('logout', 'AuthController@logout');
});
