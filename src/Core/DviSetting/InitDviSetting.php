<?php

namespace DVICloudDeploy\Core\DviSetting;


class InitDviSetting
{


    public function init()
    {
        add_filter('wpcd_settings_metaboxes', [$this, 'MetaBoxes']);
        add_filter('wpcd_settings_tabs', [$this, 'Taps']);
    }

    public function MetaBoxes($meta_boxes)
    {

        $meta_boxes[] = [
            'id'             => 'wordpress-dns-settings',
            'title'          => __('High Performance', 'wpcd'),
            'settings_pages' => 'wpcd_settings',
            'tab'            => 'high-performance',
            'fields'         => [
                [
                    'name' => __('High Performance - Separate Posts', 'wpcd'),
                    'type' => 'heading',
                    'desc' => __('Enabling this option will separate posts into different tables, which improves database performance and website responsiveness.', 'wpcd'),
                ],
                [
                    'name' => __('Enable high performance Commands Logs <span style="  font-size: medium;color: #18952a; padding: 1px;">(Bate)</span> ?', 'wpcd'),
                    'id'   => 'dvi_high_performance_command_logs',
                    'type' => 'checkbox',
                    'tab'  => 'high-performance',
                    "std" => false,
                ],
                // [
                //     'name' => __('Enable high performance ssh Logs <span style="  font-size: medium;color: #18952a; padding: 1px;">(Bate)</span> ?', 'wpcd'),
                //     'id'   => 'dvi_high_performance_ssh_logs',
                //     'type' => 'checkbox',
                //     'tab'  => 'high-performance',
                //     "std" => false,
                //     'attributes' => array(
                //         'disabled' =>  'disabled',
                //     ),
                // ],
            ],
        ];

        return $meta_boxes;
    }
    public function Taps($tabs)
    {
        $tabs['high-performance']   = __('High Performance', 'wpcd');
        return $tabs;
    }
}
