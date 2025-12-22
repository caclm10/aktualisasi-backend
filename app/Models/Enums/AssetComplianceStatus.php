<?php

namespace App;

enum AssetComplianceStatus: string
{
    case Sesuai = "sesuai";
    case TidakSesuai = "tidak sesuai";
    case Pengecualian = "pengecualian";
    case BelumDicek = "belum dicek";
}
