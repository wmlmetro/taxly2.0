<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // FIRS fields
            $table->string('invoice_number', 20)->nullable()->unique()->after('id');
            $table->string('business_id')->nullable()->after('organization_id');
            $table->string('irn')->nullable()->after('business_id');
            $table->date('issue_date')->nullable()->after('irn');
            $table->date('due_date')->nullable()->after('issue_date');
            $table->time('issue_time')->nullable()->after('due_date');
            $table->string('invoice_type_code')->default('396')->after('vat_treatment');
            $table->string('payment_status')->default('PENDING')->after('invoice_type_code');
            $table->text('note')->nullable()->after('payment_status');
            $table->date('tax_point_date')->nullable()->after('note');
            $table->string('document_currency_code')->default('NGN')->after('tax_point_date');
            $table->string('tax_currency_code')->default('NGN')->after('document_currency_code');
            $table->string('accounting_cost')->nullable()->after('tax_currency_code');
            $table->string('buyer_reference')->nullable()->after('accounting_cost');

            // JSON references
            $table->json('invoice_delivery_period')->nullable()->after('buyer_reference');
            $table->json('billing_reference')->nullable()->after('invoice_delivery_period');
            $table->json('dispatch_document_reference')->nullable()->after('billing_reference');
            $table->json('receipt_document_reference')->nullable()->after('dispatch_document_reference');
            $table->json('originator_document_reference')->nullable()->after('receipt_document_reference');
            $table->json('contract_document_reference')->nullable()->after('originator_document_reference');
            $table->json('additional_document_reference')->nullable()->after('contract_document_reference');

            // Delivery & payments
            $table->date('actual_delivery_date')->nullable()->after('additional_document_reference');
            $table->json('payment_means')->nullable()->after('actual_delivery_date');
            $table->text('payment_terms_note')->nullable()->after('payment_means');
            $table->json('allowance_charge')->nullable()->after('payment_terms_note');

            // Totals
            $table->json('tax_total')->nullable()->after('allowance_charge');
            $table->json('legal_monetary_total')->nullable()->after('tax_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'business_id',
                'irn',
                'issue_date',
                'due_date',
                'issue_time',
                'invoice_type_code',
                'payment_status',
                'note',
                'tax_point_date',
                'document_currency_code',
                'tax_currency_code',
                'accounting_cost',
                'buyer_reference',
                'invoice_delivery_period',
                'billing_reference',
                'dispatch_document_reference',
                'receipt_document_reference',
                'originator_document_reference',
                'contract_document_reference',
                'additional_document_reference',
                'actual_delivery_date',
                'payment_means',
                'payment_terms_note',
                'allowance_charge',
                'tax_total',
                'legal_monetary_total',
            ]);
        });
    }
};
