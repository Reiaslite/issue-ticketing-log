<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketStatusController;
use App\Http\Controllers\Api\TicketTrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Issue Ticketing Log API Routes
|--------------------------------------------------------------------------
|
| Base path: /api
| Authentication: Bearer token for every route except login.
| Response shape: success/message/data for success and
| success/message/errors for failures.
|
*/

// Public authentication endpoint. Accepts username/password and returns a Bearer token.
Route::post('/auth/login', [AuthController::class, 'login']);

/*
| Protected ticket endpoints.
|
| auth.token validates Authorization: Bearer {access_token} and attaches the
| authenticated user for audit fields such as created_by, updated_by, and
| deleted_by.
*/
Route::middleware('auth.token')->group(function () {
    // Employee endpoints.
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{employee_id}', [EmployeeController::class, 'show']);
    Route::put('/employees/{employee_id}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{employee_id}', [EmployeeController::class, 'destroy']);

    // Main ticket issue endpoints.
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket_id}', [TicketController::class, 'show']);
    Route::put('/tickets/{ticket_id}', [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket_id}', [TicketController::class, 'destroy']);

    // Tracking/status endpoints. These create tracking history and sync ticket status.
    Route::get('/tickets/{ticket_id}/trackings', [TicketTrackingController::class, 'index']);
    Route::post('/tickets/{ticket_id}/trackings', [TicketTrackingController::class, 'store']);
    Route::patch('/tickets/{ticket_id}/status', [TicketStatusController::class, 'update']);
});
