<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransWebhookController;

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
