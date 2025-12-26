<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetActivityController extends Controller
{
    use ApiResponse;

    public function index(Asset $asset): JsonResponse
    {
        // TO-DO: Menampilkan daftar aktivitas pada aset terpilih

        return $this->json();
    }
}
