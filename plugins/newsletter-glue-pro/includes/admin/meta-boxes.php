<?php
/**
 * Metaboxes.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add meta box support.
 */
function newsletterglue_add_meta_box() {

	$unsupported = array();
	$saved_types = get_option( 'newsletterglue_post_types' );

	if ( ! empty( $saved_types ) ) {
		$post_types = explode( ',', $saved_types );
	} else {
		$post_types = apply_filters( 'newsletterglue_supported_core_types', array() );
	}

	$post_types = array_merge( $post_types, array( 'newsletterglue', 'ngl_pattern', 'ngl_template', 'ngl_automation' ) );

	if ( is_array( $post_types ) ) {
		foreach( $post_types as $post_type ) {
			if ( ! in_array( $post_type, apply_filters( 'newsletterglue_unsupported_post_types', $unsupported ) ) ) {
				if ( current_user_can( 'publish_newsletterglue' ) ) {
					add_meta_box( 'newsletter_glue_metabox', __( 'Newsletter Glue: Send as newsletter', 'newsletter-glue' ), 'newsletterglue_meta_box', $post_type, 'normal', 'high' );
				}
			}
		}
	}

}
add_action( 'add_meta_boxes', 'newsletterglue_add_meta_box', 1 );

/**
 * Save meta box.
 */
function newsletterglue_save_meta_box( $post_id, $post ) {

	// $post_id and $post are required
	$saved_meta_boxes = false;

	if ( empty( $post_id ) || empty( $post ) || $saved_meta_boxes ) {
		return;
	}

	// Dont' save meta boxes for revisions or autosaves
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
		return;
	}

	// Check the nonce
	if ( empty( $_POST['newsletterglue_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['newsletterglue_meta_nonce'] ) ), 'newsletterglue_save_data' ) ) {
		return;
	}

	// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
	if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
		return;
	}

	// Avoid draft save.
	if ( ! isset( $post->post_status ) ) {
		return;
	}

	if ( defined( 'NEWSLETTERGLUE_DEMO' ) ) {
		return;
	}

	// Save newsletter data.
	newsletterglue_save_data( $post_id, newsletterglue_sanitize( $_POST ) );

	// Only allow published and scheduled posts.
	if ( ! in_array( $post->post_status, array( 'publish', 'future' ) ) ) {
		return;
	}

	// Check user has permission to edit
	if ( ! current_user_can( 'publish_newsletterglue' ) ) {
		return;
	}

	// We need this save event to run once to avoid potential endless loops. This would have been perfect:
	$saved_meta_boxes = true;

	// Save automation.
	if ( in_array( $post->post_type, array( 'ngl_automation', 'ngl_log' ) ) ) {
		do_action( "{$post->post_type}_save", $post_id, $post );
		return;
	}

	// The "Send" checkbox is not checked.
	if ( ! isset( $_POST[ 'ngl_double_confirm' ] ) || $_POST[ 'ngl_double_confirm' ] !== 'yes' ) {
		return;
	}

	// Send it.
	if ( $post->post_status == 'future' ) {

        if ( ! empty( $_POST['ngl_schedule'] ) && $_POST['ngl_schedule'] === 'schedule_draft' ) {
            newsletterglue_send( $post_id, false, true );
        } else {

            // Schedule newsletter.
            update_post_meta( $post_id, '_ngl_future_send', 'yes' );

            $scheduled = array(
                'status'        => 200,
                'type'          => 'schedule',
                'message'       => __( 'Scheduled', 'newsletter-glue' ),
            );

            $subject = isset( $_POST[ 'ngl_subject' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ngl_subject' ] ) ) : '';

            newsletterglue_add_campaign_data( $post_id, $subject, $scheduled, 0 );

        }

	} else {
		newsletterglue_send( $post_id );
	}

	// We did an action here.
	if ( ! get_option( 'newsletterglue_did_action' ) ) {
		update_option( 'newsletterglue_did_action', 'yes' );
	}

}
add_action( 'save_post', 'newsletterglue_save_meta_box', 1, 2 );

/**
 * Shows the metabox content.
 */
function newsletterglue_meta_box() {
	global $post, $the_lists, $post_type, $automation;

	$app 		= newsletterglue_default_connection();
	$init		= newsletterglue_get_path( $app ) . '/init.php';
	$settings   = newsletterglue_get_data( $post->ID );

	if ( isset( $post_type ) && $post_type === 'ngl_automation' ) {
		$automation = new NGL_Automation( $post->ID );
	}

	do_action( 'newsletterglue_admin_before_metabox_content', $post->ID );

	if ( isset( $automation ) ) {
		if ( $automation->is_enabled() ) {
			$state = 'enabled';
			$automating = 'is-automated';
		} else {
			$state = 'paused';
			$automating = '';
		}
		echo '<div class="ngl-admin-automation-mb ' . esc_attr( $automating ) . ' is-' . esc_attr( $state ) . '">';

		include( 'metabox/views/automation-scheduled.php' );

	}

	if ( ( ! $app || ! file_exists( $init ) ) && ! in_array( $post_type, newsletterglue_get_core_cpts() ) ) {

		$app = 'mailchimp';

		include_once( newsletterglue_get_path( $app ) . '/init.php' );

		$class 		= 'NGL_' . ucfirst( $app );
		$api   		= new $class();
		$defaults 	= newsletterglue_get_form_defaults( $post, $api );

		include( 'metabox/views/connect.php' );

	} else {

		if ( empty( $app ) ) {
			$app = 'core';
		}

		include_once newsletterglue_get_path( $app ) . '/init.php';

		$class 		= 'NGL_' . ucfirst( $app );
		$api   		= new $class();
		$defaults 	= newsletterglue_get_form_defaults( $post, $api );

		$hide = false;

		if ( ! isset( $settings->sent ) ) {
			$hide = true;
		}

		if ( get_post_meta( $post->ID, '_ngl_future_send', true ) ) {
			$hide = false;
		}

		include( 'metabox/views/before-metabox.php' );

		if ( ! in_array( $post_type, newsletterglue_get_core_cpts() ) || defined( 'NEWSLETTERGLUE_DEMO' ) ) {
			include newsletterglue_get_path( $app ) . '/metabox.php';
		}

		include( 'metabox/views/after-metabox.php' );

		wp_nonce_field( 'newsletterglue_save_data', 'newsletterglue_meta_nonce' );

		if ( isset( $automation ) ) {
			echo '</div>';
		}
	}

}