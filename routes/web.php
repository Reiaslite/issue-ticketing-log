<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'auth.login')->name('login');
Route::view('/', 'welcome')->name('dashboard');

/**
 * Employee routes
 */
Route::view('/employees', 'pages.employees.index')->name('employees.index');
Route::view('/employees/create', 'pages.employees.create')->name('employees.create');

/**
 * Ticket routes
 */
Route::view('/tickets', 'pages.tickets.index')->name('tickets.index');
Route::view('/tickets/create', 'pages.tickets.create')->name('tickets.create');

Route::get('/tickets/{ticket}', function (string $ticket) {
    return view('pages.tickets.show', ['ticketId' => $ticket]);
})->name('tickets.show');
