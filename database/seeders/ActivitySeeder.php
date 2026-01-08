<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Asset;
use App\Models\Enums\ActivityCategory;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = Asset::with("room.office")->get();
        $users = User::all();
        $rooms = Room::with("office")->get();

        if ($assets->isEmpty()) {
            $this->command->warn(
                "No assets found. Please run AssetSeeder first.",
            );
            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn("No user found. Please create a user first.");
            return;
        }

        $conditions = ["baik", "rusak", "rusak berat"];
        $baselines = ["sesuai", "tidak sesuai", "pengecualian", "belum dicek"];
        $osVersions = [
            "15.2(7)E",
            "16.12.4",
            "17.3.2",
            "IOS XE 17.6",
            "JunOS 21.4R1",
            "IOS 15.4",
            "IOS XE 16.9",
        ];

        $maintenanceRemarks = [
            "Update firmware rutin",
            "Perbaikan port mati",
            "Penggantian power supply",
            "Reset konfigurasi",
            "Upgrade kapasitas",
            "Pengecekan berkala",
            "Perbaikan koneksi",
        ];

        $mutationRemarks = [
            "Relokasi untuk kebutuhan proyek",
            "Pemindahan ke ruang baru",
            "Konsolidasi perangkat",
            "Rotasi inventaris",
            "Kebutuhan operasional",
            "Penempatan ulang",
        ];

        $counter = 0;

        foreach ($assets as $asset) {
            // Track current state untuk setiap asset
            $currentRoomId = $asset->room_id;
            $currentRoom = $asset->room;
            $currentOsVersion = $asset->os_version;
            $currentCondition = $asset->condition?->value ?? "baik";
            $currentBaseline = $asset->baseline?->value ?? "belum dicek";

            // Setiap asset mendapat 0 atau 1 aktivitas (agar total tidak melebihi jumlah aset)
            $activityCount = rand(0, 1);

            // Generate tanggal yang berurutan (dari yang paling lama)
            $dates = [];
            for ($i = 0; $i < $activityCount; $i++) {
                $dates[] = now()
                    ->subDays(rand(1 + $i * 100, 100 + $i * 100))
                    ->subHours(rand(0, 23))
                    ->subMinutes(rand(0, 59));
            }
            // Sort dari paling lama ke terbaru
            usort($dates, fn($a, $b) => $a->timestamp <=> $b->timestamp);

            for ($i = 0; $i < $activityCount; $i++) {
                // Random category
                $category = rand(0, 1)
                    ? ActivityCategory::Pemeliharaan
                    : ActivityCategory::Perjalanan;

                $performedAt = $dates[$i];

                if ($category === ActivityCategory::Pemeliharaan) {
                    // Aktivitas pemeliharaan
                    $property = ["os_version", "condition", "baseline"][
                        rand(0, 2)
                    ];

                    if ($property === "os_version") {
                        $old =
                            $currentOsVersion ??
                            $osVersions[array_rand($osVersions)];
                        $new = $osVersions[array_rand($osVersions)];
                        while ($old === $new) {
                            $new = $osVersions[array_rand($osVersions)];
                        }
                        $currentOsVersion = $new;
                    } elseif ($property === "condition") {
                        $old = $currentCondition;
                        $new = $conditions[array_rand($conditions)];
                        while ($old === $new) {
                            $new = $conditions[array_rand($conditions)];
                        }
                        $currentCondition = $new;
                    } else {
                        $old = $currentBaseline;
                        $new = $baselines[array_rand($baselines)];
                        while ($old === $new) {
                            $new = $baselines[array_rand($baselines)];
                        }
                        $currentBaseline = $new;
                    }

                    Activity::create([
                        "user_id" => $users->random()->id,
                        "asset_id" => $asset->id,
                        "room_id" => $currentRoomId,
                        "category" => $category,
                        "property" => $property,
                        "old" => $old,
                        "new" => $new,
                        "remarks" =>
                            $maintenanceRemarks[
                                array_rand($maintenanceRemarks)
                            ],
                        "performed_at" => $performedAt,
                    ]);
                } else {
                    // Aktivitas perjalanan/mutasi
                    $oldRoom = $currentRoom;
                    $newRoom = $rooms
                        ->where("id", "!=", $currentRoomId)
                        ->random();

                    $oldRoomName = $oldRoom
                        ? "{$oldRoom->name} - {$oldRoom->office->name} (Lantai {$oldRoom->floor})"
                        : "";
                    $newRoomName = $newRoom
                        ? "{$newRoom->name} - {$newRoom->office->name} (Lantai {$newRoom->floor})"
                        : "";

                    Activity::create([
                        "user_id" => $users->random()->id,
                        "asset_id" => $asset->id,
                        "room_id" => $newRoom->id,
                        "category" => $category,
                        "property" => "room_id",
                        "old" => $oldRoomName,
                        "new" => $newRoomName,
                        "remarks" =>
                            $mutationRemarks[array_rand($mutationRemarks)],
                        "performed_at" => $performedAt,
                    ]);

                    // Update current state setelah mutasi
                    $currentRoomId = $newRoom->id;
                    $currentRoom = $newRoom;
                }

                $counter++;
            }

            // Update asset ke lokasi terakhir setelah semua aktivitas
            $asset->room_id = $currentRoomId;
            $asset->os_version = $currentOsVersion;
            $asset->condition = $currentCondition;
            $asset->baseline = $currentBaseline;
            $asset->save();
        }

        $this->command->info(
            "Created " .
                $counter .
                " activities for " .
                $assets->count() .
                " assets.",
        );
    }
}
