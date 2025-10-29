<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Save_Block_Settings class.
 */
class NGL_REST_API_Save_Block_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/save_block_settings', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' )
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		if ( ! newsletterglue_is_activated() ) {
			return rest_ensure_response( array( 'unverified' => true ) );
		}

		$response = array();

		$data = json_decode( $request->get_body(), true );

		$use_blocks = get_option( 'newsletterglue_use_blocks' );
		
		if ( ! $use_blocks ) {
			$use_blocks = array();
		}

		$block_id 	= sanitize_text_field( wp_unslash( $data[ 'block_id' ] ) );
		$is_active  = absint( $data[ 'is_active' ] );

		$use_blocks[ $block_id ] = $is_active ? 'yes' : 'no';

		update_option( 'newsletterglue_use_blocks', $use_blocks );

		do_action( 'newsletterglue_update_block_options', $data );

		return rest_ensure_response( $use_blocks );

	}

}

return new NGL_REST_API_Save_Block_Settings();