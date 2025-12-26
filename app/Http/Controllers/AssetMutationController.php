<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\Asset;
use App\Models\Enums\ActivityCategory;
use App\Models\Room;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetMutationController extends Controller
{
    use ApiResponse;

    /**
     * Memindahkan aset ke ruangan baru.
     *
     * Mencatat perpindahan/mutasi aset dari ruangan lama ke ruangan baru.
     */
    public function __invoke(Request $request, Asset $asset): JsonResponse
    {
        $validated = $request->validate([
            "roomId" => ["required", "exists:rooms,id"],
            "remarks" => ["nullable", "string"],
        ]);

        $newRoomId = $validated["roomId"];
        $oldRoomId = $asset->room_id;

        // Jika ruangan sama, tidak perlu update
        if ($oldRoomId === $newRoomId) {
            return $this->json(
                statusCode: 422,
                message: "Ruangan tujuan sama dengan ruangan saat ini.",
            );
        }

        // Ambil nama ruangan untuk disimpan di log
        $oldRoom = Room::with("office")->find($oldRoomId);
        $newRoom = Room::with("office")->find($newRoomId);

        $oldRoomName = $oldRoom
            ? "{$oldRoom->name} - {$oldRoom->office->name} (Lantai {$oldRoom->floor})"
            : "";
        $newRoomName = $newRoom
            ? "{$newRoom->name} - {$newRoom->office->name} (Lantai {$newRoom->floor})"
            : "";

        $activity = DB::transaction(function () use (
            $asset,
            $newRoomId,
            $oldRoomName,
            $newRoomName,
            $validated,
            $request,
        ) {
            // 1. Buat record aktivitas
            $activity = new Activity([
                "category" => ActivityCategory::Perjalanan,
                "property" => "room_id",
                "old" => $oldRoomName,
                "new" => $newRoomName,
                "remarks" => $validated["remarks"] ?? null,
            ]);

            $activity->user()->associate($request->user());
            $activity->asset()->associate($asset);
            $activity->room_id = $newRoomId;

            $activity->save();

            // 2. Update lokasi asset
            $asset->room_id = $newRoomId;
            $asset->save();

            return $activity;
        });

        $activity->load(["user", "asset", "room.office"]);

        return $this->json(
            data: new ActivityResource($activity),
            statusCode: 201,
            message: "Asset mutation recorded successfully.",
        );
    }
}
