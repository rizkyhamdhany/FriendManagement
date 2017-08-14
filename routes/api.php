<?php

use Illuminate\Http\Request;

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

Route::prefix('/v1')->group(function () {
    Route::get('/user', 'API\FriendController@index')->name('friend.index');
    Route::post('/friend_connect', 'API\FriendController@friendConnect')->name('friend.connect');
    Route::post('/friend_list', 'API\FriendController@friendList')->name('friend.list');
    Route::post('/common_friend', 'API\FriendController@commonFriend')->name('friend.common');
});

