<?php
/**
 * This file contains general or helper functions for WPCD
 * when handling metabox related things.
 *
 * It primarily helps with creating CARD wrappers in tabs.
 *
 * Looking for other metabox related functions?
 * check out core/apps/wordpres-app/traits/traits-for-class-wordpress-app/metaboxes-app.php
 * and core/apps/wordpres-app/traits/traits-for-class-wordpress-app/metaboxes-server.php
 *
 * @package wpcd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return an ARRAY suitable for a metabox field
 * that has a div wrapper with certain classes.
 *
 * @since 5.7.
 *
 * @param string $tab The tab on which the field will go.
 */
function wpcd_start_full_card( $tab ) {

	$field = array(
		'tab'               => $tab,
		'type'              => 'wpcd_card_container',
		'column_row_before' => '<div class="wpcd-card-group wpcd-card-full">',
	);

	return $field;

}

/**
 * Return an ARRAY suitable for a metabox field
 * that has a div wrapper with certain classes.
 *
 * @since 5.7.
 *
 * @param string $tab The tab on which the field will go.
 */
function wpcd_start_full_card_no_border( $tab ) {

	$field = array(
		'tab'               => $tab,
		'type'              => 'wpcd_card_container',
		'column_row_before' => '<div class="wpcd-card-group wpcd-card-full-no-border">',
	);

	return $field;

}

/**
 * Return an ARRAY suitable for a metabox field
 * that has a div wrapper with certain classes.
 *
 * @since 5.7.
 *
 * @param string $tab The tab on which the field will go.
 */
function wpcd_start_full_card_page_heading( $tab ) {

	$field = array(
		'tab'               => $tab,
		'type'              => 'wpcd_card_container',
		'column_row_before' => '<div class="wpcd-card-group wpcd-card-full-no-border wpcd-card-full-page-heading">',
	);

	return $field;

}


/**
 * Return an ARRAY suitable for a metabox field
 * that has a div wrapper with certain classes.
 *
 * @since 5.7.
 *
 * @param string $tab The tab on which the field will go.
 */
function wpcd_start_half_card( $tab ) {

	$field = array(
		'tab'               => $tab,
		'type'              => 'wpcd_card_container',
		'column_row_before' => '<div class="wpcd-card-group wpcd-card-half">',
	);

	return $field;

}

/**
 * Return an ARRAY suitable for a metabox field
 * that has a div wrapper with certain classes.
 *
 * @since 5.7.
 *
 * @param string $tab The tab on which the field will go.
 */
function wpcd_start_one_third_card( $tab ) {

	$field = array(
		'tab'               => $tab,
		'type'              => 'wpcd_card_container',
		'column_row_before' => '<div class="wpcd-card-group wpcd-card-third">',
	);

	return $field;

}

/**
 * Return an ARRAY suitable for a metabox field
 * that has a div wrapper with certain classes.
 *
 * @since 5.7.
 *
 * @param string $tab The tab on which the field will go.
 */
function wpcd_start_two_thirds_card( $tab ) {

	$field = array(
		'tab'               => $tab,
		'type'              => 'wpcd_card_container',
		'column_row_before' => '<div class="wpcd-card-group wpcd-card-two-thirds">',
	);

	return $field;

}

/**
 * Return the closing div for a card.
 *
 * @since 5.7.
 *
 * @param string $tab The tab on which the field will go.
 */
function wpcd_end_card( $tab ) {

	$field = array(
		'tab'               => $tab,
		'type'              => 'wpcd_card_container',
		'column_row_before' => '</div><!-- .wpcd-card-group -->',
	);

	return $field;

}

 // This code addresses an issue where servers are inaccessible due to changes in metabox behavior starting from version 5.9.11
add_filter( 'map_meta_cap', 'custom_map_meta_cap_for_server_access', 10, 4 );

function custom_map_meta_cap_for_server_access( $caps, $cap, $user_id, $args ) {
    // Check if the required capability is `read_post` and the user is an admin using `wpcd_is_admin()`
    if ( 'read_post' === $cap and wpcd_is_admin() ) {
        // Get the post ID from the arguments to check its post type
        $post_id = isset( $args[0] ) ? $args[0] : 0;
        $post_type = get_post_type( $post_id );

        // Check if the post type is specifically `wpcd_app_server`
        if ( 'wpcd_app_server' === $post_type ) {
            // Override capability check and allow reading the post
            return array( 'exist' ); // Grant access without additional permissions
        }
    }

    // Return the original capabilities if the conditions above are not met
    return $caps;
}
