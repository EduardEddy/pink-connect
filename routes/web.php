<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Order\OrderController;
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

Route::get('/', function () {
    //return view('welcome');
    return Http::withHeaders([
        'Authorization'=>'Bearer rU0SOsKO3ny9MNO4UZYC9X_3ilOPGmng'
    ])->get(env('BASE_PATH').'/orders');
});

Route::get('/orders',[OrderController::class,'index']);
