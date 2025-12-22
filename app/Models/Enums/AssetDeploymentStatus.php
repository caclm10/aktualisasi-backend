<?php

namespace App\Models\Enums;

enum AssetDeploymentStatus: string
{
    case InStock = "in stock";
    case Deployed = "deployed";
    case Maintenance = "maintenance";
}
