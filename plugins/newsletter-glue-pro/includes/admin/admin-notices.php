<?php
/**
 * Admin Notices.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add a custom notice.
 */
function newsletterglue_add_notice( $notice = array() ) {

	$notices = get_option( 'newsletterglue_notices' );

	if ( ! $notices ) {
		$notices = array();
	}

	$notices[] = $notice;

	update_option( 'newsletterglue_notices', $notices );

}

/**
 * Add a custom notice.
 */
function newsletterglue_admin_notices() {

	$notices = get_option( 'newsletterglue_notices' );

	if ( empty( $notices ) ) {
		return;
	}

	$notices = array_reverse( $notices );

	foreach( $notices as $key => $notice ) {

		if ( ! isset( $notice['notice'] ) ) {
			continue;
		}

	?>

		<div class="ngl-notice error notice is-dismissible" data-key="<?php echo esc_attr( $key ); ?>">
			<p><?php echo wp_kses_post( $notice['notice'] ); ?></p>
		</div>

	<?php
	}

	update_option( 'newsletterglue_notices', $notices );

}
add_action( 'admin_notices', 'newsletterglue_admin_notices', 99 );

/**
 * Process any pending notices that were created during cron jobs.
 */
function newsletterglue_process_pending_notices() {
	$pending_notices = get_option( 'newsletterglue_pending_notices', array() );
	
	if ( empty( $pending_notices ) ) {
		return;
	}
	
	// Add each pending notice to the regular notices system
	foreach ( $pending_notices as $notice ) {
		newsletterglue_add_notice( $notice );
	}
	
	// Clear the pending notices
	delete_option( 'newsletterglue_pending_notices' );
}
add_action( 'admin_init', 'newsletterglue_process_pending_notices', 5 );

/**
 * Remove a custom notice.
 */
function newsletterglue_remove_notice( $key = 0, $sanitized = '' ) {

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		return;
	}

	$notices = get_option( 'newsletterglue_notices' );

	if ( isset( $notices[ $key ] ) ) {
		unset( $notices[ $key ] );
	}

	update_option( 'newsletterglue_notices', $notices );

	update_user_meta( get_current_user_id(), '_ngl_remove_' . $sanitized, 'yes' );

}