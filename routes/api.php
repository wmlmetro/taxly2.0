<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuyerInvoiceController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\InvoiceCrudController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\WebhookCrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// --------------------
// Public Auth Routes
// --------------------
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/tax-payer-login', [AuthController::class, 'taxPayerLogin']);
});

// --------------------
// Public Resource Routes
// --------------------
Route::prefix('v1/resources')->group(function () {
    Route::get('/invoice-types', [ResourceController::class, 'getInvoiceTypes']);
    Route::get('/payment-means', [ResourceController::class, 'getPaymentMeans']);
    Route::get('/tax-categories', [ResourceController::class, 'getTaxCategories']);
    Route::get('/tin/{tin_number}', [ResourceController::class, 'getTin']);
    Route::get('/entity/{entity_id}', [ResourceController::class, 'getEntity']);
    Route::get('/currencies', [ResourceController::class, 'getCurrencies']);
    Route::get('/hs-codes', [ResourceController::class, 'getHSCodes']);
    Route::get('/services-codes', [ResourceController::class, 'getServiceCodes']);
    Route::get('/countries', [ResourceController::class, 'getCountries']);
    Route::get('/states', [ResourceController::class, 'getStates']);
    Route::get('/lgas', [ResourceController::class, 'getLGAs']);
});

// --------------------
// Routes Supporting Either Sanctum or API Key
// --------------------
Route::middleware(['auth-or-apikey'])->prefix('v1')->group(function () {

    // Invoice Operations (support both auth methods)
    Route::prefix('invoices')->group(function () {
        Route::get('/search/{business_id}', [InvoiceController::class, 'search']);
        Route::get('/{irn}/download', [InvoiceController::class, 'download']);
        Route::get('/{irn}/confirm', [InvoiceController::class, 'confirm']);
        Route::patch('/{irn}/update', [InvoiceController::class, 'update']);
        Route::post('/validate', [InvoiceController::class, 'validateInvoice']);
        Route::post('/submit', [InvoiceController::class, 'submit']);
        Route::post('/{irn}/transmit', [InvoiceController::class, 'transmit']);
        Route::get('/transmit/health-check', [InvoiceController::class, 'healthCheck']);
        Route::get('/transmit/{irn}/lookup', [InvoiceController::class, 'getInvoiceTransmitted']);
        Route::get('/transmit/tin/{tin}/lookup', [InvoiceController::class, 'getInvoiceTransmittedByTin']);
        Route::get('/transmit/pull', [InvoiceController::class, 'pullTransmittedInvoices']);
        Route::patch('/transmit/{irn}/confirmation', [InvoiceController::class, 'acknowledge']);

        // IRN validation endpoint
        Route::post('/irn/validate', [InvoiceController::class, 'validateInvoiceIRN']);
    });

    // Webhooks CRUD (support both auth methods)
    Route::get('/webhooks', [WebhookCrudController::class, 'index']);
    Route::post('/webhooks', [WebhookCrudController::class, 'store']);

    // Buyer actions (support both auth methods)
    Route::prefix('buyer/invoices')->group(function () {
        Route::post('/{invoice}/accept', [BuyerInvoiceController::class, 'accept']);
        Route::post('/{invoice}/reject', [BuyerInvoiceController::class, 'reject']);
    });
});

// --------------------
// Protected Routes via Sanctum
// --------------------
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Tenants
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::get('/tenants/{tenant}', [TenantController::class, 'show']);

    // Invoice CRUD (Sanctum-only routes)
    Route::post('/invoices', [InvoiceCrudController::class, 'store']);
    Route::post('/invoices/{invoice}/validate', [InvoiceCrudController::class, 'validateInvoice']);
    Route::post('/invoices/{invoice}/submit', [InvoiceCrudController::class, 'submit']);

    // Note: Invoice operations, webhooks CRUD, and buyer actions are now in the auth-or-apikey group
    // They support both API Key and Sanctum authentication through the auth-or-apikey middleware
});

// --------------------
// Webhooks (public)
// --------------------
Route::post('/webhooks/firs', [WebhookController::class, 'handle']);
Route::patch('/v1/invoice/transmit/{irn}', [InvoiceController::class, 'acknowledge']);
