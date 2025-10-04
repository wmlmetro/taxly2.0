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
        Schema::table('customer_transmissions', function (Blueprint $table) {
            $table->string('agent_tin')->nullable()->after('customer_email');
            $table->string('base_amount')->nullable()->after('agent_tin');
            $table->string('beneficiary_tin')->nullable()->after('base_amount');
            $table->string('currency')->after('beneficiary_tin')->default('NGN');
            $table->string('item_description')->nullable()->after('currency');
            $table->string('other_taxes')->nullable()->after('item_description');
            $table->string('total_amount')->nullable()->after('other_taxes');
            $table->string('transaction_date')->nullable()->after('total_amount');
            $table->string('integrator_service_id')->nullable()->after('transaction_date');
            $table->string('vat_calculated')->nullable()->after('integrator_service_id');
            $table->string('vat_rate')->nullable()->after('vat_calculated');
            $table->string('vat_status')->nullable()->after('vat_rate');
            $table->timestamp('acknowledged_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_transmissions', function (Blueprint $table) {
            $table->dropColumn([
                'agent_tin',
                'base_amount',
                'beneficiary_tin',
                'currency',
                'item_description',
                'other_taxes',
                'total_amount',
                'transaction_date',
                'integrator_service_id',
                'vat_calculated',
                'vat_rate',
                'vat_status',
                'acknowledged_at',
            ]);
        });
    }
};
