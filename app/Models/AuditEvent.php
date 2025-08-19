<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AuditEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_ref',
        'verb',
        'actor_id',
        'payload_hash',
    ];

    public $timestamps = false; // we store `created_at` manually

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function auditable()
    {
        return $this->morphTo(__FUNCTION__, 'entity_ref', 'id');
    }

    // ---------------------------
    // 🔧 Business Logic Helpers
    // ---------------------------

    /**
     * Record a new audit event
     */
    public static function record(string $entityRef, string $verb, ?int $actorId = null, ?array $payload = null): self
    {
        return self::create([
            'entity_ref' => $entityRef,
            'verb'       => $verb,
            'actor_id'   => $actorId,
            'payload_hash' => $payload ? Hash::make(json_encode($payload)) : null,
            'created_at' => now(),
        ]);
    }

    /**
     * Fetch recent events for an entity
     */
    public static function forEntity(string $entityRef, int $limit = 50)
    {
        return self::where('entity_ref', $entityRef)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
