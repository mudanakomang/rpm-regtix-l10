<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Webhook\MidtransWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::post('/voucher-code/check', [VoucherController::class, 'checkVoucherCode']);
Route::post('/register', [RegistrationController::class, 'store']);
