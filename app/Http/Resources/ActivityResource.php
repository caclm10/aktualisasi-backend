<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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

            "category" => $this->category?->value,
            "type" => $this->type,
            "remarks" => $this->remarks,
            "statusSnapshot" => $this->status_snapshot,
            "properties" => $this->properties,

            // Relasi
            "userId" => $this->user_id,
            "user" => new UserResource($this->whenLoaded("user")),

            "assetId" => $this->asset_id,
            "asset" => new AssetResource($this->whenLoaded("asset")),

            "roomId" => $this->room_id,
            "room" => new RoomResource($this->whenLoaded("room")),

            // Timestamps
            "createdAt" => \Date::parse($this->created_at)->toIso8601String(),
            "updatedAt" => \Date::parse($this->updated_at)->toIso8601String(),
        ];
    }
}
