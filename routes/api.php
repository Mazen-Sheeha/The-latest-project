<?php

use App\Http\Controllers\EasyOrderController;
use Illuminate\Support\Facades\Route;

Route::post('/easyorder/webhook', [EasyOrderController::class, 'webhook']);
