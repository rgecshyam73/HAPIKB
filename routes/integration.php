<?php
 
/*
|--------------------------------------------------------------------------
| Integration Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => 'warnNonWhitelistIp'], function () {
    Route::get('/login', 'DocController@login')->name('doc.login');
    Route::post('/plogin', 'DocController@processlogin')->name('doc.processlogin');

    Route::middleware(['doc.auth', 'onlyWhitelistIp'])->group(function () {
        Route::get('/', 'DocController@index')->name('doc.index');
        Route::get('/function/{name}', 'DocController@showfunction')->name('doc.functionname');
        Route::post('/function', 'DocController@testfunction')->name('doc.functiontest');
        Route::get('/logout', 'DocController@logout')->name('doc.logout');
    });
});