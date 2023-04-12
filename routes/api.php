<?php

use App\Http\Controllers\API\TelegramWebhookController;
use Illuminate\Support\Facades\Route;


Route::middleware('telegram-auth')->controller(TelegramWebhookController::class)->group(function(){
    Route::any('/webhook/{webhook_token}', 'webhook');
});

