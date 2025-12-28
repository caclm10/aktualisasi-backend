<?php

namespace App\Models;

use App\Models\Enums\ActivityCategory;
use App\Traits\HasNanoID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasNanoID;

    protected $fillable = [
        "category",
        "property",
        "old",
        "new",
        "remarks",
        "performed_at",
    ];

    protected $casts = [
        "category" => ActivityCategory::class,
        "performed_at" => "datetime",
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
