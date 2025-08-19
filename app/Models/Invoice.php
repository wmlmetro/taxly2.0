<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'buyer_organization_ref',
        'total_amount',
        'tax_breakdown',
        'vat_treatment',
        'wht_amount',
        'status',
    ];

    protected $casts = [
        'tax_breakdown' => 'array',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function irn()
    {
        return $this->hasOne(Irns::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function acceptances()
    {
        return $this->hasMany(Acceptance::class);
    }

    public function artifacts()
    {
        return $this->hasMany(Artifact::class);
    }

    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'entity_ref');
    }

    // Business Logic Helpers
    public function markAsValidated(): void
    {
        $this->status = 'validated';
        $this->save();
    }

    public function markAsSubmitted(): void
    {
        $this->status = 'submitted';
        $this->save();
    }

    public function markAsReported(): void
    {
        $this->status = 'reported';
        $this->save();
    }

    public function markAsClosed(): void
    {
        $this->status = 'closed';
        $this->save();
    }
}
