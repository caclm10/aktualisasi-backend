<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    use ApiResponse;

    /**
     * Logout.
     *
     * Mengakhiri sesi user yang sedang login dan menghapus token autentikasi.
     */
    public function __invoke(Request $request): JsonResponse
    {
        \Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->json(message: "Successfully logged out.");
    }
}
