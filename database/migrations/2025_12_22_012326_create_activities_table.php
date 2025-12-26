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
        Schema::create("activities", function (Blueprint $table) {
            $table->nanoid();

            $table->foreignNanoid("user_id")->constrained("users");

            $table->foreignNanoid("asset_id")->constrained("assets");
            $table->foreignNanoid("room_id")->constrained("rooms");

            $table->enum("category", ["perjalanan", "pemeliharaan"]);

            $table->string("property");

            $table->string("old");

            $table->string("new");

            $table->text("remarks")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("activities");
    }
};
