<?php

namespace App\Models;

use App\Traits\HasNanoID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasNanoID;

    protected $fillable = [
        "category",
        "type",
        "remarks",
        "status_snapshot",
        "properties",
    ];

    protected $casts = [
        "properties" => "array",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
