<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'type', 'template', 'status'];
    protected $casts = [];

    public function org()
    {
        return $this->belongsTo(Organization::class);
    }

    public function markSent(): void
    {
        $this->status = 'sent';
        $this->save();
    }

    public function markFailed(): void
    {
        $this->status = 'failed';
        $this->save();
    }
}
