<?php
/**
 * REST API.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_REST_API_Get_Block_Settings class.
 */
class NGL_REST_API_Get_Block_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/get_block_settings', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = array();

		$blocks = newsletterglue_get_blocks();

		foreach( $blocks as $block_id => $params ) {
			$classname = ucfirst( str_replace( 'newsletterglue_block_', 'NGL_Block_', $block_id ) );
			if ( ! class_exists( $classname ) ) {
				continue;
			}
			$block = new $classname;
			$data[ 'blocks' ][ $block_id ] = array(
				'id'			=> esc_attr( $block_id ),
				'label'			=> esc_html( $block->get_label() ),
				'description'	=> esc_html( $block->get_description() ),
				'icon'			=> $block->get_icon_svg(),
				'url'			=> $block->get_demo_url(),
				'in_use'		=> $block->use_block() === 'yes' ? 1 : 0,
			);
		}

		return rest_ensure_response( $data );
	}

}

return new NGL_REST_API_Get_Block_Settings();