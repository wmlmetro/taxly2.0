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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('buyer_organization_ref')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->json('tax_breakdown')->nullable();
            $table->enum('vat_treatment', ['standard', 'zero-rated', 'exempt'])->default('standard');
            $table->decimal('wht_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'validated', 'submitted', 'reported', 'closed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
