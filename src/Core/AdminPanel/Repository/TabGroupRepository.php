<?php

namespace DVICloudDeploy\Core\AdminPanel\Repository;

use DVICloudDeploy\Core\AdminPanel\Contract\TabGroupRepositoryInterface;

class TabGroupRepository implements TabGroupRepositoryInterface
{
    public function get(string $context): array
    {
        $map = 'server' === $context ? $this->serverDefaults() : $this->siteDefaults();

        /**
         * Filter Admin Panel tab groups and icons.
         *
         * @param array  $map
         * @param string $context site|server
         */
        return apply_filters('dvicd_admin_panel_tab_groups', $map, $context);
    }

    private function siteDefaults(): array
    {
        return [
            'groups' => [
                'general'     => ['label' => __('General', 'wpcd'), 'order' => 10, 'icon' => 'fas fa-house'],
                'files'       => ['label' => __('Files', 'wpcd'), 'order' => 20, 'icon' => 'fas fa-folder-open'],
                'database'    => ['label' => __('Databases', 'wpcd'), 'order' => 30, 'icon' => 'fas fa-database'],
                'domains'     => ['label' => __('Domains & SSL', 'wpcd'), 'order' => 40, 'icon' => 'fas fa-globe'],
                'security'    => ['label' => __('Security', 'wpcd'), 'order' => 50, 'icon' => 'fas fa-shield-halved'],
                'performance' => ['label' => __('Performance', 'wpcd'), 'order' => 60, 'icon' => 'fas fa-gauge-high'],
                'development' => ['label' => __('Development', 'wpcd'), 'order' => 70, 'icon' => 'fas fa-code'],
                'monitoring'  => ['label' => __('Monitoring', 'wpcd'), 'order' => 80, 'icon' => 'fas fa-chart-line'],
                'other'       => ['label' => __('Other', 'wpcd'), 'order' => 90, 'icon' => 'fas fa-ellipsis'],
            ],
            'tabs'   => [
                'general'                  => ['group' => 'general', 'icon' => 'fas fa-circle-info'],
                'misc'                     => ['group' => 'general', 'icon' => 'fas fa-gear'],
                'tools'                    => ['group' => 'general', 'icon' => 'fas fa-wrench'],
                'wpconfig'                 => ['group' => 'general', 'icon' => 'fas fa-file-code'],
                'theme-and-plugin-updates' => ['group' => 'general', 'icon' => 'fas fa-rotate'],
                'file-manager'             => ['group' => 'files', 'icon' => 'fas fa-folder-tree'],
                'backup'                   => ['group' => 'files', 'icon' => 'fas fa-cloud-arrow-up'],
                'sftp'                     => ['group' => 'files', 'icon' => 'fas fa-right-left'],
                'database'                 => ['group' => 'database', 'icon' => 'fas fa-database'],
                'change-domain'            => ['group' => 'domains', 'icon' => 'fas fa-right-left'],
                'ssl'                      => ['group' => 'domains', 'icon' => 'fas fa-lock'],
                'redirect-rules'           => ['group' => 'domains', 'icon' => 'fas fa-diamond-turn-right'],
                'site-security'            => ['group' => 'security', 'icon' => 'fas fa-shield-halved'],
                '6g_waf'                   => ['group' => 'security', 'icon' => 'fas fa-fire'],
                '7g_waf'                   => ['group' => 'security', 'icon' => 'fas fa-user-shield'],
                'wp-site-users'            => ['group' => 'security', 'icon' => 'fas fa-users'],
                'site-system-users'        => ['group' => 'security', 'icon' => 'fas fa-user-lock'],
                'cache'                    => ['group' => 'performance', 'icon' => 'fas fa-bolt'],
                'php-options'              => ['group' => 'performance', 'icon' => 'fab fa-php'],
                'tweaks'                   => ['group' => 'performance', 'icon' => 'fas fa-sliders'],
                'staging'                  => ['group' => 'development', 'icon' => 'fas fa-flask'],
                'clone-site'               => ['group' => 'development', 'icon' => 'fas fa-clone'],
                'copy-to-existing-site'    => ['group' => 'development', 'icon' => 'fas fa-copy'],
                'site-sync'                => ['group' => 'development', 'icon' => 'fas fa-rotate'],
                'git-site-control'         => ['group' => 'development', 'icon' => 'fab fa-git-alt'],
                'multitenant-site'         => ['group' => 'development', 'icon' => 'fas fa-sitemap'],
                'statistics'               => ['group' => 'monitoring', 'icon' => 'fas fa-chart-column'],
                'site-logs'                => ['group' => 'monitoring', 'icon' => 'fas fa-file-lines'],
                'crons'                    => ['group' => 'monitoring', 'icon' => 'fas fa-clock'],
            ],
        ];
    }

    private function serverDefaults(): array
    {
        return [
            'groups' => [
                'general'    => ['label' => __('General', 'wpcd'), 'order' => 10, 'icon' => 'fas fa-server'],
                'services'   => ['label' => __('Services', 'wpcd'), 'order' => 20, 'icon' => 'fas fa-gears'],
                'security'   => ['label' => __('Security', 'wpcd'), 'order' => 30, 'icon' => 'fas fa-shield-halved'],
                'backup'     => ['label' => __('Backup', 'wpcd'), 'order' => 40, 'icon' => 'fas fa-cloud-arrow-up'],
                'monitoring' => ['label' => __('Monitoring', 'wpcd'), 'order' => 50, 'icon' => 'fas fa-chart-line'],
                'power'      => ['label' => __('Power & Resize', 'wpcd'), 'order' => 60, 'icon' => 'fas fa-power-off'],
                'other'      => ['label' => __('Other', 'wpcd'), 'order' => 90, 'icon' => 'fas fa-ellipsis'],
            ],
            'tabs'   => [
                'general'            => ['group' => 'general', 'icon' => 'fas fa-circle-info'],
                'sites_on_server'    => ['group' => 'general', 'icon' => 'fas fa-globe'],
                'svr_tools'          => ['group' => 'general', 'icon' => 'fas fa-wrench'],
                'svr_tweaks'         => ['group' => 'general', 'icon' => 'fas fa-sliders'],
                'services'           => ['group' => 'services', 'icon' => 'fas fa-gears'],
                'ssh_console'        => ['group' => 'services', 'icon' => 'fas fa-terminal'],
                'ols_console'        => ['group' => 'services', 'icon' => 'fas fa-window-maximize'],
                'server-users'       => ['group' => 'services', 'icon' => 'fas fa-users'],
                'server-ssh-keys'    => ['group' => 'services', 'icon' => 'fas fa-key'],
                'git-server-control' => ['group' => 'services', 'icon' => 'fab fa-git-alt'],
                'firewall'           => ['group' => 'security', 'icon' => 'fas fa-fire'],
                'fail2ban'           => ['group' => 'security', 'icon' => 'fas fa-user-lock'],
                'server_backup'      => ['group' => 'backup', 'icon' => 'fas fa-cloud-arrow-up'],
                'svr_statistics'     => ['group' => 'monitoring', 'icon' => 'fas fa-chart-column'],
                'server-logs'        => ['group' => 'monitoring', 'icon' => 'fas fa-file-lines'],
                'callbacks'          => ['group' => 'monitoring', 'icon' => 'fas fa-satellite-dish'],
                'monit-healing'      => ['group' => 'monitoring', 'icon' => 'fas fa-heart-pulse'],
                'monitorix'          => ['group' => 'monitoring', 'icon' => 'fas fa-desktop'],
                'goaccess'           => ['group' => 'monitoring', 'icon' => 'fas fa-chart-line'],
                'svr_power'          => ['group' => 'power', 'icon' => 'fas fa-power-off'],
                'resize'             => ['group' => 'power', 'icon' => 'fas fa-up-right-and-down-left-from-center'],
                'server_upgrade'     => ['group' => 'power', 'icon' => 'fas fa-arrow-up'],
            ],
        ];
    }
}
