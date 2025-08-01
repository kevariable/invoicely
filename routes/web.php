<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
})->name('home');

// Public invoice preview routes (no authentication required)
Route::get('/invoice/preview/{token}', [App\Http\Controllers\InvoicePreviewController::class, 'show'])
    ->name('invoice.public.preview');
Route::get('/invoice/preview/{token}/download', [App\Http\Controllers\InvoicePreviewController::class, 'downloadPdf'])
    ->name('invoice.public.download');

// Redirect any old routes to admin
Route::redirect('/dashboard', '/admin');
Route::redirect('/settings', '/admin');
Route::redirect('/settings/{any}', '/admin')->where('any', '.*');

require __DIR__.'/auth.php';
