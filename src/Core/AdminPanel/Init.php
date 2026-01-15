<?php

namespace DVICloudDeploy\Core\AdminPanel;

use DVICloudDeploy\Core\AdminPanel\Admin\AssetManager;
use DVICloudDeploy\Core\AdminPanel\Admin\BodyClass;
use DVICloudDeploy\Core\AdminPanel\Admin\SettingsIntegration;
use DVICloudDeploy\Core\AdminPanel\PublicPortal\ListCards;
use DVICloudDeploy\Core\AdminPanel\PublicPortal\PortalShell;
use DVICloudDeploy\Core\AdminPanel\PublicPortal\StatsSidebar;
use DVICloudDeploy\Core\AdminPanel\Service\HookBridge;
use DVICloudDeploy\Core\AdminPanel\Service\MetaboxStyleAdapter;
use DVICloudDeploy\Core\AdminPanel\Support\Deprecated;

class Init
{
    public static function init()
    {
        SettingsIntegration::register();
        AssetManager::register();
        BodyClass::register();
        MetaboxStyleAdapter::register();
        HookBridge::register();
        Deprecated::register();
        PortalShell::register();
        ListCards::register();
        StatsSidebar::register();
    }
}
