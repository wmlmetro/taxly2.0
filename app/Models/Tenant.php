<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'domain',
        'feature_flags',
        'retention_policy',
    ];

    protected $casts = [
        'feature_flags' => 'array',
    ];

    // Relationships
    public function organization()
    {
        return $this->hasMany(Organization::class);
    }

    public function usageMeters()
    {
        return $this->hasMany(UsageMeter::class);
    }
}
