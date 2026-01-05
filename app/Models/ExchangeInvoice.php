<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeInvoice extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<string>
   */
  protected $fillable = [
    'irn',
    'buyer_tin',
    'seller_tin',
    'direction',
    'status',
    'invoice_data',
    'tenant_id',
    'integrator_id',
    'acknowledged_at',
    'webhook_delivered_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'invoice_data' => 'array',
    'acknowledged_at' => 'datetime',
    'webhook_delivered_at' => 'datetime',
  ];

  /**
   * Status constants
   */
  const STATUS_TRANSMITTED = 'TRANSMITTED';
  const STATUS_ACKNOWLEDGED = 'ACKNOWLEDGED';
  const STATUS_FAILED = 'FAILED';

  /**
   * Direction constants
   */
  const DIRECTION_INCOMING = 'INCOMING';
  const DIRECTION_OUTGOING = 'OUTGOING';

  /**
   * Get the tenant that owns this invoice.
   */
  public function tenant(): BelongsTo
  {
    return $this->belongsTo(Tenant::class);
  }

  /**
   * Get the integrator that owns this invoice.
   */
  public function integrator(): BelongsTo
  {
    return $this->belongsTo(Tenant::class, 'integrator_id');
  }

  /**
   * Mark the invoice as acknowledged
   */
  public function markAsAcknowledged(): void
  {
    $this->update([
      'status' => self::STATUS_ACKNOWLEDGED,
      'acknowledged_at' => now(),
    ]);
  }

  /**
   * Mark the invoice as webhook delivered
   */
  public function markAsWebhookDelivered(): void
  {
    $this->update(['webhook_delivered_at' => now()]);
  }

  /**
   * Check if the invoice has been acknowledged
   */
  public function isAcknowledged(): bool
  {
    return $this->status === self::STATUS_ACKNOWLEDGED;
  }

  /**
   * Check if the invoice webhook has been delivered
   */
  public function isWebhookDelivered(): bool
  {
    return !is_null($this->webhook_delivered_at);
  }

  /**
   * Check if the invoice is assigned to a tenant
   */
  public function isAssigned(): bool
  {
    return !is_null($this->tenant_id);
  }

  /**
   * Scope to get unassigned invoices
   */
  public function scopeUnassigned($query)
  {
    return $query->whereNull('tenant_id');
  }

  /**
   * Scope to get incoming invoices
   */
  public function scopeIncoming($query)
  {
    return $query->where('direction', self::DIRECTION_INCOMING);
  }

  /**
   * Scope to get outgoing invoices
   */
  public function scopeOutgoing($query)
  {
    return $query->where('direction', self::DIRECTION_OUTGOING);
  }

  /**
   * Check if invoice is incoming
   */
  public function isIncoming(): bool
  {
    return $this->direction === self::DIRECTION_INCOMING;
  }

  /**
   * Check if invoice is outgoing
   */
  public function isOutgoing(): bool
  {
    return $this->direction === self::DIRECTION_OUTGOING;
  }
}
