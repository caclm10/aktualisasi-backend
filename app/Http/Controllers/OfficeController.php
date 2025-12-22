<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfficeResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $offices = Office::all();

        return $this->json(
            data: OfficeResource::collection($offices),
            message: "Office fetched successfully.",
        );
    }

    public function show(Office $office): JsonResponse
    {
        $office->load("rooms");

        return $this->json(
            data: new OfficeResource($office),
            message: "Data kantor berhasil diambil.",
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "picName" => ["required", "string", "max:255"],
            "picContact" => ["required", "string", "max:255"],
        ]);

        $office = new Office([
            "name" => $validated["name"],
            "pic_name" => $validated["picName"],
            "pic_contact" => $validated["picContact"],
        ]);
        $office->save();

        return $this->json(
            data: new OfficeResource($office),
            statusCode: 201,
            message: "New office created successfully.",
        );
    }

    public function update(Request $request, Office $office): JsonResponse
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "picName" => ["required", "string", "max:255"],
            "picContact" => ["required", "string", "max:255"],
        ]);

        $office->fill([
            "name" => $validated["name"],
            "pic_name" => $validated["picName"],
            "pic_contact" => $validated["picContact"],
        ]);
        $office->save();

        return $this->json(
            data: new OfficeResource($office),
            message: "Office data updated successfully.",
        );
    }

    public function destroy(Office $office): JsonResponse
    {
        \DB::transaction(function () use ($office) {
            // A. Hapus semua ruangan di kantor ini (Soft Delete)
            $office->rooms()->delete();

            // B. Hapus kantornya sendiri (Soft Delete)
            $office->delete();
        });

        return $this->json(message: "An office deleted successfully.");
    }
}
