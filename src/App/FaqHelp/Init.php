<?php

namespace DVICloudDeploy\App\FaqHelp;

use DVICloudDeploy\App\FaqHelp\Admin\FaqHelpPage;
use DVICloudDeploy\App\FaqHelp\Support\Deprecated;

class Init
{
    public static function init(): void
    {
        Deprecated::register();
        FaqHelpPage::register();
    }
}
