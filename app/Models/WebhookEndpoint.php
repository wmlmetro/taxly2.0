<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'url',
        'irn',
        'message',
    ];

    protected $casts = [
        'subscribed_events' => 'array',
    ];

    public function org()
    {
        return $this->belongsTo(Organization::class);
    }

    public function isSubscribed(string $event): bool
    {
        $events = $this->subscribed_events ?? [];
        return in_array($event, $events, true) || in_array('*', $events, true);
    }

    /**
     * Generate HMAC signature header for a payload
     */
    public function signatureFor(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->secret);
    }
}
