<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'tin',
        'business_id',
        'service_id',
        'trade_name',
        'registration_number',
        'email',
        'phone',
        'street_name',
        'city_name',
        'postal_zone',
        'country',
        'description',
        'bank_details',
    ];

    protected $casts = [
        'bank_details' => 'array',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function webhookEndpoints()
    {
        return $this->hasMany(WebhookEndpoint::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
