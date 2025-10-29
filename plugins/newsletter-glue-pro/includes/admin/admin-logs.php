<?php
/**
 * Admin: Logs.
 * 
 * @package Newsletter Glue
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Admin_Logs class.
 */
class NGL_Admin_Logs {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$post_type = 'ngl_log';

		add_filter( 'views_edit-ngl_log', array( $this, 'views' ), 10 );

		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'admin_init' ), 99 );

		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 99 );

		add_filter( "manage_{$post_type}_posts_columns", array( $this, 'columns' ), 99 );
		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'column_data' ), 99, 2 );

	}

	/**
	 * Views.
	 * 
	 * @param array $views The views array.
	 */
	public function views( $views ) {
		return array();
	}

	/**
	 * Row actions.
	 * 
	 * @param array  $actions The actions array.
	 * @param object $post The post object.
	 */
	public function row_actions( $actions, $post ) {
		if ( 'ngl_log' == $post->post_type ) {
			$actions = array();

			return $actions;
		}
		return $actions;
	}

	/**
	 * Admin init.
	 */
	public function admin_init() {
		global $pagenow;
		if ( 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'ngl_log' === $_GET['post_type'] ) { // phpcs:ignore
			if ( empty( $_GET['automation_id'] ) ) { // phpcs:ignore
				wp_redirect( admin_url( 'edit.php?post_type=ngl_automation' ) );
				exit;
			}
		}
	}

	/**
	 * Pre-query. Show only logs for specific automation.
	 * 
	 * @param object $query This is an object containing the query.
	 */
	public function pre_get_posts( $query ) {
		global $pagenow;
		if ( is_admin() && 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && 'ngl_log' === $_GET['post_type'] && isset( $_GET['automation_id'] ) ) { // phpcs:ignore
			$query->set( 'meta_key', '_automation_id' ); // phpcs:ignore
			$query->set( 'meta_value', absint( $_GET['automation_id'] ) ); // phpcs:ignore
		}
	}

	/**
	 * Manage columns.
	 * 
	 * @param array $columns The columns array.
	 */
	public function columns( $columns ) {

		foreach ( $columns as $key => $value ) {
			if ( ! in_array( $key, array( 'cb' ) ) ) {
				unset( $columns[ $key ] );
			}
		}

		$columns['ngl_log_title']   = __( 'Subject line', 'newsletter-glue' );
		$columns['ngl_log_status']  = __( 'Status', 'newsletter-glue' );
		$columns['ngl_log_details'] = __( 'Details', 'newsletter-glue' );

		return $columns;

	}

	/**
	 * Display custom post columns.
	 * 
	 * @param string  $column The single column.
	 * @param integer $post_id The post ID.
	 */
	public function column_data( $column, $post_id ) {

		$log = new NGL_Log( $post_id );

		$status = $log->get_status();

		$message = '&mdash;';

		if ( is_object( $status ) ) {
			$message = '<span class="ngl-success">' . __( 'Sent', 'newsletter-glue' ) . '</span>';
		} else if ( isset( $status['status'] ) ) {
			if ( 'draft' === $status['status'] ) {
				$message = '<span class="ngl-neutral">' . __( 'Saved as draft', 'newsletter-glue' ) . '</span>';
			}
			if ( 'sent' === $status['status'] ) {
				$message = '<span class="ngl-success">' . __( 'Sent', 'newsletter-glue' ) . '</span>';
			}
		} else if ( 1 == $status ) {
			$message = '<span class="ngl-success">' . __( 'Sent', 'newsletter-glue' ) . '</span>';
		}

		switch ( $column ) {

			case 'ngl_log_title':
				echo '<a href="' . esc_url( $log->get_preview_link() ) . '" target="_blank" class="row-title">' . esc_html( $log->get_post_title() ) . '</a>';
				echo '<div class="row-actions">
					<span class="edit"><a href="' . esc_url( $log->get_preview_link() ) . '" target="_blank">' . esc_html__( 'Preview', 'newsletter-glue' ) . '</a></span> | 
					<span class="trash"><a href="' . esc_url( $log->get_trash_link() ) . '" class="submitdelete">' . esc_html__( 'Trash', 'newsletter-glue' ) . '</span>
				</div>';
				break;

			case 'ngl_log_status':
				echo wp_kses_post( $message );
				break;

			case 'ngl_log_details':
				echo '<span class="ngl-regular">' . wp_kses_post( $log->get_date() ) . '</span>';
				break;
		}

	}

}

return new NGL_Admin_Logs();
