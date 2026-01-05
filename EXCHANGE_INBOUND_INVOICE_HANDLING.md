# Exchange Inbound Invoice Handling Implementation

## Overview

This implementation provides full Exchange Inbound Invoice Handling for Taxly, compliant with FIRS Exchange documentation. The system receives invoices transmitted by external systems (Taxly itself, other Access Point Providers, or Direct FIRS integrations) and routes them to the correct integrator without allowing direct FIRS → Vendra communication.

## Architecture

### Core Components

1. **Database Tables**

    - `exchange_events`: Stores incoming webhook events from FIRS Exchange
    - `exchange_invoices`: Stores pulled invoice details and processing state
    - Uses existing `organizations` table TIN field for invoice ownership resolution

2. **Models**

    - `ExchangeEvent`: Manages webhook events with status tracking
    - `ExchangeInvoice`: Manages invoice data with tenant/integrator assignment
    - Leverages existing `Organization` model TIN field for tenant resolution

3. **Controllers**

    - `FirsExchangeWebhookController`: Receives and validates FIRS Exchange webhooks

4. **Jobs**

    - `PullExchangeInvoiceJob`: Pulls invoice details from FIRS Exchange
    - `SyncExchangeInvoicesJob`: Scheduled job for backfilling and retrying failed invoices

5. **Services**
    - `ExchangeInvoiceProcessingService`: Orchestrates the entire invoice processing workflow
    - `TenantResolverService`: Resolves which tenant/integrator owns the invoice
    - `IntegratorWebhookDispatchService`: Dispatches webhooks to integrators
    - `FirsAcknowledgementService`: Acknowledges invoice receipt back to FIRS
    - Enhanced `FirsApiService` with Exchange-specific endpoints

## Workflow

### 1. Webhook Reception

```
FIRS Exchange → POST /api/firs-exchange/webhook → FirsExchangeWebhookController
```

-   Validates payload (IRN and message required)
-   Creates exchange event record
-   Logs raw payload for debugging
-   Returns HTTP 200 immediately (non-blocking)
-   Dispatches `PullExchangeInvoiceJob` for transmission events

### 2. Invoice Processing

```
PullExchangeInvoiceJob → ExchangeInvoiceProcessingService
```

-   Checks for duplicate invoices using IRN
-   Pulls invoice details from FIRS Exchange API
-   Creates exchange invoice record
-   Determines invoice direction (incoming/outgoing)
-   Resolves tenant and integrator ownership
-   Dispatches webhook to integrator
-   Acknowledges receipt to FIRS Exchange

### 3. Tenant Resolution

```
TenantResolverService → Match TIN against organizations
```

-   Matches buyer TIN against organization records for incoming invoices
-   Matches seller TIN against organization records for outgoing invoices
-   Resolves tenant through organization relationship
-   Supports reverse matching for flexibility
-   Handles unassigned invoices for later retry

### 4. Webhook Dispatch

```
IntegratorWebhookDispatchService → POST to integrator endpoints
```

-   Finds active webhook endpoints for integrator
-   Builds standardized payload structure
-   Supports multiple endpoints per integrator
-   Logs all webhook attempts with response tracking
-   Implements retry logic with backoff

### 5. FIRS Acknowledgement

```
FirsAcknowledgementService → PATCH /api/v1/invoice/transmit/{IRN}
```

-   Only acknowledges when invoice is assigned and webhook delivered
-   Prevents duplicate acknowledgements
-   Tracks acknowledgement state
-   Implements retry logic for failed acknowledgements

## API Endpoints

### FIRS Exchange Webhook

-   **URL**: `POST /api/firs-exchange/webhook`
-   **Rate Limit**: 60 requests per minute
-   **Payload**:

```json
{
    "irn": "INV0990-088ED42R-20270920",
    "message": "TRANSMITTED"
}
```

-   **Response**:

```json
{
    "message": "Webhook received successfully",
    "event_id": 123
}
```

### Integrator Webhook Payload

```json
{
    "irn": "INV0990-088ED42R-20270920",
    "direction": "INCOMING",
    "status": "TRANSMITTED",
    "buyer_tin": "123456789",
    "seller_tin": "987654321",
    "source": "FIRS_EXCHANGE",
    "received_at": "2025-12-23T09:01:00Z",
    "invoice_data": {
        /* Full invoice data */
    }
}
```

## Scheduled Jobs

### SyncExchangeInvoicesJob (Every 15 minutes)

-   Retries tenant resolution for unassigned invoices
-   Pulls missed invoices from FIRS Exchange
-   Prevents duplicates using IRN uniqueness
-   Processes invoices from last 7 days

### Daily Cleanup Commands

-   `exchange:cleanup`: Removes exchange events older than 30 days
-   `exchange:retry-acknowledgements`: Retries failed acknowledgements

## Configuration

### Environment Variables

```env
# FIRS API Configuration
FIRS_BASE_URL=https://api.firs.gov.ng
FIRS_API_KEY=your_api_key
FIRS_SECRET=your_secret
```

### Queue Configuration

-   Uses `exchange-invoices` queue for invoice processing jobs
-   Configurable retry attempts (default: 3)
-   Configurable timeout (default: 120 seconds)
-   Configurable backoff (default: 60 seconds)

## Security Features

1. **Webhook Validation**: Validates required fields and payload structure
2. **Rate Limiting**: 60 requests per minute on webhook endpoint
3. **Duplicate Prevention**: IRN-based uniqueness checks
4. **Error Handling**: Comprehensive error logging and graceful degradation
5. **Non-blocking Processing**: Immediate HTTP 200 response to prevent FIRS retries

## Observability

### Structured Logging

-   Webhook reception with payload and headers
-   Invoice pull success/failure
-   Tenant resolution results
-   Webhook dispatch attempts and responses
-   FIRS acknowledgement status

### Database Tracking

-   `exchange_events`: Raw webhook data and processing state
-   `exchange_invoices`: Complete invoice lifecycle tracking
-   `webhook_logs`: Detailed webhook attempt logging

## Testing

Comprehensive test suite covering:

-   Webhook reception and validation
-   Model functionality and relationships
-   Service logic and edge cases
-   Tenant resolution scenarios
-   Webhook payload structure

Run tests with:

```bash
php artisan test tests/Feature/ExchangeInvoiceHandlingTest.php
```

## Deployment

1. Run migrations:

```bash
php artisan migrate
```

2. Configure queue workers:

```bash
php artisan queue:work --queue=exchange-invoices,default
```

3. Schedule cron jobs:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Compliance

✅ **FIRS Exchange Compliant**: Follows FIRS Exchange documentation
✅ **No Direct FIRS→Vendra Communication**: All communication routed through Taxly
✅ **Idempotent Processing**: Safe retry handling with duplicate prevention
✅ **Audit Trail**: Complete logging and tracking of all operations
✅ **Error Recovery**: Automatic retry mechanisms for failed operations

## Future Enhancements

1. **Enhanced Tenant Matching**: Support for multiple TINs per tenant
2. **Webhook Security**: HMAC signature validation for integrator webhooks
3. **Performance Optimization**: Batch processing for high-volume scenarios
4. **Monitoring Dashboard**: Real-time processing status and metrics
5. **Advanced Filtering**: Support for filtering invoices by various criteria
