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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware' => 'warnNonWhitelistIp'], function () {
    Route::get('/', 'Api\ApiAuthController@index');

    Route::group(['middleware' => ['onlyWhitelistIp','api.log2']], function () {
        // User Controller
        Route::post('/lobby', 'Api\UserController@lobby')->name('lobby.v2');
        Route::post('/register', 'Api\UserController@register')->middleware('api.log')->name('register.v2');
        Route::post('/balance', 'Api\UserController@balance')->name('check.balance.v2');
        Route::post('/getAllPlayerBalance', 'Api\UserController@getAllPlayerBalance')->name('player.balance.v2');
        Route::post('/getOnlinePlayerCount', 'Api\UserController@getOnlinePlayerCount')->name('player.count.online.v2');
        Route::post('/checkPlayerIsOnline', 'Api\UserController@checkPlayerIsOnline')->name('player.is.online.v2');
        Route::post('/updatePlayerSetting', 'Api\UserController@updatePlayerSetting')->name('player.update.setting.v2');
        Route::post('/getGameToken', 'Api\UserController@getToken')->name('token.v2');
        Route::get('/error', 'Api\UserController@error')->name('error.v2');
        Route::any('/test', 'Api\UserController@test')->name('test.v2');

        // Bet Controller
        // Route::post('getBetDetails', 'Api\BetController@betDetails')->name('bet.details.v2');
        Route::post('getCurrentOutstandingBet', 'Api\BetController@outstandingBet')->name('outstanding.bet.v2');
        Route::post('getCurrentOutstandingBetDetail', 'Api\BetController@outstandingBetDetails')->name('outstanding.bet.details.v2');
        Route::post('/getDailyWinLose', 'Api\BetController@winlose');

        // Transaction Controller
        Route::post('/transfer', 'Api\TransactionController@transfer')->middleware('api.log')->name('transfer.v2');
        Route::post('/check_trans', 'Api\TransactionController@check_trans')->middleware('api.log')->name('check.transfer.v2');
        Route::any('/getTransResult', 'Api\TransactionController@transResults')->name('transaction.result.v2');
        //Route::post('/getTransDetails', 'Api\TransactionController@transDetails')->name('transaction.details.v2');
        Route::post('/getInvoiceTogel', 'Api\TransactionController@invoiceTogel')->name('transaction.invoice.togel.v2');
        Route::post('/getInvoiceTogelperUsername', 'Api\TransactionController@invoiceTogelperUsername')->name('transaction.invoice.togel.per.username.v2');

        // Game Controller
        Route::post('/getJackpot', 'Api\GameController@jackpot')->name('jackpot.v2');
        Route::post('/getNumberResults', 'Api\GameController@numberResults')->name('number.result.v2');
        Route::post('/getNumberDetails', 'Api\GameController@numberDetails')->name('number.details.v2');
        Route::post('/getMarketTime', 'Api\GameController@getMarketTime')->name('market.time.v2');
        Route::post('/getTableName', 'Api\GameController@getTableName')->name('game.table.v2');
        Route::post('/getGameTurnover', 'Api\GameController@getGameTurnover')->name('game.turnover.v1');
        Route::post('/getAllNumberResults', 'Api\GameController@allnumberResults')->name('allnumber.result.v1');

        // Referral Controller
        Route::post('/getReferral', 'Api\ReferralController@referral')->name('referral.v2');
        Route::post('/getTurnover', 'Api\ReferralController@turnover')->name('turnover.v2');
        Route::post('/getBonusReferral', 'Api\ReferralController@getBonusReferral')->name('referral.bonus.v2');
        Route::post('/getReferralPerDay', 'Api\ReferralController@getReferralPerDay')->name('referral.daily.v2');
        Route::post('/getDownline', 'Api\ReferralController@getDownline')->name('downline.v2');

    });
    
    Route::prefix('auth')->group(function () {
        // Authentication
        Route::get('/login', 'Api\ApiAuthController@login')->name('api.login.v2');
        Route::post('/login', 'Api\ApiAuthController@doLogin');
        

        Route::get('/logout', 'Api\ApiAuthController@logout');
    });
});

Route::get('testlbcf', 'Api\UserController@testlbcf');
Route::get('testhash', 'Api\UserController@testhash');
Route::get('/login_apk', 'Api\UserController@show_apk');