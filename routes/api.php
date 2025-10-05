<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuyerInvoiceController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/auth/register', [AuthController::class, 'register']);
Route::post('/v1/auth/login',    [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    Route::middleware('role:super admin')->group(function () {
        Route::post('/tenants', [TenantController::class, 'store']);
    });
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::get('/tenants/{tenant}', [TenantController::class, 'show']);

    // Invoices
    Route::get('/invoices/search/{business_id}',    [InvoiceController::class, 'search']);
    Route::get('/invoices/{irn}/download',   [InvoiceController::class, 'download']);
    Route::get('/invoices/{irn}/confirm',   [InvoiceController::class, 'confirm']);
    Route::patch('/invoices/{irn}/update',  [InvoiceController::class, 'update']);
    Route::post('/invoices/irn/validate',   [InvoiceController::class, 'validateInvoiceIRN']);
    Route::post('/invoices/validate',       [InvoiceController::class, 'validateInvoice']);
    Route::post('/invoices/submit',         [InvoiceController::class, 'submit']);
    Route::post('/invoices/{irn}/transmit', [InvoiceController::class, 'transmit']);
    Route::get('/invoices/transmit/health-check', [InvoiceController::class, 'healthCheck']);
    Route::get('/invoices/transmit/{irn}/lookup', [InvoiceController::class, 'getInvoiceTransmitted']);

    // Buyer actions
    Route::post('/buyer/invoices/{invoice}/accept', [BuyerInvoiceController::class, 'accept']);
    Route::post('/buyer/invoices/{invoice}/reject', [BuyerInvoiceController::class, 'reject']);
});

// Webhooks
Route::post('/webhooks/firs', [WebhookController::class, 'handle']);
Route::patch('/v1/invoice/transmit/{irn}', [InvoiceController::class, 'acknowledge']);
