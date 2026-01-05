<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeEvent extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<string>
   */
  protected $fillable = [
    'irn',
    'status',
    'raw_payload',
    'processed_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'raw_payload' => 'array',
    'processed_at' => 'datetime',
  ];

  /**
   * Status constants
   */
  const STATUS_TRANSMITTING = 'TRANSMITTING';
  const STATUS_TRANSMITTED = 'TRANSMITTED';
  const STATUS_ACKNOWLEDGED = 'ACKNOWLEDGED';
  const STATUS_FAILED = 'FAILED';

  /**
   * Mark the event as processed
   */
  public function markAsProcessed(): void
  {
    $this->update(['processed_at' => now()]);
  }

  /**
   * Check if the event has been processed
   */
  public function isProcessed(): bool
  {
    return !is_null($this->processed_at);
  }
}
