<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'card_id',
        'language',
        'condition',
        'variant',
        'price',
        'stock',
        'is_active',
        'visits',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
