<?php

namespace DVICloudDeploy\Marketplace\Handler;


/**
 * Handles plugin update functionality by comparing remote data with installed plugins.
 */
class Updater
{
    /**
     * Register hooks for plugin update checking.
     */
    public static function initiate()
    {
        add_filter('site_transient_update_plugins', [self::class, 'checkForUpdates']);
        add_filter('plugins_api', [self::class, 'updaterInfo'], 20, 3);
        add_action('upgrader_process_complete', [self::class, 'updaterpurge'], 10, 2);

    }
    public static function updaterpurge($upgrader, $options)
    {

        if ('update' === $options['action'] && 'plugin' === $options['type']) {
            // just clean the cache when new plugin version is installed
            delete_transient('dvi_marketplace_plugins');
        }
    }
    /**
     * Fetches and formats plugin information for updates using remote data.
     *
     * @param mixed $response The existing plugin update response.
     * @param string $action The current action (e.g., 'plugin_information').
     * @param object $args Additional arguments, such as the plugin slug.
     * @return mixed Updated response with plugin information or original response if no update is applicable.
     */
    public static function updaterInfo($response, $action, $args)
    {
        // Ensure the action is related to plugin information
        if ('plugin_information' !== $action) {
            return $response;
        }



        // Fetch remote plugin data
        $remote_plugins = FetchingRemotePluginsDate::getData();

        // Check if data is available and extract the relevant plugin using array_filter
        $remote = array_filter($remote_plugins, function ($plugin) use ($args) {
            return isset($plugin['slug']) && $plugin['slug'] == $args->slug;
        });

        // Get the first matching plugin
        $remote = !empty($remote) ? reset($remote) : null;

        // Return the existing response if the plugin data is not found
        if (!$remote) {
            return $response;
        }

        // Convert to object for easier handling
        $remote = (object)$remote;

        // Create and populate the response object
        $response = new \stdClass();

        $response->name           = $remote->name ?? '';
        $response->slug           = $remote->slug ?? '';
        $response->version        = $remote->version ?? '';
        $response->tested         = $remote->tested ?? '';
        $response->requires       = $remote->requires ?? '';
        $response->author         = $remote->author ?? '';
        $response->author_profile = $remote->author_profile ?? '';
        $response->donate_link    = $remote->donate_link ?? '';
        $response->homepage       = $remote->homepage ?? '';
        $response->download_link  = $remote->download_url ?? '';
        $response->trunk          = $remote->download_url ?? '';
        $response->requires_php   = $remote->requires_php ?? '';
        $response->last_updated   = $remote->last_updated ?? '';

        $response->sections = [
            'description'  => $remote->sections['description'] ?? '',
            'installation' => $remote->sections['installation'] ?? '',
            'changelog'    => $remote->sections['changelog'] ?? ''
        ];

        return $response;
    }

    /**
     * Check for updates and add them to the WordPress update transient.
     *
     * @param object $transient The existing plugins update transient.
     * @return object Modified transient with update information.
     */
    public static function checkForUpdates($transient)
    {
        // Get plugins data from the cached transient
        $remote_plugins = FetchingRemotePluginsDate::getData();

        if (empty($transient->checked)) {
            return $transient;
        }

        foreach ($remote_plugins as $remote) {
            $plugin_info = $remote['plugin_info'];
            // Compare versions and add to update list if necessary
            if (
                isset($transient->checked[$plugin_info]) &&
                version_compare($transient->checked[$plugin_info], $remote['version'], '<')
            ) {
                $response = (object)[
                    'slug' => $remote['slug'],
                    'plugin' => $plugin_info,
                    'new_version' => $remote['version'],
                    'package' => $remote['download_url'],
                ];

                $transient->response[$plugin_info] = $response;
            }
        }

        return $transient;
    }
}
