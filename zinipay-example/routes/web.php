<?php

use App\Http\Controllers\ZinipayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get( '/', function () {
    return view( 'welcome' );
} );

Route::get( 'pay', [ZinipayController::class, 'show'] )->name( 'zinipay.payment-form' );
Route::post( 'pay', [ZinipayController::class, 'pay'] )->name( 'zinipay.pay' );
Route::get( 'success', [ZinipayController::class, 'success'] )->name( 'zinipay.success' );
Route::get( 'cancel', [ZinipayController::class, 'cancel'] )->name( 'zinipay.cancel' );