<?php

namespace App\Models;

use App\Traits\HasNanoID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasNanoID;

    protected $fillable = ["name", "floor", "code"];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
