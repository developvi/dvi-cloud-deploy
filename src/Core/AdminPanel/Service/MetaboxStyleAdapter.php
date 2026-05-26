<?php

namespace DVICloudDeploy\Core\AdminPanel\Service;

use DVICloudDeploy\Core\AdminPanel\Support\StyleDetector;

/**
 * Ensures Admin Panel marker classes on tabbed metaboxes.
 */
class MetaboxStyleAdapter
{
    public static function register()
    {
        add_filter('rwmb_meta_boxes', [self::class, 'adapt'], 999);
    }

    public static function adapt(array $metaBoxes): array
    {
        if (!StyleDetector::isActive()) {
            return $metaBoxes;
        }

        foreach ($metaBoxes as $index => $box) {
            if (empty($box['tabs'])) {
                continue;
            }

            $postTypes = isset($box['post_types']) ? (array) $box['post_types'] : [];
            $pages     = isset($box['pages']) ? (array) $box['pages'] : [];
            $targets   = array_merge($postTypes, $pages);

            $isApp    = in_array('wpcd_app', $targets, true) && StyleDetector::isSiteStyle();
            $isServer = in_array('wpcd_app_server', $targets, true) && StyleDetector::isServerStyle();

            if (!$isApp && !$isServer) {
                continue;
            }

            // Metaboxes already rewrite adminpanel → left; always force seamless for AP.
            $metaBoxes[$index]['tab_style'] = 'left';
            $metaBoxes[$index]['style']     = 'seamless';

            $class = isset($box['class']) ? (string) $box['class'] : '';
            if (! str_contains( $class, 'dvicd-ap-metabox' )) {
                $metaBoxes[$index]['class'] = trim($class . ' dvicd-ap-metabox');
            }

            /**
             * Allow plugins to alter adapted Admin Panel metabox config.
             *
             * @param array $box
             * @param int   $index
             */
            $metaBoxes[$index] = apply_filters('dvicd_admin_panel_metabox', $metaBoxes[$index], $index);
        }

        return $metaBoxes;
    }
}
