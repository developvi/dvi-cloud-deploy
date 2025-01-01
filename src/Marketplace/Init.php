<?php

namespace DVICloudDeploy\Marketplace;

use DVICloudDeploy\Marketplace\Admin\Menu;
use DVICloudDeploy\Marketplace\Ajax\Modal;
use DVICloudDeploy\Marketplace\Ajax\PluginActions;
use DVICloudDeploy\Marketplace\Handler\Updater;

class Init
{

    public static function init()
    {
        // Initialize all functionalities
        Menu::register(); // Add admin menu and enqueue scripts
        Updater::initiate(); // Enable plugin update checks
        PluginActions::register(); // Handle AJAX plugin actions
        Modal::register(); // Handle modal-related AJAX requests

    }
}
