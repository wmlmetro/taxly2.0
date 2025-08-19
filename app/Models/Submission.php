<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'channel',
        'atrs_txn_id',
        'status',
        'attempts',
        'last_error',
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Helpers
    public function markSuccess(string $txnId): void
    {
        $this->status = 'success';
        $this->atrs_txn_id = $txnId;
        $this->save();
    }

    public function markFailed(string $error): void
    {
        $this->status = 'failed';
        $this->last_error = $error;
        $this->attempts++;
        $this->save();
    }
}
