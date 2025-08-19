<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Irns extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'irn_hash',
        'qr_text',
        'qr_image_path',
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
