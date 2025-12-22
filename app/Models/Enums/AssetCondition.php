<?php

namespace App\Models\Enums;

enum AssetCondition: string
{
    case Baik = 'baik';
    case Rusak = 'rusak';
    case RusakBerat = 'rusak berat';
}


