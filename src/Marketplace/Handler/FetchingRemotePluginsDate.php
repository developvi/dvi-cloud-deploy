<?php
namespace DVICloudDeploy\Marketplace\Handler;

/**
 * Handles plugin  fetching remote plugins data.
 */
class FetchingRemotePluginsDate
{


    /**
     * Fetch plugins data from the API and cache it using set_transient.
     *
     * @return array Plugins data from API or transient.
     */
    public static function getData()
    {
        // Check if the data is already cached
        $plugins_data = get_transient('dvi_marketplace_plugins');
        // If no cached data, fetch from API
        if (!$plugins_data) {
            $remote = wp_remote_get(
                'https://developvi.com/dvicd.json',
                [
                    'timeout' => 10,
                    'headers' => ['Accept' => 'application/json'],
                ]
            );

            // Handle errors or invalid responses
            if (is_wp_error($remote) || 200 !== wp_remote_retrieve_response_code($remote)) {
                return []; // Return an empty array in case of errors
            }

            $plugins_data = json_decode(wp_remote_retrieve_body($remote), true);
            // Cache the data for 1 day
            set_transient('dvi_marketplace_plugins', $plugins_data, DAY_IN_SECONDS);
        }
        
        if(!is_array($plugins_data)) {
            return []; // Return an empty array if the data is not in the expected format
        }
        return $plugins_data;
    }
}
