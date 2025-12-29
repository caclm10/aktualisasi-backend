<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::prefix("api")->group(function () {
    Route::post("login", LoginController::class);
    Route::delete("logout", LogoutController::class);
});

Route::get("/files/{path}", function ($path) {
    abort_unless(Storage::disk("public")->exists($path), 404);

    return Storage::disk("public")->response($path);
})->where("path", ".*");
