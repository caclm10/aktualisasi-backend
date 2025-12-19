<?php

namespace App\Traits;

use Hidehalo\Nanoid\Client;

trait HasNanoID
{
    /**
     * Boot function from Laravel model lifecycle.
     */
    protected static function bootHasNanoID()
    {
        // Event 'creating' dipanggil sebelum data disimpan ke DB
        static::creating(function ($model) {
            // Cek jika ID belum ada, maka buatkan baru
            if (empty($model->{$model->getKeyName()})) {
                $client = new Client();

                // Generate NanoID (panjang default 21 karakter)
                $model->{$model->getKeyName()} = $client->generateId(size: 21);
            }
        });
    }

    /**
     * Matikan auto-increment karena kita pakai string
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Beri tahu Eloquent tipe data ID adalah string
     */
    public function getKeyType()
    {
        return "string";
    }
}
