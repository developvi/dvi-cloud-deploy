<?php

namespace DVICloudDeploy\Core\DviSetting;


class InitDviSetting
{
    // public function __construct() {
    //     add_action( 'init', [ self::class, 'init' ] );
    // }

    public function init()
    {				
        add_filter( 'wpcd_settings_metaboxes', [ $this, 'MetaBoxes' ] );
		add_filter( 'wpcd_settings_tabs', [  $this, 'Taps' ] );
       
    }

    public function MetaBoxes($meta_boxes) {
        $meta_boxes[] = array(
            'id'             => 'wordpress-dns-settings',
            'title'          => __( 'high performance', 'wpcd' ),
            'settings_pages' => 'wpcd_settings',
            'tab'            => 'high-performance',
            'fields'         => array(

                array(
                    'name' => __( 'Do you want to enable high performance Logs?', 'wpcd' ),
                    'id'   => 'dvi_high_performance',
                    'type' => 'checkbox',
                    'desc' => __( 'Turn this on to mack your logs high performance (recommended).', 'wpcd' ),
                    'tab'  => 'high-performance',
                ),
            ),
        );
        return $meta_boxes;
    }
    public function Taps($tabs) {
        $tabs['high-performance']   = __( 'High Performance', 'wpcd' );
        return $tabs;

    }

}
// add_action( 'init', [ 'InitDviSetting', 'init' ] );
// new InitDviSetting();
