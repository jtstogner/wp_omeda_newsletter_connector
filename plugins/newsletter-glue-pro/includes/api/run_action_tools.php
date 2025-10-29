<?php
/**
 * WP-admin Tools.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_RUN_ACTION_TOOLS class.
 */
class NGL_RUN_ACTION_TOOLS {

	/**
	 * Constructor.
	 */
	public function __construct() {

		register_rest_route( 'newsletterglue/' . newsletterglue()->api_version(), '/run_action_tools', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'response' ),
			'permission_callback' => array( 'NGL_REST_API', 'authenticate' ),
		) );

	}

	/**
	 * Response.
	 */
	public function response( $request ) {

		$data = json_decode( $request->get_body(), true );

		$action = ! empty( $data['action'] ) ? esc_attr( $data['action'] ) : null;

		if ( ! current_user_can( 'manage_options' ) ) {
			$action = null;
			$message = null;
		}

		if ( $action ) {
			$message = call_user_func( array( $this, $action ) );
		}

		$response = array(
			'action'  => $action,
			'message' => ! empty( $message ) ? esc_html( $message ) : null,
		);

		return rest_ensure_response( $response );
	}

	/**
	 * Function.
	 */
	private function reset_templates() {

		require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-templates.php' );
		$param = false;
		$templates = new NGL_Default_Templates();
		$templates->create( $param );
		update_option( 'newsletterglue_did_default_templates_v2', 'yes' );

		$message = __( 'Your newsletter templates have been reset.', 'newsletter-glue' );

		return $message;
	}

	/**
	 * Function.
	 */
	private function reset_patterns() {

		require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-patterns.php' );
		$param = false;
		$patterns = new NGL_Default_Patterns();
		$patterns->create( $param );
		update_option( 'newsletterglue_did_default_patterns', 'yes' );

		$message = __( 'Your newsletter patterns have been reset.', 'newsletter-glue' );

		return $message;
	}

	/**
	 * Function.
	 */
	private function reset_theme() {

		delete_option( 'newsletterglue_theme' );

		$message = __( 'Your newsletter theme was reset.', 'newsletter-glue' );

		return $message;
	}

	/**
	 * Function.
	 */
	private function reset_css() {

		delete_option( 'newsletterglue_css' );

		$message = __( 'Your newsletter custom CSS was reset.', 'newsletter-glue' );

		return $message;
	}

	/**
	 * Function.
	 */
	private function reinstall_roles() {

		newsletterglue_assign_caps();

		$message = __( 'Your newsletter user roles and permissions have been reset.', 'newsletter-glue' );

		return $message;
	}
}

return new NGL_RUN_ACTION_TOOLS();
