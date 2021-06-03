<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Offers\OfferController;
use App\Http\Controllers\Order\OrderController;

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

Route::get('/orders',[OrderController::class,'index']);
Route::get('/orders/{order}',[OrderController::class,'show']);
Route::put("/orders/{order}/status/{status}",[OrderController::class, "updateOrderStatus"]);
Route::put("/orders/{order}/lines",[OrderController::class, "cancelProducts"]);
Route::post("/orders/{order}/refund",[OrderController::class, "refundMoney"]);
Route::post("/orders/{order}/return",[OrderController::class, "returnProduct"]);
Route::post("/orders/{order}/invoices",[OrderController::class, "sendInvoice"]);

Route::get('offers',[OfferController::class,'index']);
Route::get('offers/file_status',[OfferController::class,'getFileStatus']);
Route::get('offers/price_list',[OfferController::class,'priceList']);
Route::get('offers/uploads_stocks',[OfferController::class,'uploadStock']);
