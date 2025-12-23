<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetImageController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:sanctum"])->group(function () {
    Route::get("/user", [UserController::class, "index"]);

    Route::apiResource("assets", AssetController::class)->only([
        "index",
        "store",
        "show",
        "update",
        "destroy",
    ]);

    // Asset Image Routes
    Route::post("assets/{asset}/image", [
        AssetImageController::class,
        "update",
    ]);
    Route::delete("assets/{asset}/image", [
        AssetImageController::class,
        "destroy",
    ]);

    // Activity Routes
    Route::apiResource("activities", ActivityController::class)->only([
        "index",
        "store",
        "show",
        "update",
        "destroy",
    ]);

    Route::resource("offices", OfficeController::class)->only([
        "index",
        "store",
        "show",
        "update",
        "destroy",
    ]);

    Route::resource("offices.rooms", RoomController::class)->only([
        "index",
        "store",
        "update",
        "destroy",
    ]);
});
