<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'price',
        'line_total',

        'hsn_code',
        'product_category',
        'discount_rate',
        'discount_amount',
        'fee_rate',
        'fee_amount',
        'invoiced_quantity',
        'line_extension_amount',

        'item_name',
        'item_description',
        'sellers_item_identification',

        'price_amount',
        'base_quantity',
        'price_unit',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function toFirsPayload(): array
    {
        return [
            "hsn_code" => $this->hsn_code,
            "product_category" => $this->product_category,
            "discount_rate" => $this->discount_rate,
            "discount_amount" => $this->discount_amount,
            "fee_rate" => $this->fee_rate,
            "fee_amount" => $this->fee_amount,
            "invoiced_quantity" => $this->invoiced_quantity ?? $this->quantity,
            "line_extension_amount" => $this->line_extension_amount ?? $this->line_total,
            "item" => [
                "name" => $this->item_name,
                "description" => $this->item_description ?? $this->description,
                "sellers_item_identification" => $this->sellers_item_identification,
            ],
            "price" => [
                "price_amount" => $this->price_amount ?? $this->price,
                "base_quantity" => $this->base_quantity ?? 1,
                "price_unit" => $this->price_unit ?? "NGN per 1",
            ]
        ];
    }
}
