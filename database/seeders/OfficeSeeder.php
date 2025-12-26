<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Office;
use App\Models\Room;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            [
                "name" => "Kantor Pusat",
                "pic_name" => "Budi Santoso",
                "pic_contact" => "081234567890",
                "rooms" => [
                    [
                        "name" => "Server Room Utama",
                        "floor" => "2",
                        "code" => "KP-SR-01",
                    ],
                    [
                        "name" => "NOC",
                        "floor" => "2",
                        "code" => "KP-NOC-01",
                    ],
                    [
                        "name" => "Ruang IT",
                        "floor" => "3",
                        "code" => "KP-IT-01",
                    ],
                    [
                        "name" => "Data Center",
                        "floor" => "Basement",
                        "code" => "KP-DC-01",
                    ],
                ],
            ],
            [
                "name" => "Kanwil DKI Jakarta",
                "pic_name" => "Andi Wijaya",
                "pic_contact" => "081234567891",
                "rooms" => [
                    [
                        "name" => "Server Room",
                        "floor" => "1",
                        "code" => "KWDKI-SR-01",
                    ],
                    [
                        "name" => "Ruang IT",
                        "floor" => "1",
                        "code" => "KWDKI-IT-01",
                    ],
                    [
                        "name" => "Ruang Meeting",
                        "floor" => "2",
                        "code" => "KWDKI-MR-01",
                    ],
                ],
            ],
            [
                "name" => "KPKNL Bogor",
                "pic_name" => "Siti Rahayu",
                "pic_contact" => "081234567892",
                "rooms" => [
                    [
                        "name" => "Server Room",
                        "floor" => "1",
                        "code" => "BGR-SR-01",
                    ],
                    [
                        "name" => "Ruang IT",
                        "floor" => "1",
                        "code" => "BGR-IT-01",
                    ],
                ],
            ],
            [
                "name" => "KPKNL Medan",
                "pic_name" => "Ahmad Fauzi",
                "pic_contact" => "081234567893",
                "rooms" => [
                    [
                        "name" => "Server Room",
                        "floor" => "1",
                        "code" => "MDN-SR-01",
                    ],
                    [
                        "name" => "Ruang IT",
                        "floor" => "2",
                        "code" => "MDN-IT-01",
                    ],
                ],
            ],
            [
                "name" => "KPKNL Semarang",
                "pic_name" => "Dewi Lestari",
                "pic_contact" => "081234567894",
                "rooms" => [
                    [
                        "name" => "Server Room",
                        "floor" => "1",
                        "code" => "SMG-SR-01",
                    ],
                    [
                        "name" => "Ruang IT",
                        "floor" => "1",
                        "code" => "SMG-IT-01",
                    ],
                ],
            ],
        ];

        foreach ($offices as $officeData) {
            $rooms = $officeData["rooms"];
            unset($officeData["rooms"]);

            $office = Office::create($officeData);

            foreach ($rooms as $roomData) {
                $office->rooms()->create($roomData);
            }
        }
    }
}
