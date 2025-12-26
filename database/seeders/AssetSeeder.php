<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Room;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            $this->command->warn(
                "No rooms found. Please run OfficeSeeder first.",
            );
            return;
        }

        $brands = ["Cisco", "Juniper", "Huawei", "HP", "Dell", "MikroTik"];
        $models = [
            "Cisco" => [
                "Catalyst 2960",
                "Catalyst 3850",
                "ISR 4321",
                "Nexus 9000",
            ],
            "Juniper" => ["EX2300", "EX3400", "SRX300", "QFX5100"],
            "Huawei" => ["S5720", "S6720", "AR1200", "CE6800"],
            "HP" => ["ProCurve 2530", "Aruba 2930F", "FlexNetwork 5130"],
            "Dell" => [
                "PowerSwitch N1148",
                "PowerSwitch S4048",
                "EMC Networking",
            ],
            "MikroTik" => ["CCR1036", "CRS326", "RB4011", "CCR2004"],
        ];

        $conditions = ["baik", "baik", "baik", "rusak", "rusak berat"];
        $baselines = [
            "sesuai",
            "sesuai",
            "tidak sesuai",
            "pengecualian",
            "belum dicek",
        ];
        $osVersions = [
            "15.2(7)E",
            "16.12.4",
            "17.3.2",
            "IOS XE 17.6",
            "JunOS 21.4R1",
        ];

        $counter = 1;

        foreach ($rooms as $room) {
            // Setiap ruangan mendapat 2-5 aset
            $assetCount = rand(2, 5);

            for ($i = 0; $i < $assetCount; $i++) {
                $brand = $brands[array_rand($brands)];
                $model = $models[$brand][array_rand($models[$brand])];
                $condition = $conditions[array_rand($conditions)];
                $baseline = $baselines[array_rand($baselines)];

                $room->assets()->create([
                    "register_code" => sprintf(
                        "REG-%s-%04d",
                        date("Y"),
                        $counter,
                    ),
                    "serial_number" => strtoupper(substr(md5(uniqid()), 0, 12)),
                    "hostname" => sprintf(
                        "%s-SW-%03d",
                        strtoupper(substr($room->code, 0, 3)),
                        $counter,
                    ),
                    "brand" => $brand,
                    "model" => $model,
                    "condition" => $condition,
                    "baseline" => $baseline,
                    "ip_vlan" => sprintf(
                        "192.168.%d.%d",
                        rand(1, 254),
                        rand(1, 254),
                    ),
                    "vlan" => sprintf("VLAN%d", rand(10, 999)),
                    "port_capacity" => rand(24, 48) . " ports",
                    "os_version" => $osVersions[array_rand($osVersions)],
                    "eos_date" => now()->addYears(rand(1, 5))->format("Y-m-d"),
                    "purchase_year" => rand(2018, 2024),
                    "price" => rand(5, 50) * 1000000,
                ]);

                $counter++;
            }
        }

        $this->command->info(
            "Created " .
                ($counter - 1) .
                " assets across " .
                $rooms->count() .
                " rooms.",
        );
    }
}
