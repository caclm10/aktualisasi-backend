<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfficeResource;
use App\Http\Resources\RoomResource;
use App\Models\Office;
use App\Models\Room;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    use ApiResponse;

    public function index(Office $office): JsonResponse
    {
        $office->load("rooms");

        return $this->json(
            data: OfficeResource::collection($office),
            message: "Rooms fetched successfully.",
        );
    }

    public function store(Request $request, Office $office): JsonResponse
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "floor" => ["required", "string", "max:255"],
            "code" => ["required", "string", Rule::unique("rooms")],
        ]);

        $room = $office->rooms()->create($validated);

        return $this->json(
            data: new RoomResource($room),
            statusCode: 201,
            message: "New room created successfully.",
        );
    }

    public function update(
        Request $request,
        Office $office,
        Room $room,
    ): JsonResponse {
        if ($room->office_id !== $office->id) {
            abort(404, "Ruangan tidak ditemukan di kantor ini.");
        }

        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "floor" => ["required", "string", "max:255"],
            "code" => [
                "required",
                "string",
                Rule::unique("rooms")->ignore($room->id),
            ],
        ]);

        $room->fill($validated);
        $room->save();

        return $this->json(
            data: new RoomResource($room),
            message: "Room updated successfully.",
        );
    }

    public function destroy(Office $office, Room $room): JsonResponse
    {
        if ($room->office_id !== $office->id) {
            abort(404, "Ruangan tidak ditemukan di kantor ini.");
        }

        $room->delete();

        return $this->json(message: "Ruangan berhasil dihapus.");
    }
}
