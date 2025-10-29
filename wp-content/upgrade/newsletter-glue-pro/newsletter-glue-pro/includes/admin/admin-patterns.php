<?php
/**
 * Admin patterns.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Admin_Patterns class.
 */
class NGL_Admin_Patterns {

	/**
	 * Constructor.
	 */
	public static function init() {

		add_filter( 'display_post_states', array( __CLASS__, 'display_post_states' ), 50, 2 );

		// Row actions.
		add_action( 'post_row_actions', array( __CLASS__, 'post_row_actions' ), 50, 2 );

		add_action( 'admin_footer', array( __CLASS__, 'add_reset_content' ) );
	}

	/**
	 * Mark pattern as default.
	 */
	public static function display_post_states( $post_states, $post ) {
		if ( 'ngl_pattern' === $post->post_type ) {
			if ( get_post_meta( $post->ID, '_ngl_core_pattern', true ) ) {
				$post_states[ 'ngl_default' ] = '<span class="ngl-pattern-state" style="font-weight:normal;font-size:13px;">' . __( 'Default', 'newsletter-glue' ) . '</span>';
			}
		}
		return $post_states;
	}

	/**
	 * Row actions.
	 */
	public static function post_row_actions( $actions, $post ) {

		if ( $post->post_type == 'ngl_pattern' ) {
			$actions[ 'view' ] = '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" title="' . __( 'View post', 'newsletter-glue' ) . '" target="_blank">' . __( 'View post', 'newsletter-glue' ) . '</a>';

			$actions[ 'ngl_preview' ] = '<a href="' . add_query_arg( 'preview_email', $post->ID, home_url() ) . '" title="' . __( 'View email preview', 'newsletter-glue' ) . '" target="_blank">' . __( 'View email preview', 'newsletter-glue' ) . '</a>';
	
			$actions[ 'ngl_duplicate' ] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=ngl_duplicate_as_pattern&post=' . $post->ID ), plugin_basename( NGL_PLUGIN_FILE ), 'ngl_duplicate_nonce' ) . '" title="' . __( 'Duplicate this pattern', 'newsletter-glue' ) . '" rel="permalink">' . __( 'Duplicate', 'newsletter-glue' ) . '</a>';
		}

		if ( $post->post_type == 'newsletterglue' ) {
			$actions[ 'ngl_duplicate' ] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=ngl_duplicate_as_newsletter&post=' . $post->ID ), plugin_basename( NGL_PLUGIN_FILE ), 'ngl_duplicate_nonce' ) . '" title="' . __( 'Duplicate this newsletter', 'newsletter-glue' ) . '" rel="permalink">' . __( 'Duplicate', 'newsletter-glue' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Add reset content.
	 */
	public static function add_reset_content( $args ) {
		global $post_type, $pagenow;

		if ( $pagenow === 'post-new.php' || $pagenow === 'post.php' ) {
			return;
		}

		if ( ! isset( $_GET[ 'post_type' ] ) || $_GET[ 'post_type' ] !== 'ngl_pattern' ) { // phpcs:ignore
			return;
		}

		require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-patterns.php' );

		$class		= new NGL_Default_Patterns();
		$patterns 	= $class->get_patterns();
		?>
		<div class="ngl-pattern-extra-ui">
			<div class="ngl-pattern-reset">
				<a href="#" class="ngl-pattern-reset-toggle"><?php _e( 'Reset default patterns', 'newsletter-glue' ); ?></a>
				&nbsp;|&nbsp;
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-template-wizard' ) ); ?>"><?php _e( 'Run patterns wizard again', 'newsletter-glue' ); ?></a>
				<div class="ngl-pattern-reset-ui">
					<div>
						<select>
							<option value="all" data-url="<?php echo esc_url( add_query_arg( 'recreate-patterns', 'all', admin_url( 'edit.php?post_type=ngl_pattern' ) ) ); ?>" selected><?php _e( 'All default patterns', 'newsletter-glue' ); ?></option>
							<?php foreach( $patterns as $key => $data ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" data-url="<?php echo esc_url( add_query_arg( 'recreate-patterns', $key, admin_url( 'edit.php?post_type=ngl_pattern' ) ) ); ?>"><?php echo esc_html( $data[ 'title' ] ); ?></option>
							<?php endforeach; ?>
						</select>
						<a href="<?php echo esc_url( add_query_arg( 'recreate-patterns', 'all', admin_url( 'edit.php?post_type=ngl_pattern' ) ) ); ?>" class="button button-primary action ngl-pattern-reset-start"><?php _e( 'Restore', 'newsletter-glue' ); ?></a>
					</div>
					<div>
						<?php _e( 'This only affects default patterns. This won&rsquo;t affect patterns you&rsquo;ve created yourself.', 'newsletter-glue' ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

}

NGL_Admin_Patterns::init();
