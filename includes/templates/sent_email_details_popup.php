<?php
/**
 * This template file used for load sent emails details data
 *
 * @package wpcd
 */

$email_body = '';

// Use get_post to retrieve the post directly by its ID.
$entry_found = get_post( $post_id );

// Check post_id in wpcd_notify_user.

if ( $entry_found and $entry_found->post_type == 'wpcd_sent_emails' and  $entry_found->post_status == 'private' ) {
    // If the post exists and matches the expected post type and status, retrieve the meta data.
    $email_body = get_post_meta( $post_id, 'wpcd_sent_email_email_body', true );
}

?>

<!-- Model -->
<div id="wpcd_sent_email_details_popup_sec" class="wpcd_sent_email_details_modal">
	<div class="wpcd_popup_header_sec">
		<span class="wpcd_sent_email_details_close wpcd_close_custom_popup" title="close">×</span>
	</div>	
	<div class="wpcd_sent_email_modal_content">        
		<div class="wpcd_sent_email_details_html">			
			<div class="wpcd_sent_email_details_row">
				<div class="col-sm-2">
					<h3><?php echo esc_html( __( 'Email Body', 'wpcd' ) ); ?></h3>
				</div>
				<div class="col-sm-10">
					<?php
						$allowed_html = array(
							'a'          => array(
								'href'  => array(),
								'title' => array(),
							),
							'img'        => array(
								'src'    => array(),
								'width'  => array(),
								'height' => array(),
							),
							'br'         => array(),
							'em'         => array(),
							'strong'     => array(),
							'p'          => array(),
							'b'          => array(),
							'ul'         => array(),
							'li'         => array(),
							'ol'         => array(),
							'i'          => array(),
							'blockquote' => array(),
							'br'         => array(),
							'del'        => array(),
							'ins'        => array(
								'datetime' => array(),
							),
							'del'        => array(
								'datetime' => array(),
							),
						);
						echo wp_kses( $email_body, $allowed_html );
						?>
				</div>
			</div>
		</div>
	</div>
</div>
