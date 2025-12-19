<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        // 1. Macro untuk Primary Key NanoID
        Blueprint::macro("nanoid", function (
            $column = "id",
            $length = 21,
        ): \Illuminate\Database\Schema\ColumnDefinition {
            // $this di sini mengacu pada instance Blueprint
            return $this->char($column, $length)->primary();
        });

        // 2. Macro Foreign Key
        Blueprint::macro("foreignNanoid", function (
            $column,
            $length = 21,
        ): \Illuminate\Database\Schema\ForeignIdColumnDefinition {
            // Kita return instance ForeignIdColumnDefinition
            // supaya bisa pakai method ->constrained() nanti
            return $this->addColumnDefinition(
                new ForeignIdColumnDefinition($this, [
                    "type" => "char", // <--- WAJIB CHAR (Karena NanoID itu String)
                    "name" => $column,
                    "length" => $length, // Panjang harus sama dengan PK tabel induk (21)
                ]),
            );
        });
    }
}
