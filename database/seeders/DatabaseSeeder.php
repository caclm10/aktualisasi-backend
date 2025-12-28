<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     "name" => "Test User",
        //     "email" => "test@example.com",
        // ]);

        $demoUsers = [
            ["name" => "Budi Santoso", "email" => "budi@demo.com"],
            ["name" => "Siti Rahayu", "email" => "siti@demo.com"],
            ["name" => "Ahmad Fauzi", "email" => "ahmad@demo.com"],
            ["name" => "Dewi Lestari", "email" => "dewi@demo.com"],
            ["name" => "Andi Wijaya", "email" => "andi@demo.com"],
            ["name" => "Rina Kusuma", "email" => "rina@demo.com"],
            ["name" => "Hendra Pratama", "email" => "hendra@demo.com"],
        ];

        foreach ($demoUsers as $userData) {
            User::create([
                "name" => $userData["name"],
                "email" => $userData["email"],
                "password" => bcrypt("demo1234"),
            ]);
        }

        $this->call([
            OfficeSeeder::class,
            AssetSeeder::class,
            ActivitySeeder::class,
        ]);
    }
}
