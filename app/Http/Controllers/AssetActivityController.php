<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Asset;
use App\Models\Enums\ActivityCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetActivityController extends Controller
{
    use ApiResponse;

    /**
     * Menampilkan daftar aktivitas perjalanan pada aset.
     *
     * Mendapatkan riwayat mutasi/perpindahan aset antar ruangan.
     */
    public function index(Request $request, Asset $asset): JsonResponse
    {
        $query = $asset->activities()->with(["user", "room.office"]);

        // Sorting
        $sortBy = $request->input("sort_by", "created_at");
        $sortOrder = $request->input("sort_order", "desc");
        $query->orderBy($sortBy, $sortOrder);

        $activities = $query->get();

        return $this->json(
            data: ActivityResource::collection($activities),
            message: "Asset activities fetched successfully.",
        );
    }
}
