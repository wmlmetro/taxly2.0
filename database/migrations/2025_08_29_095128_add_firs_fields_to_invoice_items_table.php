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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('hsn_code')->nullable()->after('price');
            $table->string('product_category')->nullable()->after('hsn_code');
            $table->decimal('discount_rate', 8, 2)->nullable()->after('product_category');
            $table->decimal('discount_amount', 18, 2)->nullable()->after('discount_rate');
            $table->decimal('fee_rate', 8, 2)->nullable()->after('discount_amount');
            $table->decimal('fee_amount', 18, 2)->nullable()->after('fee_rate');
            $table->integer('invoiced_quantity')->default(1)->after('fee_amount');
            $table->decimal('line_extension_amount', 18, 2)->nullable()->after('invoiced_quantity');

            // Item object
            $table->string('item_name')->nullable()->after('line_extension_amount');
            $table->text('item_description')->nullable()->after('item_name');
            $table->string('sellers_item_identification')->nullable()->after('item_description');

            // Price object
            $table->decimal('price_amount', 18, 2)->nullable()->after('sellers_item_identification');
            $table->integer('base_quantity')->default(1)->after('price_amount');
            $table->string('price_unit')->default('NGN per 1')->after('base_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn([
                'hsn_code',
                'product_category',
                'discount_rate',
                'discount_amount',
                'fee_rate',
                'fee_amount',
                'invoiced_quantity',
                'line_extension_amount',
                'item_name',
                'item_description',
                'sellers_item_identification',
                'price_amount',
                'base_quantity',
                'price_unit',
            ]);
        });
    }
};
