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
        $assets = Asset::with("room")->get();
        $users = User::all();
        $rooms = Room::all();

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
            // Setiap asset mendapat 1-3 aktivitas
            $activityCount = rand(1, 3);

            for ($i = 0; $i < $activityCount; $i++) {
                // Random category
                $category = rand(0, 1)
                    ? ActivityCategory::Pemeliharaan
                    : ActivityCategory::Perjalanan;

                // Generate random date dalam 1 tahun terakhir
                $performedAt = now()
                    ->subDays(rand(1, 365))
                    ->subHours(rand(0, 23))
                    ->subMinutes(rand(0, 59));

                if ($category === ActivityCategory::Pemeliharaan) {
                    // Aktivitas pemeliharaan
                    $property = ["os_version", "condition", "baseline"][
                        rand(0, 2)
                    ];

                    if ($property === "os_version") {
                        $old = $osVersions[array_rand($osVersions)];
                        $new = $osVersions[array_rand($osVersions)];
                    } elseif ($property === "condition") {
                        $old = $conditions[array_rand($conditions)];
                        $new = $conditions[array_rand($conditions)];
                    } else {
                        $old = $baselines[array_rand($baselines)];
                        $new = $baselines[array_rand($baselines)];
                    }

                    // Pastikan old != new
                    while ($old === $new) {
                        if ($property === "os_version") {
                            $new = $osVersions[array_rand($osVersions)];
                        } elseif ($property === "condition") {
                            $new = $conditions[array_rand($conditions)];
                        } else {
                            $new = $baselines[array_rand($baselines)];
                        }
                    }

                    Activity::create([
                        "user_id" => $users->random()->id,
                        "asset_id" => $asset->id,
                        "room_id" => $asset->room_id,
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
                    $oldRoom = $asset->room;
                    $newRoom = $rooms
                        ->where("id", "!=", $asset->room_id)
                        ->random();

                    $oldRoomName = $oldRoom
                        ? "{$oldRoom->name} - Lantai {$oldRoom->floor}"
                        : "";
                    $newRoomName = $newRoom
                        ? "{$newRoom->name} - Lantai {$newRoom->floor}"
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
                }

                $counter++;
            }
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
