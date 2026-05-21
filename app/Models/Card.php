<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    protected $fillable = ['card_set_id', 'name', 'card_number', 'card_type', 'artist', 'image_url'];

    public function set(): BelongsTo
    {
        return $this->belongsTo(CardSet::class, 'card_set_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
