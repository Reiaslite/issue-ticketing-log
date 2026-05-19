<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketStatusController;
use App\Http\Controllers\Api\TicketTrackingController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth.token')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket_id}', [TicketController::class, 'show']);
    Route::put('/tickets/{ticket_id}', [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket_id}', [TicketController::class, 'destroy']);

    Route::get('/tickets/{ticket_id}/trackings', [TicketTrackingController::class, 'index']);
    Route::post('/tickets/{ticket_id}/trackings', [TicketTrackingController::class, 'store']);
    Route::patch('/tickets/{ticket_id}/status', [TicketStatusController::class, 'update']);
});
