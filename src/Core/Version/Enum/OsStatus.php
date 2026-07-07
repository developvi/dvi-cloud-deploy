<?php

namespace DVICloudDeploy\Core\Version\Enum;

enum OsStatus: string
{
    case Supported = 'supported';
    case Deprecated = 'deprecated';
    case EndOfLife = 'end_of_life';
    case Experimental = 'experimental';
}
