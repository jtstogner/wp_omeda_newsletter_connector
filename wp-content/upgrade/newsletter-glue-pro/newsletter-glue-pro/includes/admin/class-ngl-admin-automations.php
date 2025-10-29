<?php
/**
 * Admin: Automations.
 * 
 * @package Newsletter Glue.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Admin_Automations class.
 */
class NGL_Admin_Automations {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$post_type = 'ngl_automation';

		add_action( 'post_row_actions', array( __CLASS__, 'post_row_actions' ), 50, 2 );

		add_filter( "manage_{$post_type}_posts_columns", array( $this, 'columns' ), 99 );
		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'column_data' ), 99, 2 );

		add_action( 'ngl_automation_save', array( $this, 'save_automation_metabox' ), 10, 2 );

		add_action( 'admin_head', array( __CLASS__, 'add_template_selector' ) );

		add_filter( 'nglue_backend_args', array( __CLASS__, 'add_arguments' ) );

		add_action( 'load-post-new.php', array( __CLASS__, 'load_template_content' ) );

	}

	/**
	 * Pauses an automation.
	 */
	public static function admin_init() {
		global $post_type;

		if ( isset( $_GET['post'] ) && ! empty( $_GET['ngl_pause'] ) && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) {

			$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';
			if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'ngl_nonce_action' ) ) {
				return;
			}

			$post_id = absint( $_GET['post'] );
			$automation = new NGL_Automation( $post_id );
			if ( $automation->is_valid() ) {
				$automation->disable();
			}
		}
	}

	/**
	 * Row actions.
	 * 
	 * @param array  $actions The actions array.
	 * @param object $post The post object.
	 */
	public static function post_row_actions( $actions, $post ) {

		if ( 'ngl_automation' == $post->post_type ) {
			$automation = new NGL_Automation( $post->ID );
			$url_to_edit = add_query_arg( 'ngl_pause', 'yes', get_edit_post_link( $post->ID ) );
			$actions['edit'] = '<a href="' . esc_url( wp_nonce_url( $url_to_edit, 'ngl_nonce_action' ) ) . '" class="automation-edit-link">' . __( 'Edit', 'newsletter-glue' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Manage columns.
	 * 
	 * @param array $columns An array of columns to add.
	 */
	public function columns( $columns ) {

		foreach ( $columns as $key => $value ) {
			$ngl_columns[ $key ] = $value;
			if ( 'title' == $key ) {
				$ngl_columns['ngl_aschedule'] = __( 'Schedule', 'newsletter-glue' );
				$ngl_columns['ngl_astatus'] = __( 'Status', 'newsletter-glue' );
				$ngl_columns['ngl_alog'] = __( 'Email log', 'newsletter-glue' );
			}
		}

		return $ngl_columns;

	}

	/**
	 * Display custom post columns.
	 * 
	 * @param string  $column The column name.
	 * @param integer $post_id The post ID.
	 */
	public function column_data( $column, $post_id ) {

		$automation  = new NGL_Automation( $post_id );
		$status      = $automation->get_status();

		switch ( $column ) {
			case 'ngl_aschedule':
				if ( $automation->is_scheduled() ) {
					echo '<span class="ngl-regular">' . wp_kses_post( $automation->get_schedule_text() ) . '</span>';
				} else {
					echo '<span class="ngl-muted">' . esc_html__( 'Unscheduled', 'newsletter-glue' ) . '</span>';
				}
				break;

			case 'ngl_astatus':
				echo '<div class="ngl-automation-status" data-status="' . esc_attr( $status ) . '" data-id="' . absint( $post_id ) . '"></div>';
				break;

			case 'ngl_alog':
				$logs = $automation->get_logs();
				if ( ! empty( $logs ) ) {
					echo '<span class="ngl-regular"><a href="' . esc_url( admin_url( 'edit.php?post_type=ngl_log&automation_id=' . $post_id ) ) . '">' . esc_html__( 'View log', 'newsletter-glue' ) . '</a> (' . count( $logs ) . ')</span>';
				} else {
					echo '<span class="ngl-muted">' . esc_html__( 'No emails sent yet', 'newsletter-glue' ) . '</span>';
				}
				break;
		}
	}

	/**
	 * Save automation metabox.
	 * 
	 * @param integer $post_id This is the post ID.
	 * @param object  $post This is the post object.
	 */
	public function save_automation_metabox( $post_id, $post ) {

		// Check the nonce.
		if ( empty( $_POST['newsletterglue_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['newsletterglue_meta_nonce'] ) ), 'newsletterglue_save_data' ) ) {
			return;
		}

		$automation = new NGL_Automation( $post_id );

		$send_as_newsletter = isset( $_POST['ngl_send_newsletter'] ) ? true : false;
		$frequency          = isset( $_POST['ngl_frequency'] ) ? sanitize_text_field( $_POST['ngl_frequency'] ) : '';
		$day                = isset( $_POST['ngl_frequency_day'] ) ? sanitize_text_field( $_POST['ngl_frequency_day'] ) : '';
		$time               = isset( $_POST['ngl_frequency_time'] ) ? sanitize_text_field( $_POST['ngl_frequency_time'] ) : '';
		$day_exception = array();
		$day_exception = newsletterglue_parse_frequency_day_exceptions( $_POST );
		$send_now = isset( $_POST['ngl_send_now'] ) ? true : false;

		if ( 'monthly' == $frequency ) {
			$day = isset( $_POST['ngl_frequency_day2'] ) ? sanitize_text_field( $_POST['ngl_frequency_day2'] ) : 7;
		}

		$schedule = array(
			'frequency' => $frequency,
			'day' => $day,
			'time' => $time,
			'day_exception' => $day_exception,
		);

		$automation->set_schedule( $schedule );

		$automation->set_send_now( $send_now );

		if ( $send_as_newsletter ) {
			$automation->enable();
		} else {
			$automation->disable();
		}

	}

	/**
	 * Add templates section.
	 */
	public static function add_template_selector() {
		global $current_screen;

		if ( 'ngl_automation' == $current_screen->post_type ) { ?>

			<div id="ngl_automation_select"></div>

			<style type="text/css">
			#ngl_automation_select {
				display: none;
				position: relative;
				margin: 0 0 0 10px;
				align-items: center;
			}
			.wrap a.page-title-action:not(.ngl-page-title-action) {
				display: none !important;
			}
			</style>

			<script type="text/javascript">
			jQuery(document).ready( function($) {
				jQuery( '#ngl_automation_select' ).insertAfter( 'h1.wp-heading-inline' ).css( { 'display' : 'inline-flex' } );
			});
			</script>

			<?php
		}

	}

	/**
	 * Add arguments.
	 * 
	 * @param array $args Arguments array.
	 */
	public static function add_arguments( $args ) {
		global $pagenow;

		if ( ! isset( $_GET['post_type'] ) || 'ngl_automation' != $_GET['post_type'] ) { // phpcs:ignore
			return $args;
		}

		$args['newsletter_url'] = admin_url( 'post-new.php?post_type=ngl_automation' );

		return $args;
	}

	/**
	 * Load proper template content.
	 */
	public static function load_template_content() {
		global $post;

		$thepost = get_default_post_to_edit( 'ngl_automation', true );

		if ( isset( $_GET['post_type'] ) && 'ngl_automation' === $_GET['post_type'] ) { // phpcs:ignore

			$template_id = isset( $_GET['template_id'] ) ? absint( $_GET['template_id'] ) : 0; // phpcs:ignore

			if ( $template_id ) {

				$template = get_post( $template_id );

				if ( ! isset( $template->post_content ) ) {
					return;
				}

				$content = $template->post_content;
				$content = addslashes( $content );

				$args = array(
					'ID'            => $thepost->ID,
					'post_title'    => $template->post_title,
					'post_content'  => $content,
					'post_status'   => 'draft',
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

}

return new NGL_Admin_Automations();
