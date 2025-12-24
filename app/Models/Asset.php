<?php

namespace App\Models;

use App\Models\Enums\AssetComplianceStatus;
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
        "compliance_status",

        "os_version",

        "eos_date",
        "purchase_year",

        "image_url",

        "price",
    ];

    protected $casts = [
        "condition" => AssetCondition::class,
        "compliance_status" => AssetComplianceStatus::class,
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
