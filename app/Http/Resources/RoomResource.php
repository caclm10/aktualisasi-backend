<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            "floor" => $this->floor,
            "code" => $this->code,

            "officeId" => $this->office_id,
            "office" => new OfficeResource($this->whenLoaded("office")),
        ];
    }
}
