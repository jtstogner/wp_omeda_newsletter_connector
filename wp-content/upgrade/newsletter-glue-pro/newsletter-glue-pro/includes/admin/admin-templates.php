<?php
/**
 * Admin templates.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Admin_Templates class.
 */
class NGL_Admin_Templates {

	/**
	 * Constructor.
	 */
	public static function init() {

		add_filter( 'display_post_states', array( __CLASS__, 'display_post_states' ), 50, 2 );

		// Row actions.
		add_action( 'post_row_actions', array( __CLASS__, 'post_row_actions' ), 50, 2 );

		add_filter( 'nglue_backend_args', array( __CLASS__, 'add_arguments' ) );

		add_action( 'admin_head', array( __CLASS__, 'add_templates_section' ) );

		add_filter( 'manage_ngl_template_posts_columns', array( __CLASS__, 'set_custom_columns' ) );
		add_action( 'manage_ngl_template_posts_custom_column' , array( __CLASS__, 'show_custom_columns' ), 10, 2 );

		add_action( 'load-post-new.php', array( __CLASS__, 'load_template_content' ) );

		add_action( 'restrict_manage_posts', array( __CLASS__, 'restrict_manage_posts' ), 100 );

		// Removes date filter.
		add_filter( 'months_dropdown_results', array( __CLASS__, 'months_dropdown_results' ) );

		add_action( 'admin_footer', array( __CLASS__, 'add_reset_content' ) );
	}

	/**
	 * Mark pattern as default.
	 */
	public static function display_post_states( $post_states, $post ) {
		if ( 'ngl_template' === $post->post_type ) {
			if ( get_post_meta( $post->ID, '_ngl_core_template', true ) ) {
				$post_states[ 'ngl_default' ] = '<span class="ngl-pattern-state" style="font-weight:normal;font-size:13px;">' . __( 'Default', 'newsletter-glue' ) . '</span>';
			}
		}
		return $post_states;
	}

	/**
	 * Row actions.
	 */
	public static function post_row_actions( $actions, $post ) {

		if ( $post->post_type == 'ngl_template' ) {
			$actions[ 'view' ] = '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" title="' . __( 'View post', 'newsletter-glue' ) . '" target="_blank">' . __( 'View post', 'newsletter-glue' ) . '</a>';

			$actions[ 'ngl_preview' ] = '<a href="' . add_query_arg( 'preview_email', $post->ID, home_url() ) . '" title="' . __( 'View email preview', 'newsletter-glue' ) . '" target="_blank">' . __( 'View email preview', 'newsletter-glue' ) . '</a>';
	
			$actions[ 'ngl_duplicate' ] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=ngl_duplicate_as_template&post=' . $post->ID ), plugin_basename( NGL_PLUGIN_FILE ), 'ngl_duplicate_nonce' ) . '" title="' . __( 'Duplicate this template', 'newsletter-glue' ) . '" rel="permalink">' . __( 'Duplicate', 'newsletter-glue' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Add arguments.
	 */
	public static function add_arguments( $args ) {
		global $pagenow;

		if ( ! isset( $_GET[ 'post_type' ] ) || $_GET[ 'post_type' ] != 'newsletterglue' ) { // phpcs:ignore
			return $args;
		}

		$counts = wp_count_posts( 'ngl_template' )->publish;

		$template_cats = get_terms( array(
			'taxonomy'		=> 'ngl_template_category',
			'hide_false' 	=> false,
			'orderby'		=> 'name',
			'order'			=> 'asc'
		) );

		$args[ 'template_categories' ][] = array( 'id' => 0, 'name' => __( 'All', 'newsletter-glue' ), 'count' => $counts );

		foreach( $template_cats as $template_cat ) {
			$args[ 'template_categories' ][] = array( 'id' => $template_cat->term_id, 'name' => $template_cat->name, 'count' => $template_cat->count );
		}

		$args[ 'newsletter_url' ] = admin_url( 'post-new.php?post_type=newsletterglue' );

		$args[ 'default_template' ] = get_option( 'newsletterglue_default_template_id' );

		return $args;
	}

	/**
	 * Load proper template content.
	 */
	public static function load_template_content() {
		global $post;

		$thepost = get_default_post_to_edit( 'newsletterglue', true );

		if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'newsletterglue' ) { // phpcs:ignore
			$template_id = isset( $_GET[ 'template_id' ] ) ? absint( $_GET[ 'template_id' ] ) : 0; // phpcs:ignore
			if ( $template_id ) {
				$template = get_post( $template_id );
				if ( ! isset( $template->post_content ) ) {
					return;
				}

				$content = $template->post_content;
				$content = addslashes( $content );

				$args = array(
					'ID'			=> $thepost->ID,
					'post_title'	=> $template->post_title,
					'post_content'	=> $content,
					'post_status'	=> 'draft',
				);

				wp_update_post( $args );

				$styles = get_post_meta( $template->ID, '_newsletterglue_theme', true );
				if ( $styles ) {
					update_post_meta( $thepost->ID, '_newsletterglue_theme', $styles );
				}

				$css = get_post_meta( $template->ID, '_newsletterglue_css', true );
				if ( $css ) {
					update_post_meta( $thepost->ID, '_newsletterglue_css', $css );
				}

				$data = array( 'app' => newsletterglue_default_connection() );
				update_post_meta( $thepost->ID, '_newsletterglue', $data );

				wp_redirect( admin_url( 'post.php?post=' . $thepost->ID . '&action=edit' ) );
				exit;
			}
		}
	}

	/**
	 * Add templates section.
	 */
	public static function add_templates_section() {
		global $current_screen;

		$default_template 	= get_option( 'newsletterglue_default_template_id' );
		$url 				= admin_url( 'post-new.php?post_type=newsletterglue' );

		if ( $default_template ) {
			$url = add_query_arg( 'template_id', $default_template, $url );
		}

		if ( 'newsletterglue' != $current_screen->post_type ) { ?>

			<script type="text/javascript">
			jQuery(document).ready( function($) {
				//jQuery( '.toplevel_page_newsletter-glue .wp-submenu li:nth-child(3) a' ).attr( 'href', '<?php echo esc_attr( $url ); ?>' );
			});
			</script>

		<?php
		} else { ?>

			<div id="ngl_template"></div>

			<style type="text/css">
			#ngl_template {
				display: none;
				position: relative;
				margin: 0 0 0 10px;
				align-items: center;
			}
			</style>

			<script type="text/javascript">
			jQuery(document).ready( function($) {
				jQuery( 'a.page-title-action' ).attr( 'href', '<?php echo esc_url_raw( $url ); ?>' );
				jQuery( '#ngl_template' ).insertAfter( 'a.page-title-action' ).css( { 'display' : 'inline-flex' } );
			});
			</script>

		<?php
		}
	}

	/**
	 * Add custom columns.
	 */
	public static function set_custom_columns( $columns ) {

		$date = $columns[ 'date' ];

		unset( $columns[ 'date' ] );

		$columns[ 'set_defaults' ] = __( 'Set active template', 'newsletter-glue' );
		$columns[ 'ngl_template_category' ] = __( 'Template category', 'newsletter-glue' );
		$columns[ 'date' ] = $date;

		return $columns;
	}

	/**
	 * Show custom columns.
	 */
	public static function show_custom_columns( $column, $post_id ) {

		$default_template = get_option( 'newsletterglue_default_template_id' );
		$shortcuts = get_option( 'newsletterglue_template_shortcuts' );

		switch ( $column ) {

			case 'set_defaults' :

				echo '<div class="ngl-tpl-col" data-post-id="' . absint( $post_id ) . '" data-is-shortcut="' . absint( newsletterglue_is_shortcut( $post_id ) ) . '">';

				if ( $default_template == $post_id ) {
					echo '<a href="#" class="ngl-tpl-default">' . esc_html__( 'Active template', 'newsletter-glue' ) . '</a>';
				} else {
					echo '<a href="#" class="ngl-tpl-make-default">' . esc_html__( 'Set active template', 'newsletter-glue' ) . '</a>';
				}

				echo '</div>';

			break;

			case 'ngl_template_category' :

				$terms = wp_get_post_terms( $post_id, 'ngl_template_category' );
				if ( ! empty( $terms ) ) {
					$output = '';
					foreach( $terms as $term ) {
						$output .= '<a href="' . admin_url( 'edit.php?post_type=ngl_template&ngl_template_category=' . $term->slug ) . '">' . $term->name . '</a> (<a href="' . admin_url( 'term.php?taxonomy=ngl_template_category&tag_ID=' . $term->term_id . '&post_type=newsletterglue' ) . '">' . __( 'Edit', 'newsletter-glue' ) . '</a>)<span style="display:inline-block;width:20px;"></span>';
					}
					echo wp_kses_post( $output );
				} else {
					echo '&mdash;';
				}

			break;

		}
	}

	/**
	 * Add category dropdown filter.
	 */
	public static function restrict_manage_posts() {
		global $typenow, $post, $post_id;

		if ( $typenow == 'ngl_template' ) {

			$post_type 	= get_query_var( 'post_type' ); 
			$taxonomies = get_object_taxonomies( $post_type );

			if ( $taxonomies ) {
				foreach( $taxonomies as $tax_slug ) {
					$tax_obj = get_taxonomy( $tax_slug );
					$tax_name = $tax_obj->labels->name;
					$terms = get_terms( array( 'taxonomy' => $tax_slug, 'hide_empty' => false, 'orderby' => 'term_id', 'order' => 'asc' ) );
					echo "<select name='" . esc_attr( $tax_slug ) . "' id='" . esc_attr( $tax_slug ) . "' class='postform'>";
					echo "<option value=''>" . esc_html__( 'All Categories', 'newsletter-glue' ) . "</option>";
					foreach ( $terms as $term ) { 
						$label = ( isset( $_GET[ $tax_slug ] ) ) ? esc_attr( wp_unslash( $_GET[ $tax_slug ] ) ) : ''; // phpcs:ignore
						echo '<option value=' . esc_attr( $term->slug ), $label == $term->slug ? ' selected="selected"' : '','>' . esc_html( $term->name ) . '</option>';
					}
					echo "</select>";
				}
			}
		}
	}

	/**
	 * Remove date filter.
	 */
	public static function months_dropdown_results( $months ) {
		global $typenow;

		if ( $typenow == 'ngl_template' ) {
			return array();
		}

		return $months;
	}

	/**
	 * Add reset content.
	 */
	public static function add_reset_content( $args ) {
		global $post_type, $pagenow;

		if ( $pagenow === 'post-new.php' || $pagenow === 'post.php' ) {
			return;
		}

		if ( ! isset( $_GET[ 'post_type' ] ) || $_GET[ 'post_type' ] !== 'ngl_template' ) { // phpcs:ignore
			return;
		}

		require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-templates.php' );

		$class		= new NGL_Default_Templates();
		$templates 	= $class->get_templates();
		?>
		<div class="ngl-pattern-extra-ui">
			<div class="ngl-pattern-text">
				<?php _e( 'Create, edit and manage newsletter templates.', 'newsletter-glue' ); ?>
				<a href="#" class="ngd-launch-video" data-src="https://www.youtube.com/embed/fwTjrRjgmvM?autoplay=1"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M10 16.5l6-4.5-6-4.5v9zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path></svg><?php _e( 'Watch quick tutorial.', 'newsletter-glue' ); ?></a>
			</div>
			<div class="ngl-pattern-reset">
				<a href="#" class="ngl-pattern-reset-toggle"><?php _e( 'Reset default templates', 'newsletter-glue' ); ?></a>
				<div class="ngl-pattern-reset-ui">
					<div>
						<select>
							<option value="all" data-url="<?php echo esc_url( add_query_arg( 'recreate-templates', 'all', admin_url( 'edit.php?post_type=ngl_template' ) ) ); ?>" selected><?php _e( 'All default templates', 'newsletter-glue' ); ?></option>
							<?php foreach( $templates as $key => $data ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" data-url="<?php echo esc_url( add_query_arg( 'recreate-templates', $key, admin_url( 'edit.php?post_type=ngl_template' ) ) ); ?>"><?php echo esc_html( $data[ 'title' ] ); ?></option>
							<?php endforeach; ?>
						</select>
						<a href="<?php echo esc_url( add_query_arg( 'recreate-templates', 'all', admin_url( 'edit.php?post_type=ngl_template' ) ) ); ?>" class="button button-primary action ngl-pattern-reset-start"><?php _e( 'Restore', 'newsletter-glue' ); ?></a>
					</div>
					<div>
						<?php _e( 'This only affects default templates. This won&rsquo;t affect templates you&rsquo;ve created yourself.', 'newsletter-glue' ); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="ngd-tut-overlay">
			<div class="ngd-tut-container"></div>
		</div>
		<?php
	}

}

NGL_Admin_Templates::init();
