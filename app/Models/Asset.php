<?php

namespace App\Models;

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
        "deployment_status",

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

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
