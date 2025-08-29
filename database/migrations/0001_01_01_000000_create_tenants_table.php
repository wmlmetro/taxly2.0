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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable(); // TAX Payer Email
            $table->string('password')->nullable(); // TAX Payer Password
            $table->string('entity_id')->unique()->nullable(); // FIRS Entity ID
            $table->string('brand')->nullable();
            $table->string('domain')->unique()->nullable();
            $table->json('feature_flags')->nullable();
            $table->string('retention_policy')->default('default');
            $table->timestamps();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('tin')->unique(); // Tax Identification Number
            $table->string('business_id')->unique()->nullable(); // FIRS Business ID
            $table->string('service_id')->unique()->nullable(); // FIRS Service ID
            $table->string('trade_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('street_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('postal_zone')->nullable();
            $table->string('country')->default('NG');
            $table->text('description')->nullable();
            $table->json('bank_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('organizations');
    }
};
