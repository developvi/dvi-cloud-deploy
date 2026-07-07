<?php

namespace DVICloudDeploy\Core\Version\Enum;

enum WebRepo: string
{
    case OndrejNginxPpa = 'ondrej_nginx_ppa';
    case Sury = 'sury';
    case None = 'none';
}
