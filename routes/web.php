<?php

use App\Http\Controllers\AttendantController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'index'])->name('tickets.index');
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
Route::get('/api/attendants', [AttendantController::class, 'index'])->name('api.attendants');