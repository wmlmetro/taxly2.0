<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuyerInvoiceController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/auth/register', [AuthController::class, 'register']);
Route::post('/v1/auth/login',    [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Invoices
    Route::get('/invoices',                 [InvoiceController::class, 'index']);
    Route::post('/invoices',                [InvoiceController::class, 'store']);
    Route::get('/invoices/{invoice}',       [InvoiceController::class, 'show']);
    Route::post('/invoices/{invoice}/validate', [InvoiceController::class, 'validateInvoice']);
    Route::post('/invoices/{invoice}/submit',   [InvoiceController::class, 'submit']);

    // Buyer actions
    Route::post('/buyer/invoices/{invoice}/accept', [BuyerInvoiceController::class, 'accept']);
    Route::post('/buyer/invoices/{invoice}/reject', [BuyerInvoiceController::class, 'reject']);

    // Webhooks
    Route::get('/webhooks',    [WebhookController::class, 'index']);
    Route::post('/webhooks',   [WebhookController::class, 'store']);
    Route::delete('/webhooks/{webhookEndpoint}', [WebhookController::class, 'destroy']);
});
