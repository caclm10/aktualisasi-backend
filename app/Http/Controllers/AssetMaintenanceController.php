<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request, Asset $asset): JsonResponse
    {
        // TO-DO: Menambah aktivitas pada kategori pemeliharaan pada aset terpilih

        // Kategori Pemeliharaan untuk sementara akan dapat mengubah 3 properti, yaitu Versi OS, Kondisi, dan Baseline
        // hanya salah satu dalam satu waktu yang bisa diubah (contoh: jika sudah memilih baseline, maka tidak bisa memilih versi OS atau kondisi)
        // terdapat input remarks juga (jika ada catatan tambahan)
        // Akan ada 2 write, pertama penambahan record ke tabel activites dan kedua perubahan data pada tabel asstes

        return $this->json(statusCode: 201);
    }
}
