<?php

use App\Http\Controllers\API\AIAgentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AIChatController;
use App\Http\Controllers\API\BusinessAgentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('v1')->group(function () {

    Route::post(
        '/ai/chatbot',
        [AIChatController::class, 'chat']
    );
});

Route::post('/v1/ai/agent', [AIAgentController::class, 'chat']);
Route::post(
    '/v1/ai/business-agent',
    [BusinessAgentController::class, 'chat']
);
