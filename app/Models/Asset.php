<?php

namespace App\Models;

use App\Models\Enums\AssetBaseline;
use App\Models\Enums\AssetCondition;
use App\Traits\HasNanoID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes, HasNanoID;

    protected $fillable = [
        "register_code",
        "serial_number",
        "hostname",
        "brand",
        "model",

        "condition",

        "ip_vlan",
        "vlan",
        "port_acs_vlan",
        "port_trunk",
        "port_capacity",
        "baseline",

        "os_version",

        "eos_date",
        "purchase_year",

        "image_url",

        "price",
    ];

    protected $casts = [
        "condition" => AssetCondition::class,
        "baseline" => AssetBaseline::class,
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
