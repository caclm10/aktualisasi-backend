<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Models\Room;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of assets with pagination and search.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Asset::with("room.office");

        // Search functionality
        if ($search = $request->input("search")) {
            $query->where(function ($q) use ($search) {
                $q->where("serial_number", "like", "%{$search}%")
                    ->orWhere("hostname", "like", "%{$search}%")
                    ->orWhere("brand", "like", "%{$search}%")
                    ->orWhere("model", "like", "%{$search}%")
                    ->orWhere("ip_vlan", "like", "%{$search}%");
            });
        }

        // Filter by location
        if ($room = $request->input("room")) {
            $query->where("room_id", $room);
        }

        // Filter by condition
        if ($condition = $request->input("condition")) {
            $query->where("condition", $condition);
        }

        // Filter by deployment status
        if ($deploymentStatus = $request->input("deployment_status")) {
            $query->where("deployment_status", $deploymentStatus);
        }

        // Sorting
        $sortBy = $request->input("sort_by", "created_at");
        $sortOrder = $request->input("sort_order", "desc");
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        // $perPage = $request->input("per_page", 15);
        // $assets = $query->paginate($perPage);

        $assets = $query->get();

        return $this->json(
            data: AssetResource::collection($assets),
            message: "Assets fetched successfully.",
        );
    }

    /**
     * Store a newly created asset.
     */
    public function store(AssetRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $room = Room::with("office")->find($validated["room"]);
        unset($validated["room"]);

        $asset = $room->assets()->create($this->fill($validated));

        $asset->setRelation("room", $room);

        return $this->json(
            data: new AssetResource($asset),
            statusCode: 201,
            message: "Asset created successfully.",
        );
    }

    /**
     * Display the specified asset.
     */
    public function show(Asset $asset): JsonResponse
    {
        $asset->load("room.office");

        return $this->json(
            data: new AssetResource($asset),
            message: "Asset fetched successfully.",
        );
    }

    /**
     * Update the specified asset.
     */
    public function update(AssetRequest $request, Asset $asset): JsonResponse
    {
        $validated = $request->validated();

        $room = null;
        if ($validated["room"] != $asset->room_id) {
            $room = Room::query()->findOrFail($validated["room"]);
        }

        $asset->fill($this->fill($validated));

        if ($room) {
            $asset->room()->associate($room);
        }

        $asset->save();

        $asset->load("room.office");

        return $this->json(
            data: new AssetResource($asset),
            message: "Asset updated successfully.",
        );
    }

    /**
     * Remove the specified asset (soft delete).
     */
    public function destroy(Asset $asset): JsonResponse
    {
        $asset->delete();

        return $this->json(message: "Asset deleted successfully.");
    }

    protected function fill(array $data)
    {
        return [
            ...$data,
            "register_code" => $data["registerCode"],
            "serial_number" => $data["serialNumber"],
            "deployment_status" => $data["deploymentStatus"],
            "ip_vlan" => $data["ipVlan"],
            "port_capacity" => $data["portCapacity"],
            "port_trunk" => $data["portTrunk"],
            "os_verison" => $data["osVersion"],
            "eos_date" => $data["eosDate"],
            "purchase_year" => $data["purchaseYear"],
            "compliance_status" => $data["complianceStatus"],
        ];
    }
}
