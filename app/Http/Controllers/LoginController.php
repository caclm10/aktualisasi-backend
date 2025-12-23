<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use ApiResponse;

    /**
     * Login.
     *
     * Melakukan autentikasi user dengan email dan password.
     * Mengembalikan data user jika berhasil login.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"],
            "remember" => ["boolean"],
        ]);
        $credentials = $request->only(["email", "password"]);

        $isValid = \Auth::attempt($credentials, $validated["remember"]);
        if (!$isValid) {
            throw ValidationException::withMessages([
                "email" => __("auth.failed"),
            ]);
        }

        $request->session()->regenerate();

        return $this->json(
            data: new UserResource(\Auth::user()),
            message: "Logged in successfully.",
        );
    }
}
