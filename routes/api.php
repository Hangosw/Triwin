<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HanetWebhookController;

Route::post('/hanet/webhook', [HanetWebhookController::class, 'handle']);
