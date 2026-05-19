<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'auth.login')->name('login');
Route::view('/', 'welcome')->name('dashboard');
Route::view('/tickets', 'tickets.index')->name('tickets.index');
Route::view('/tickets/create', 'tickets.create')->name('tickets.create');

Route::get('/tickets/{ticket}', function (string $ticket) {
    return view('tickets.show', ['ticketId' => $ticket]);
})->name('tickets.show');
