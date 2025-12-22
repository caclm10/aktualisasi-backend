<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeResource extends JsonResource
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
            "name" => $this->name,
            "picName" => $this->pic_name,
            "picContact" => $this->pic_contact,
            "rooms" => RoomResource::collection($this->whenLoaded("rooms")),
        ];
    }
}
