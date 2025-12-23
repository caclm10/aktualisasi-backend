<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\Asset;
use App\Models\Enums\ActivityCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Activity::with(["user", "asset", "room.office"]);

        if ($search = $request->input("search")) {
            $query->where(function ($q) use ($search) {
                $q->where("type", "like", "%{$search}%")->orWhere(
                    "remarks",
                    "like",
                    "%{$search}%",
                );
            });
        }

        // Filter by category
        if ($category = $request->input("category")) {
            $query->where("category", $category);
        }

        // Filter by asset
        if ($assetId = $request->input("asset_id")) {
            $query->where("asset_id", $assetId);
        }

        // Filter by room
        if ($roomId = $request->input("room_id")) {
            $query->where("room_id", $roomId);
        }

        // Filter by user
        if ($userId = $request->input("user_id")) {
            $query->where("user_id", $userId);
        }

        // Sorting
        $sortBy = $request->input("sort_by", "created_at");
        $sortOrder = $request->input("sort_order", "desc");
        $query->orderBy($sortBy, $sortOrder);

        $activities = $query->get();

        return $this->json(
            data: ActivityResource::collection($activities),
            message: "Activities fetched successfully.",
        );
    }

    public function store(ActivityRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $activity = DB::transaction(function () use ($validated, $request) {
            $asset = Asset::findOrFail($validated["assetId"]);

            $activity = new Activity([
                "category" => $validated["category"],
                "type" => $validated["type"],
                "remarks" => $validated["remarks"] ?? null,
                "status_snapshot" => $validated["statusSnapshot"] ?? null,
                "properties" => $validated["properties"] ?? null,
            ]);

            $activity->user()->associate($request->user());
            $activity->asset()->associate($asset);

            if (isset($validated["roomId"])) {
                $activity->room_id = $validated["roomId"];

                // Jika kategori adalah Perjalanan (mutasi), update lokasi asset
                if (
                    $validated["category"] ===
                    ActivityCategory::Perjalanan->value
                ) {
                    $asset->room_id = $validated["roomId"];
                    $asset->save();
                }
            }

            $activity->save();

            return $activity;
        });

        $activity->load(["user", "asset", "room.office"]);

        return $this->json(
            data: new ActivityResource($activity),
            statusCode: 201,
            message: "Activity created successfully.",
        );
    }

    public function show(Activity $activity): JsonResponse
    {
        $activity->load(["user", "asset", "room.office"]);

        return $this->json(
            data: new ActivityResource($activity),
            message: "Activity fetched successfully.",
        );
    }

    public function update(
        ActivityRequest $request,
        Activity $activity,
    ): JsonResponse {
        $validated = $request->validated();

        $activity->fill([
            "category" => $validated["category"],
            "type" => $validated["type"],
            "remarks" => $validated["remarks"] ?? null,
            "status_snapshot" => $validated["statusSnapshot"] ?? null,
            "properties" => $validated["properties"] ?? null,
        ]);

        if (
            isset($validated["assetId"]) &&
            $validated["assetId"] != $activity->asset_id
        ) {
            $asset = Asset::findOrFail($validated["assetId"]);
            $activity->asset()->associate($asset);
        }

        if (isset($validated["roomId"])) {
            $activity->room_id = $validated["roomId"];
        }

        $activity->save();

        $activity->load(["user", "asset", "room.office"]);

        return $this->json(
            data: new ActivityResource($activity),
            message: "Activity updated successfully.",
        );
    }

    public function destroy(Activity $activity): JsonResponse
    {
        $activity->delete();

        return $this->json(message: "Activity deleted successfully.");
    }
}
