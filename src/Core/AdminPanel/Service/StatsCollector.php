<?php

namespace DVICloudDeploy\Core\AdminPanel\Service;

use DVICloudDeploy\Core\AdminPanel\Contract\StatsProviderInterface;

class StatsCollector implements StatsProviderInterface
{
    public function collect(int $postId, string $context): array
    {
        $items = 'server' === $context
            ? $this->collectServer($postId)
            : $this->collectSite($postId);

        /**
         * Filter Admin Panel stats sidebar items.
         *
         * @param array  $items
         * @param int    $postId
         * @param string $context
         */
        return apply_filters('dvicd_admin_panel_stats_items', $items, $postId, $context);
    }

    private function collectSite(int $appId): array
    {
        $app = function_exists('WPCD_WORDPRESS_APP') ? \WPCD_WORDPRESS_APP() : null;
        if (!$app) {
            return [];
        }

        $domain = (string) get_post_meta($appId, 'wpapp_domain', true);
        $ip     = method_exists($app, 'get_ipv4_address') ? (string) $app->get_ipv4_address($appId) : '';
        $php    = method_exists($app, 'get_php_version_for_app') ? (string) $app->get_php_version_for_app($appId) : '';
        $sslOn  = method_exists($app, 'get_site_local_ssl_status') ? (bool) $app->get_site_local_ssl_status($appId) : false;
        $cache  = method_exists($app, 'get_page_cache_status') ? (string) $app->get_page_cache_status($appId) : '';
        $disk   = (string) get_post_meta($appId, 'wpapp_diskspace_used', true);
        if (empty($disk) && method_exists($app, 'get_total_disk_used')) {
            $disk = (string) $app->get_total_disk_used($appId);
        }

        $provider = method_exists($app, 'get_server_provider') ? (string) $app->get_server_provider($appId) : '';
        if ($provider && function_exists('WPCD') && method_exists(\WPCD(), 'wpcd_get_cloud_provider_desc')) {
            $provider = (string) \WPCD()->wpcd_get_cloud_provider_desc($provider);
        }

        return [
            ['label' => __('Domain', 'wpcd'), 'value' => $domain ?: '—'],
            ['label' => __('IP', 'wpcd'), 'value' => $ip ?: '—'],
            ['label' => __('Provider', 'wpcd'), 'value' => $provider ?: '—'],
            ['label' => __('PHP', 'wpcd'), 'value' => $php ?: '—'],
            [
                'label'  => __('SSL', 'wpcd'),
                'value'  => $sslOn ? __('On', 'wpcd') : __('Off', 'wpcd'),
                'status' => $sslOn ? 'positive' : 'negative',
            ],
            [
                'label'  => __('Cache', 'wpcd'),
                'value'  => ('on' === $cache) ? __('On', 'wpcd') : __('Off', 'wpcd'),
                'status' => ('on' === $cache) ? 'positive' : 'neutral',
            ],
            ['label' => __('Disk', 'wpcd'), 'value' => $disk ?: '—'],
        ];
    }

    private function collectServer(int $serverId): array
    {
        $ip       = (string) get_post_meta($serverId, 'wpcd_server_ipv4', true);
        $provider = (string) get_post_meta($serverId, 'wpcd_server_provider', true);
        $region   = (string) get_post_meta($serverId, 'wpcd_server_region', true);
        $state    = (string) get_post_meta($serverId, 'wpcd_server_current_state', true);
        $status   = get_post_meta($serverId, 'wpcd_server_status_push', true);

        if ($provider && function_exists('WPCD') && method_exists(\WPCD(), 'wpcd_get_cloud_provider_desc')) {
            $provider = (string) \WPCD()->wpcd_get_cloud_provider_desc($provider);
        }

        $disk = '—';
        if (is_array($status)) {
            if (!empty($status['disk_used']) && !empty($status['disk_total'])) {
                $disk = $status['disk_used'] . ' / ' . $status['disk_total'];
            } elseif (!empty($status['disk_used'])) {
                $disk = (string) $status['disk_used'];
            }
        }

        $stateOk = in_array(strtolower($state), ['active', 'on', 'running'], true);

        return [
            ['label' => __('IP', 'wpcd'), 'value' => $ip ?: '—'],
            ['label' => __('Provider', 'wpcd'), 'value' => $provider ?: '—'],
            ['label' => __('Region', 'wpcd'), 'value' => $region ?: '—'],
            [
                'label'  => __('Status', 'wpcd'),
                'value'  => $state ?: '—',
                'status' => $stateOk ? 'positive' : 'neutral',
            ],
            ['label' => __('Disk', 'wpcd'), 'value' => $disk],
        ];
    }
}
