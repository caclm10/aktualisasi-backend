<?php

namespace App\Models\Enums;

enum AssetBaseline: string
{
    case Sesuai = "sesuai";
    case TidakSesuai = "tidak sesuai";
    case Pengecualian = "pengecualian";
    case BelumDicek = "belum dicek";
}
