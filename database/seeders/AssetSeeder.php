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

        $brands = ["Cisco", "Juniper", "Huawei", "HP", "Dell"];
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

        // VLAN patterns sesuai contoh
        $vlanPatterns = [
            "40-22",
            "30-22",
            "50-22",
            "1 all",
            "104.105 0-22,23-45",
            "20-22",
            "60-22",
        ];

        // Port ACS VLAN patterns
        $portAcsPatterns = ["23", "24", "46.47", "-", "1-24", "25-48"];

        // Port capacity options
        $portCapacities = ["24", "48"];

        // Port trunk patterns
        $portTrunkPatterns = ["23", "24", "46.47", "47.48", "-"];

        // Kode lokasi untuk hostname
        $locationCodes = [
            "KP" => "KWPST",
            "KWDKI" => "DKIJKT",
            "BGR" => "KWBGR",
            "MDN" => "KWMDN",
            "SMG" => "KWSMG",
        ];

        $counter = 1;

        foreach ($rooms as $room) {
            // Setiap ruangan mendapat 2-5 aset
            $assetCount = rand(2, 5);

            // Ambil kode lokasi dari room code
            $roomPrefix = explode("-", $room->code)[0];
            $locationCode = $locationCodes[$roomPrefix] ?? "CX";

            for ($i = 0; $i < $assetCount; $i++) {
                $brand = $brands[array_rand($brands)];
                $model = $models[$brand][array_rand($models[$brand])];
                $condition = $conditions[array_rand($conditions)];
                $baseline = $baselines[array_rand($baselines)];
                $portCapacity = $portCapacities[array_rand($portCapacities)];

                // Generate serial number format seperti contoh: JY0220141513
                $serialPrefix = ["JY", "JW", "JX", "JZ"][rand(0, 3)];
                $serialNumber = sprintf(
                    "%s%02d%08d",
                    $serialPrefix,
                    rand(1, 22),
                    rand(10000000, 99999999),
                );

                // Generate hostname format: CX01KWACEH, CX02KWBANDUNG
                $hostname = sprintf(
                    "CX%02d%s-%s",
                    $counter,
                    $locationCode,
                    chr(65 + rand(0, 5)), // A-F suffix
                );

                // Generate IP VLAN format: xx.xx.xx.xxx
                $ipVlan = sprintf(
                    "%d.%d.%d.%d",
                    rand(10, 99),
                    rand(1, 99),
                    rand(1, 99),
                    rand(100, 255),
                );

                // Generate EOS date dengan bulan yang berbeda
                $eosDate = now()
                    ->addYears(rand(1, 5))
                    ->addMonths(rand(0, 11))
                    ->addDays(rand(1, 28))
                    ->format("Y-m-d");

                $room->assets()->create([
                    "register_code" => sprintf(
                        "REG-%s-%04d",
                        rand(2020, 2024),
                        $counter,
                    ),
                    "serial_number" => $serialNumber,
                    "hostname" => $hostname,
                    "brand" => $brand,
                    "model" => $model,
                    "condition" => $condition,
                    "baseline" => $baseline,
                    "ip_vlan" => $ipVlan,
                    "vlan" => $vlanPatterns[array_rand($vlanPatterns)],
                    "port_acs_vlan" =>
                        $portAcsPatterns[array_rand($portAcsPatterns)],
                    "port_trunk" =>
                        $portTrunkPatterns[array_rand($portTrunkPatterns)],
                    "port_capacity" => $portCapacity,
                    "os_version" => $osVersions[array_rand($osVersions)],
                    "eos_date" => $eosDate,
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
