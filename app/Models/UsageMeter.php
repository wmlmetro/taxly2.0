<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageMeter extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'counters', 'billing_cycle'];
    protected $casts = ['counters' => 'array'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function incrementCounter(int $tenantId, string $counter): void
    {
        $usage = self::firstOrCreate(['tenant_id' => $tenantId], ['counters' => []]);
        $counters = $usage->counters ?? [];
        $counters[$counter] = ($counters[$counter] ?? 0) + 1;
        $usage->counters = $counters;
        $usage->save();
    }

    public static function forTenant(int $tenantId): self
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            ['counters' => [], 'billing_cycle' => 'monthly']
        );
    }
}
