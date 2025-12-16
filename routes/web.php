<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\TransactionPdfController;

Route::get('/transactions/pdf/{user}', [TransactionPdfController::class, 'download'])
    ->name('transactions.pdf');
