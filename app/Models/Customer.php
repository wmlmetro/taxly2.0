<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tin',
        'email',
        'phone',
        'business_description',
        'street_name',
        'city_name',
        'postal_zone',
        'state',
        'country',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
