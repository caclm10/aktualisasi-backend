<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AssetActivityController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetImageController;
use App\Http\Controllers\AssetMaintenanceController;
use App\Http\Controllers\AssetMutationController;
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

    Route::apiResource(
        "assets.activities",
        AssetActivityController::class,
    )->only(["index"]);

    // Asset Image Routes
    Route::post("assets/{asset}/image", [
        AssetImageController::class,
        "update",
    ]);
    Route::delete("assets/{asset}/image", [
        AssetImageController::class,
        "destroy",
    ]);

    Route::post(
        "assets/{asset}/maintenance",
        AssetMaintenanceController::class,
    );

    Route::post("assets/{asset}/mutation", AssetMutationController::class);

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
        "show",
        "update",
        "destroy",
    ]);
});
