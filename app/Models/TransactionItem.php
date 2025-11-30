<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id', 'product_id', 'product_name', 
        'quantity', 'price_at_transaction'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}