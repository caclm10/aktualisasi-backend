<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,

            // Konversi snake_case (DB) ke camelCase (TS)
            "registerCode" => $this->register_code,
            "serialNumber" => $this->serial_number,
            "hostname" => $this->hostname,
            "brand" => $this->brand,
            "model" => $this->model,

            "roomId" => $this->room_id,

            // Relasi: Menggunakan whenLoaded agar location hanya muncul
            // jika di-load di controller (cth: Asset::with('location')->get())
            // Pastikan Anda sudah membuat LocationResource
            "room" => new RoomResource($this->whenLoaded("room")),

            "condition" => $this->condition,
            "ipVlan" => $this->ip_vlan,
            "vlan" => $this->vlan,
            "portAcsVlan" => $this->port_acs_vlan,
            "portCapacity" => $this->port_capacity,
            "portTrunk" => $this->port_trunk,
            "osVersion" => $this->os_version,

            // Date formatting: Mengubah object Carbon menjadi ISO 8601 String
            // Operator '?' digunakan untuk safety jika datanya null
            "eosDate" => $this->eos_date
                ? \Date::parse($this->eos_date)->toIso8601String()
                : null,
            "purchaseYear" => $this->purchase_year,

            "createdAt" => \Date::parse($this->created_at)->toIso8601String(),
            "updatedAt" => \Date::parse($this->updated_at)->toIso8601String(),
            "deletedAt" => $this->deleted_at
                ? \Date::parse($this->deleted_at)->toIso8601String()
                : null,
        ];
    }
}
