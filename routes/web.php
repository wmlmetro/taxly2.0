<?php

use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\InvoicePdfController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Welcome/Homepage Route - Beautiful Documentation
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Ecosystem Security Architecture Page
Route::get('/ecosystem', function () {
    return view('ecosystem-detailed');
})->name('ecosystem');

// Simple test page
Route::get('/ecosystem-test', function () {
    return view('ecosystem-simple');
})->name('ecosystem-test');

Route::middleware(['auth'])->group(function () {
    Route::get('invoices/{invoice}/pdf', [InvoicePdfController::class, 'download'])
        ->name('invoices.pdf');
    Route::get('invoices/{invoice}/send-email', [InvoicePdfController::class, 'sendEmail'])
        ->name('invoices.email');
});

Route::middleware(['auth'])->group(function () {
    Volt::route('/dashboard', 'dashboard')->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Volt::route('invoices', 'invoice.index')->name('invoices.index');
    Volt::route('invoices/create', 'invoice.create')->name('invoices.create');
    Volt::route('invoices/{invoice}', 'invoice.show')->name('invoices.show');
    Volt::route('invoices/{invoice}/edit', 'invoice.edit-invoice')->name('invoices.edit');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';
