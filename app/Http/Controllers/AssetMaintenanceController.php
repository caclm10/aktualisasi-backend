<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\Asset;
use App\Models\Enums\ActivityCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssetMaintenanceController extends Controller
{
    use ApiResponse;

    /**
     * Menambah aktivitas pemeliharaan pada aset.
     *
     * Mencatat perubahan pada Versi OS, Kondisi, atau Baseline.
     * Hanya satu properti yang dapat diubah dalam satu waktu.
     */
    public function __invoke(Request $request, Asset $asset): JsonResponse
    {
        $validated = $request->validate([
            "property" => [
                "required",
                "string",
                Rule::in(["osVersion", "condition", "baseline"]),
            ],
            "new" => ["required", "string", "max:255"],
            "remarks" => ["nullable", "string"],
            "performedAt" => ["nullable", "date"],
        ]);

        $property = \Str::snake($validated["property"]);
        $newValue = $validated["new"];

        // Handle enum fields - ambil value dari enum jika ada
        $rawOldValue = $asset->{$property};
        if ($rawOldValue instanceof \BackedEnum) {
            $oldValue = $rawOldValue->value;
        } else {
            $oldValue = (string) ($rawOldValue ?? "");
        }

        // Jika nilai sama, tidak perlu update
        if ($oldValue === $newValue) {
            return $this->json(
                statusCode: 422,
                message: "Nilai baru sama dengan nilai lama.",
            );
        }

        $activity = DB::transaction(function () use (
            $asset,
            $property,
            $oldValue,
            $newValue,
            $validated,
            $request,
        ) {
            // 1. Buat record aktivitas
            $activity = new Activity([
                "category" => ActivityCategory::Pemeliharaan,
                "property" => $property,
                "old" => $oldValue,
                "new" => $newValue,
                "remarks" => $validated["remarks"] ?? null,
                "performed_at" => $validated["performedAt"] ?? now(),
            ]);

            $activity->user()->associate($request->user());
            $activity->asset()->associate($asset);
            $activity->room_id = $asset->room_id;

            $activity->save();

            // 2. Update data asset
            $asset->{$property} = $newValue;
            $asset->save();

            return $activity;
        });

        $activity->load(["user", "asset", "room.office"]);

        return $this->json(
            data: new ActivityResource($activity),
            statusCode: 201,
            message: "Maintenance log created successfully.",
        );
    }
}
