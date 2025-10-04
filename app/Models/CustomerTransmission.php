<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTransmission extends Model
{
    protected $fillable = [
        'irn',
        'supplier_name',
        'supplier_email',
        'customer_name',
        'customer_email',
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
        'acknowledged_at'
    ];
}
