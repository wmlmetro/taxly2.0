<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuyerInvoiceController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

// ✅ Auth routes
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/tax-payer-login', [AuthController::class, 'taxPayerLogin']);
});

// 🛠️ Resources routes - Now public
Route::prefix('v1/resources')->group(function () {
    Route::get('/invoice-types', [ResourceController::class, 'getInvoiceTypes']);
    Route::get('/payment-means', [ResourceController::class, 'getPaymentMeans']);
    Route::get('/tax-categories', [ResourceController::class, 'getTaxCategories']);
    Route::get('/tin/{tin_number}', [ResourceController::class, 'getTin']);
    Route::get('/entity/{entity_id}', [ResourceController::class, 'getEntity']);
});

// ✅ Common invoice and buyer routes (to be reused) - Resources routes removed
$invoiceRoutes = function () {
    // Invoices
    Route::get('/invoices/search/{business_id}', [InvoiceController::class, 'search']);
    Route::get('/invoices/{irn}/download', [InvoiceController::class, 'download']);
    Route::get('/invoices/{irn}/confirm', [InvoiceController::class, 'confirm']);
    Route::patch('/invoices/{irn}/update', [InvoiceController::class, 'update']);
    Route::post('/invoices/irn/validate', [InvoiceController::class, 'validateInvoiceIRN']);
    Route::post('/invoices/validate', [InvoiceController::class, 'validateInvoice']);
    Route::post('/invoices/submit', [InvoiceController::class, 'submit']);
    Route::post('/invoices/{irn}/transmit', [InvoiceController::class, 'transmit']);
    Route::get('/invoices/transmit/health-check', [InvoiceController::class, 'healthCheck']);
    Route::get('/invoices/transmit/{irn}/lookup', [InvoiceController::class, 'getInvoiceTransmitted']);
    Route::get('/invoices/transmit/tin/{tin}/lookup', [InvoiceController::class, 'getInvoiceTransmittedByTin']);
    Route::get('/invoices/transmit/pull', [InvoiceController::class, 'pullTransmittedInvoices']);
    Route::patch('/invoices/transmit/{irn}/confirmation', [InvoiceController::class, 'acknowledge']);

    // Buyer actions
    Route::post('/buyer/invoices/{invoice}/accept', [BuyerInvoiceController::class, 'accept']);
    Route::post('/buyer/invoices/{invoice}/reject', [BuyerInvoiceController::class, 'reject']);
};

// ✅ Protected routes (auth:sanctum)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () use ($invoiceRoutes) {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::middleware('role:super admin')->group(function () {
        Route::post('/tenants', [TenantController::class, 'store']);
    });

    Route::get('/tenants', [TenantController::class, 'index']);
    Route::get('/tenants/{tenant}', [TenantController::class, 'show']);

    // Include invoice and buyer routes
    $invoiceRoutes();
});

// ✅ API Key or Auth routes
Route::middleware(['auth-or-apikey'])->prefix('v1')->group($invoiceRoutes);

// ✅ Webhooks
Route::post('/webhooks/firs', [WebhookController::class, 'handle']);
Route::patch('/v1/invoice/transmit/{irn}', [InvoiceController::class, 'acknowledge']);
