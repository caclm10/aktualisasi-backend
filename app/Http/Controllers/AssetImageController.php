<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetImageController extends Controller
{
    use ApiResponse;

    public function update(Request $request, Asset $asset): JsonResponse
    {
        $request->validate([
            "image" => [
                "required",
                "image",
                "mimes:jpeg,png,jpg,webp",
                "max:2048",
            ],
        ]);

        // Hapus image lama jika ada
        if ($asset->image_url) {
            $oldPath = str_replace("/storage/", "", $asset->image_url);
            \Storage::disk("public")->delete($oldPath);
        }

        // Upload image baru
        $path = $request->file("image")->store("assets", "public");

        $asset->image_url = "/storage/" . $path;
        $asset->save();

        $asset->load("room.office");

        return $this->json(
            data: new AssetResource($asset),
            message: "Asset image updated successfully.",
        );
    }

    public function destroy(Asset $asset): JsonResponse
    {
        if (!$asset->image_url) {
            return $this->json(
                statusCode: 404,
                message: "Asset does not have an image.",
            );
        }

        // Hapus file dari storage
        $path = str_replace("/storage/", "", $asset->image_url);
        \Storage::disk("public")->delete($path);

        $asset->image_url = null;
        $asset->save();

        $asset->load("room.office");

        return $this->json(
            data: new AssetResource($asset),
            message: "Asset image deleted successfully.",
        );
    }
}
