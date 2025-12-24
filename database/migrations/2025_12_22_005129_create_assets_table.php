<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("assets", function (Blueprint $table) {
            $table->nanoid();

            $table
                ->foreignNanoid("room_id")
                ->constrained("rooms")
                ->restrictOnDelete();

            // Identitas utama
            $table->string("register_code")->unique();
            $table->string("serial_number")->unique();
            $table->string("hostname")->unique();
            $table->string("brand");
            $table->string("model");

            $table
                ->enum("condition", ["baik", "rusak", "rusak berat"])
                ->default("baik");

            // Network
            $table->string("ip_vlan")->nullable();
            $table->string("vlan")->nullable();
            $table->string("port_acs_vlan")->nullable();
            $table->string("port_trunk")->nullable();
            $table->string("port_capacity")->nullable();
            $table
                ->enum("compliance_status", [
                    "sesuai",
                    "tidak sesuai",
                    "pengecualian", // jika ada dispensasi
                    "belum dicek",
                ])
                ->default("belum dicek");

            $table->string("os_version")->nullable();

            // Waktu
            $table->date("eos_date")->nullable();
            $table->year("purchase_year")->nullable();

            $table->string("image_url")->nullable();

            $table->decimal("price", 15, 0)->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("assets");
    }
};
