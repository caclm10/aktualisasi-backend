<?php

namespace App\Models;

use App\Traits\HasNanoID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    use HasNanoID;

    protected $fillable = ["name", "pic_name", "pic_contact"];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
