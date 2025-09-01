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

    public function toPartyObject(): array
    {
        return [
            "party_name"  => $this->trade_name ?? $this->registration_number,
            "tin"         => $this->tin,
            "email"       => $this->email,
            "telephone"   => $this->phone,
            "business_description" => $this->description,
            "postal_address" => [
                "street_name" => $this->street_name,
                "city_name"   => $this->city_name,
                "postal_zone" => $this->postal_zone,
                "country"     => $this->country ?? 'NG',
            ],
        ];
    }
}
