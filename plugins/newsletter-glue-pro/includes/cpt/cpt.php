<?php
/**
 * CPT.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_CPT class.
 */
class NGL_CPT {

	/**
	 * Constructor.
	 */
	public static function init() {

		// Register post types.
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );

		// Load block patterns.
		add_action( 'init', array( __CLASS__, 'load_block_patterns' ), 7 );

		// Default patterns.
		add_action( 'init', array( __CLASS__, 'create_default_patterns' ), 50 );
		add_action( 'init', array( __CLASS__, 'create_default_templates' ), 50 );
		add_action( 'admin_init', array( __CLASS__, 'recreate_v3_templates' ), 50 );

		// Register block category.
		add_action( 'init', array( __CLASS__, 'register_block_category' ), 999999999 );

		// Enqueue scripts in admin.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

		// Allowed block types in CPT.
		add_filter( 'allowed_block_types_all', array( __CLASS__, 'allowed_block_types' ), 999, 2 );

		// CSS for Gutenberg.
		add_action( 'admin_head', array( __CLASS__, 'admin_head' ), 999 );

		// Removes date filter.
		add_filter( 'months_dropdown_results', array( __CLASS__, 'months_dropdown_results' ) );

		// Add category dropdown.
		add_action( 'restrict_manage_posts', array( __CLASS__, 'restrict_manage_posts' ), 100 );

		// Filter post views.
		add_filter( 'views_edit-ngl_pattern', array( __CLASS__, 'views_edit' ) );

		// When a newsletter is saved.
		add_action( 'save_post', array( __CLASS__, 'save_newsletter' ), 10, 2 );

		// When a pattern is saved.
		add_action( 'save_post', array( __CLASS__, 'save_pattern' ), 10, 2 );

		// Duplicate.
		add_action( 'admin_action_ngl_duplicate_as_template', array( __CLASS__, 'duplicate_template' ) );
		add_action( 'admin_action_ngl_duplicate_as_pattern', array( __CLASS__, 'duplicate_pattern' ) );
		add_action( 'admin_action_ngl_duplicate_as_newsletter', array( __CLASS__, 'duplicate_newsletter' ) );

		// Add Gutenberg JS.
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_assets' ) );

		// Add top bar.
		add_action( 'all_admin_notices', array( __CLASS__, 'add_topbar' ), 999 );

		// Filter for Gutenberg use.
		add_filter( 'use_block_editor_for_post_type', array( __CLASS__, 'use_block_editor_for_post_type' ), 99999, 2 );

		// Hook for web view.
		add_action( 'wp', array( __CLASS__, 'show_webview' ), 99 );
	}

	/**
	 * Create default patterns.
	 */
	public static function create_default_patterns() {

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		if ( get_option( 'newsletterglue_did_default_patterns' ) && ! isset( $_GET[ 'recreate-patterns' ] ) ) { // phpcs:ignore
			return;
		}

		require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-patterns.php' );

		$param = false;
		if ( isset( $_GET[ 'recreate-patterns' ] ) ) { // phpcs:ignore
			if ( $_GET[ 'recreate-patterns' ] != 'all' && $_GET[ 'recreate-patterns' ] != 'true' ) { // phpcs:ignore
				$param = sanitize_text_field( wp_unslash( $_GET[ 'recreate-patterns' ] ) ); // phpcs:ignore
			}				
		}

		$patterns = new NGL_Default_Patterns();
		$patterns->create( $param );

		update_option( 'newsletterglue_did_default_patterns', 'yes' );

		if ( isset( $_GET[ 'recreate-patterns' ] ) ) { // phpcs:ignore
			wp_redirect( esc_url( remove_query_arg( 'recreate-patterns' ) ) );
			exit;
		}
	}

	/**
	 * Create default templates.
	 */
	public static function create_default_templates() {

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		if ( get_option( 'newsletterglue_did_default_templates_v2' ) && ! isset( $_GET[ 'recreate-templates' ] ) ) { // phpcs:ignore
			return;
		}

		require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-templates.php' );

		$param = false;
		if ( isset( $_GET[ 'recreate-templates' ] ) ) { // phpcs:ignore
			if ( $_GET[ 'recreate-templates' ] != 'all' && $_GET[ 'recreate-templates' ] != 'true' ) { // phpcs:ignore
				$param = sanitize_text_field( wp_unslash( $_GET[ 'recreate-templates' ] ) ); // phpcs:ignore
			}				
		}

		$templates = new NGL_Default_Templates();
		$templates->create( $param );

		update_option( 'newsletterglue_did_default_templates_v2', 'yes' );

		// Add automations category.
		wp_insert_term( __( 'Automations', 'newsletter-glue' ), 'ngl_template_category', array( 'slug' => 'automations' ) );

		if ( isset( $_GET[ 'recreate-templates' ] ) ) { // phpcs:ignore
			wp_redirect( esc_url( remove_query_arg( 'recreate-templates' ) ) );
			exit;
		}
	}

	/**
	 * Create default templates + patterns.
	 */
	public static function recreate_v3_templates() {

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		if ( ! isset( $_GET[ 'recreate-templates-patterns' ] ) ) { // phpcs:ignore
			return;
		}

		require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-templates.php' );
		require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-patterns.php' );

		$templates = new NGL_Default_Templates();
		$templates->create( false );

		$patterns = new NGL_Default_Patterns();
		$patterns->create( false );

		update_option( 'newsletterglue_updated_templates_done', 'yes' );

		// Add automations category.
		wp_insert_term( __( 'Automations', 'newsletter-glue' ), 'ngl_template_category', array( 'slug' => 'automations' ) );

		if ( isset( $_GET[ 'recreate-templates-patterns' ] ) ) { // phpcs:ignore
			wp_redirect( esc_url( remove_query_arg( 'recreate-templates-patterns' ) ) );
			exit;
		}
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {

		if ( ! is_blog_installed() || post_type_exists( 'newsletterglue' ) ) {
			return;
		}

		do_action( 'newsletterglue_register_post_types' );

		// Create newsletter post type.
		register_post_type(
			'newsletterglue',
			apply_filters(
				'newsletterglue_register_post_type_template',
				array(
					'labels'             => array(
						'name'                  => __( 'Campaigns', 'newsletter-glue' ),
						'singular_name'         => __( 'Campaign', 'newsletter-glue' ),
						'menu_name'             => esc_html_x( 'All Campaigns', 'Admin menu name', 'newsletter-glue' ),
						'add_new'               => __( 'Add Campaign', 'newsletter-glue' ),
						'add_new_item'          => __( 'Add New Campaign', 'newsletter-glue' ),
						'edit'                  => __( 'Edit', 'newsletter-glue' ),
						'edit_item'             => __( 'Edit Campaign', 'newsletter-glue' ),
						'new_item'              => __( 'New Campaign', 'newsletter-glue' ),
						'view_item'             => __( 'View Campaign', 'newsletter-glue' ),
						'search_items'          => __( 'Search Campaigns', 'newsletter-glue' ),
						'not_found'             => __( 'No Campaigns found', 'newsletter-glue' ),
						'not_found_in_trash'    => __( 'No Campaigns found in trash', 'newsletter-glue' ),
						'parent'                => __( 'Parent Campaign', 'newsletter-glue' ),
						'filter_items_list'     => __( 'Filter Campaigns', 'newsletter-glue' ),
						'items_list_navigation' => __( 'Campaigns navigation', 'newsletter-glue' ),
						'items_list'            => __( 'Newsletters list', 'newsletter-glue' ),
					),
					'description'         	=> __( 'This is where you can add new Campaigns to Newsletter Glue plugin.', 'newsletter-glue' ),
					'public'              	=> true,
					'show_ui'             	=> true,
					'capability_type'		=> 'newsletterglue',
					'capabilities'     		=> array(
						'edit_post' 			=> 'edit_newsletterglue',
						'edit_posts' 			=> 'edit_newsletterglue',
						'edit_others_posts' 	=> 'edit_newsletterglue',
						'publish_posts' 		=> 'add_newsletterglue',
						'read_post' 			=> 'edit_newsletterglue',
						'read_private_posts' 	=> 'edit_newsletterglue',
						'delete_post' 			=> 'add_newsletterglue',
						'delete_posts' 			=> 'add_newsletterglue',
						'create_posts' 			=> 'add_newsletterglue',
					),
					'publicly_queryable'  	=> true,
					'exclude_from_search' 	=> false,
					'show_in_menu'        	=> false,
					'hierarchical'        	=> false,
					'rewrite'             	=> array( 'slug' => get_option( 'newsletterglue_post_type_ep', 'newsletter' ) . '/%newsletter%', 'with_front' => false ),
					'query_var'           	=> true,
					'supports'           	=> array( 'title', 'editor', 'thumbnail', 'custom-fields', 'author' ),
					'taxonomies'        	=> array( 'ngl_newsletter_cat' ),
					'show_in_nav_menus'		=> true,
					'show_in_admin_bar'   	=> true,
					'show_in_rest'		  	=> true,
					'has_archive'			=> true,
				)
			)
		);

		// Create automated emails post type.
		register_post_type(
			'ngl_automation',
			apply_filters(
				'newsletterglue_register_automation_post_type',
				array(
					'labels'             => array(
						'name'                  => __( 'Automated emails', 'newsletter-glue' ),
						'singular_name'         => __( 'Automated email', 'newsletter-glue' ),
						'menu_name'             => esc_html_x( 'All Automations', 'Admin menu name', 'newsletter-glue' ),
						'add_new'               => __( 'Add Automation', 'newsletter-glue' ),
						'add_new_item'          => __( 'Add New Automation', 'newsletter-glue' ),
						'edit'                  => __( 'Edit', 'newsletter-glue' ),
						'edit_item'             => __( 'Edit Automation', 'newsletter-glue' ),
						'new_item'              => __( 'New Automation', 'newsletter-glue' ),
						'view_item'             => __( 'View Automation', 'newsletter-glue' ),
						'search_items'          => __( 'Search Automations', 'newsletter-glue' ),
						'not_found'             => __( 'No Automations found', 'newsletter-glue' ),
						'not_found_in_trash'    => __( 'No Automations found in trash', 'newsletter-glue' ),
						'parent'                => __( 'Parent Automation', 'newsletter-glue' ),
						'filter_items_list'     => __( 'Filter Automations', 'newsletter-glue' ),
						'items_list_navigation' => __( 'Automations navigation', 'newsletter-glue' ),
						'items_list'            => __( 'Automations list', 'newsletter-glue' ),
					),
					'description'         	=> __( 'This is where you can add new Automated emails to Newsletter Glue plugin.', 'newsletter-glue' ),
					'public'              	=> false,
					'show_ui'             	=> true,
					'capability_type'		=> 'newsletterglue',
					'capabilities'     		=> array(
						'edit_post' 			=> 'edit_newsletterglue',
						'edit_posts' 			=> 'edit_newsletterglue',
						'edit_others_posts' 	=> 'edit_newsletterglue',
						'publish_posts' 		=> 'add_newsletterglue',
						'read_post' 			=> 'edit_newsletterglue',
						'read_private_posts' 	=> 'edit_newsletterglue',
						'delete_post' 			=> 'add_newsletterglue',
						'delete_posts' 			=> 'add_newsletterglue',
						'create_posts' 			=> 'add_newsletterglue',
					),
					'publicly_queryable'  	=> false,
					'exclude_from_search' 	=> true,
					'show_in_menu'        	=> false,
					'hierarchical'        	=> false,
					'query_var'           	=> false,
					'supports'           	=> array( 'title', 'editor', 'custom-fields' ),
					'taxonomies'        	=> array(),
					'show_in_nav_menus'		=> true,
					'show_in_admin_bar'   	=> true,
					'show_in_rest'		  	=> true,
				)
			)
		);

		// Create email log post type.
		register_post_type(
			'ngl_log',
			apply_filters(
				'newsletterglue_register_log_post_type',
				array(
					'labels'             => array(
						'name'                  => __( 'Email log', 'newsletter-glue' ),
						'singular_name'         => __( 'Email log', 'newsletter-glue' ),
						'menu_name'             => esc_html_x( 'All Email logs', 'Admin menu name', 'newsletter-glue' ),
						'add_new'               => __( 'Add Email log', 'newsletter-glue' ),
						'add_new_item'          => __( 'Add New Email log', 'newsletter-glue' ),
						'edit'                  => __( 'Edit', 'newsletter-glue' ),
						'edit_item'             => __( 'Edit Email log', 'newsletter-glue' ),
						'new_item'              => __( 'New Email log', 'newsletter-glue' ),
						'view_item'             => __( 'View Email log', 'newsletter-glue' ),
						'search_items'          => __( 'Search Email logs', 'newsletter-glue' ),
						'not_found'             => __( 'No Email logs found', 'newsletter-glue' ),
						'not_found_in_trash'    => __( 'No Email logs found in trash', 'newsletter-glue' ),
						'parent'                => __( 'Parent Email log', 'newsletter-glue' ),
						'filter_items_list'     => __( 'Filter Email logs', 'newsletter-glue' ),
						'items_list_navigation' => __( 'Email logs navigation', 'newsletter-glue' ),
						'items_list'            => __( 'Email logs list', 'newsletter-glue' ),
					),
					'description'         	=> __( 'This is where you can add new email logs to Newsletter Glue plugin.', 'newsletter-glue' ),
					'public'              	=> false,
					'show_ui'             	=> true,
					'capability_type'		=> 'newsletterglue',
					'capabilities'     		=> array(
						'edit_post' 			=> 'edit_newsletterglue',
						'edit_posts' 			=> 'edit_newsletterglue',
						'edit_others_posts' 	=> 'edit_newsletterglue',
						'publish_posts' 		=> 'add_newsletterglue',
						'read_post' 			=> 'edit_newsletterglue',
						'read_private_posts' 	=> 'edit_newsletterglue',
						'delete_post' 			=> 'add_newsletterglue',
						'delete_posts' 			=> 'add_newsletterglue',
						'create_posts' 			=> is_multisite() ? 'do_not_allow' : false,
					),
					'publicly_queryable'  	=> false,
					'exclude_from_search' 	=> true,
					'show_in_menu'        	=> false,
					'hierarchical'        	=> false,
					'query_var'           	=> false,
					'supports'           	=> array(),
					'taxonomies'        	=> array(),
					'show_in_nav_menus'		=> true,
					'show_in_admin_bar'   	=> true,
					'show_in_rest'		  	=> true,
				)
			)
		);

		// Create newsletter category taxonomy.
		$args = array(
			'labels' => array(
				'name' 			=> __( 'Newsletter category', 'newsletter-glue' ),
				'singular_name' => __( 'Newsletter category', 'newsletter-glue' ),
			),
			'label'        			=> __( 'Newsletter category', 'newsletter-glue' ),
			'hierarchical' 			=> true,
			'rewrite'      			=> array( 'slug' => get_option( 'newsletterglue_post_type_ep', 'newsletter' ) ),
			'show_in_rest' 			=> true,
			'show_admin_column'		=> false,
		);

		register_taxonomy( 'ngl_newsletter_cat', array( 'newsletterglue' ), $args );

		// Add default terms (pattern categories)
		$default_categories = array(
			'archive' 		=> __( 'Archive', 'newsletter-glue' ),
		);

		foreach( $default_categories as $cat_id => $cat_name ) {
			$term = term_exists( $cat_id, 'ngl_newsletter_cat' ); // phpcs:ignore
			$default = get_option( 'newsletterglue_default_tax_id' );
			if ( ! $term && ! $default ) {
				$default_id = wp_insert_term( $cat_name, 'ngl_newsletter_cat', array( 'slug' => $cat_id ) );
				update_option( 'newsletterglue_default_tax_id', $default_id );
			} else {
				if ( $default ) {
					if ( ! is_wp_error( $default ) && isset( $default[ 'term_id' ] ) && ! term_exists( $default[ 'term_id' ], 'ngl_newsletter_cat' ) ) { // phpcs:ignore
						$default_id = wp_insert_term( $cat_name, 'ngl_newsletter_cat', array( 'slug' => $cat_id ) );
						update_option( 'newsletterglue_default_tax_id', $default_id );
					}
				} else {
					$default_id = wp_insert_term( $cat_name, 'ngl_newsletter_cat', array( 'slug' => $cat_id ) );
					if ( is_wp_error( $default_id ) ) {
						if ( isset( $default_id->error_data[ 'term_exists' ] ) ) {
							$term = get_term_by( 'id', $default_id->error_data[ 'term_exists' ], 'ngl_newsletter_cat' );
							update_option( 'newsletterglue_default_tax_id', ( array ) $term );
						}
					}
				}
			}
		}

		// Create template post type.
		$args = array(
			'labels'             => array(
				'name'                  => __( 'Templates', 'newsletter-glue' ),
				'singular_name'         => __( 'Template', 'newsletter-glue' ),
				'menu_name'             => esc_html_x( 'All Templates', 'Admin menu name', 'newsletter-glue' ),
				'add_new'               => __( 'Add New', 'newsletter-glue' ),
				'add_new_item'          => __( 'Add New Template', 'newsletter-glue' ),
				'edit'                  => __( 'Edit', 'newsletter-glue' ),
				'edit_item'             => __( 'Edit Template', 'newsletter-glue' ),
				'new_item'              => __( 'New Template', 'newsletter-glue' ),
				'view_item'             => __( 'View Template', 'newsletter-glue' ),
				'search_items'          => __( 'Search Templates', 'newsletter-glue' ),
				'not_found'             => __( 'No Templates found', 'newsletter-glue' ),
				'not_found_in_trash'    => __( 'No Templates found in trash', 'newsletter-glue' ),
				'parent'                => __( 'Parent Template', 'newsletter-glue' ),
				'filter_items_list'     => __( 'Filter Templates', 'newsletter-glue' ),
				'items_list_navigation' => __( 'Templates navigation', 'newsletter-glue' ),
				'items_list'            => __( 'Templates list', 'newsletter-glue' ),
			),
			'description'       	=> __( 'Description', 'newsletter-glue' ),
			'query_var'         	=> false,
			'supports'          	=> array( 'title', 'editor', 'custom-fields' ),
			'taxonomies'        	=> array( 'ngl_template_category' ),
			'publicly_queryable'  	=> true,
			'exclude_from_search' 	=> true,
			'show_ui'           	=> true,
			'rewrite'           	=> false,
			'show_in_rest'      	=> true,
			'show_in_menu'      	=> false,
			'show_in_admin_bar' 	=> false,
			'capability_type'		=> 'ngl_pattern',
			'capabilities'     		=> array(
				'edit_post' 			=> 'manage_newsletterglue_patterns',
				'edit_posts' 			=> 'manage_newsletterglue_patterns',
				'edit_others_posts' 	=> 'manage_newsletterglue_patterns',
				'publish_posts' 		=> 'manage_newsletterglue_patterns',
				'read_post' 			=> 'manage_newsletterglue_patterns',
				'read_private_posts' 	=> 'manage_newsletterglue_patterns',
				'delete_post' 			=> 'manage_newsletterglue_patterns',
				'delete_posts' 			=> 'manage_newsletterglue_patterns',
				'create_posts'			=> 'manage_newsletterglue_patterns',
			),
		);

		register_post_type( 'ngl_template', $args );

		// Create pattern post type.
		$args = array(
			'labels'             => array(
				'name'                  => __( 'Patterns', 'newsletter-glue' ),
				'singular_name'         => __( 'Pattern', 'newsletter-glue' ),
				'menu_name'             => esc_html_x( 'All Patterns', 'Admin menu name', 'newsletter-glue' ),
				'add_new'               => __( 'Add New', 'newsletter-glue' ),
				'add_new_item'          => __( 'Add New Pattern', 'newsletter-glue' ),
				'edit'                  => __( 'Edit', 'newsletter-glue' ),
				'edit_item'             => __( 'Edit Pattern', 'newsletter-glue' ),
				'new_item'              => __( 'New Pattern', 'newsletter-glue' ),
				'view_item'             => __( 'View Pattern', 'newsletter-glue' ),
				'search_items'          => __( 'Search Patterns', 'newsletter-glue' ),
				'not_found'             => __( 'No Patterns found', 'newsletter-glue' ),
				'not_found_in_trash'    => __( 'No Patterns found in trash', 'newsletter-glue' ),
				'parent'                => __( 'Parent Pattern', 'newsletter-glue' ),
				'filter_items_list'     => __( 'Filter Patterns', 'newsletter-glue' ),
				'items_list_navigation' => __( 'Patterns navigation', 'newsletter-glue' ),
				'items_list'            => __( 'Patterns list', 'newsletter-glue' ),
			),
			'description'       	=> __( 'Description', 'newsletter-glue' ),
			'query_var'         	=> false,
			'supports'          	=> array( 'title', 'editor', 'custom-fields' ),
			'taxonomies'        	=> array( 'ngl_pattern_category' ),
			'publicly_queryable'  	=> true,
			'exclude_from_search' 	=> true,
			'show_ui'           	=> true,
			'rewrite'           	=> false,
			'show_in_rest'      	=> true,
			'show_in_menu'      	=> false,
			'show_in_admin_bar' 	=> false,
			'capability_type'		=> 'ngl_pattern',
			'capabilities'     		=> array(
				'edit_post' 			=> 'manage_newsletterglue_patterns',
				'edit_posts' 			=> 'manage_newsletterglue_patterns',
				'edit_others_posts' 	=> 'manage_newsletterglue_patterns',
				'publish_posts' 		=> 'manage_newsletterglue_patterns',
				'read_post' 			=> 'manage_newsletterglue_patterns',
				'read_private_posts' 	=> 'manage_newsletterglue_patterns',
				'delete_post' 			=> 'manage_newsletterglue_patterns',
				'delete_posts' 			=> 'manage_newsletterglue_patterns',
				'create_posts'			=> 'manage_newsletterglue_patterns',
			),
		);

		register_post_type( 'ngl_pattern', $args );

		// Create template category taxonomy.
		$args = array(
			'label'        			=> __( 'Template category', 'newsletter-glue' ),
			'hierarchical' 			=> true,
			'rewrite'      			=> false,
			'show_in_rest' 			=> true,
			'show_admin_column'		=> false,
		);

		register_taxonomy( 'ngl_template_category', array( 'ngl_template' ), $args );

		// Create pattern category taxonomy.
		$args = array(
			'label'        			=> __( 'Pattern category', 'newsletter-glue' ),
			'hierarchical' 			=> true,
			'rewrite'      			=> false,
			'show_in_rest' 			=> true,
			'show_admin_column'		=> false,
		);

		register_taxonomy( 'ngl_pattern_category', array( 'ngl_pattern' ), $args );

		// Add default terms (pattern categories)
		if ( ! get_option( 'newsletterglue_inserted_pattern_cats' ) ) {
			$default_categories = array(
				'ngl_headers' 		=> __( 'Newsletter Header', 'newsletter-glue' ),
				'ngl_body' 			=> __( 'Newsletter Full Layout', 'newsletter-glue' ),
				'ngl_signoffs' 		=> __( 'Newsletter Callouts', 'newsletter-glue' ),
				'ngl_footers' 		=> __( 'Newsletter Footer', 'newsletter-glue' ),
				'ngl_uncategorized' => __( 'Newsletter Uncategorized', 'newsletter-glue' ),
			);

			foreach( $default_categories as $cat_id => $cat_name ) {
				$term = term_exists( $cat_id, 'ngl_pattern_category' ); // phpcs:ignore
				if ( ! $term ) {
					wp_insert_term( $cat_name, 'ngl_pattern_category', array( 'slug' => $cat_id ) );
				} else {
					wp_update_term( $term[ 'term_id' ], 'ngl_pattern_category', array( 'name' => $cat_name ) );
				}
			}

			update_option( 'newsletterglue_inserted_pattern_cats', 'yes' );
		}

		register_post_meta(
			'ngl_pattern',
			'_webview',
			array(
				'show_in_rest' 	=> true,
				'single'       	=> true,
				'type'         	=> 'string',
				'default'       => 'blog',
				'auth_callback' => function () { return current_user_can( 'manage_newsletterglue' ); }
			)
		);

		register_post_meta(
			'ngl_pattern',
			'_ngl_core_pattern',
			array(
				'show_in_rest' 	=> true,
				'single'       	=> true,
				'type'         	=> 'string',
				'default'       => '',
				'auth_callback' => function () { return current_user_can( 'manage_newsletterglue' ); }
			)
		);

		register_taxonomy_for_object_type( 'post_tag', 'newsletterglue' );

		do_action( 'newsletterglue_after_register_post_type' );

	}

	/**
	 * Add scripts.
	 */
	public static function admin_enqueue_scripts() {
		global $post_type;

		if ( ! is_admin() || empty( $post_type ) ) {
			return;
		}

		// Only in our CPT.
		if ( in_array( $post_type, newsletterglue_get_main_cpts() ) ) {
			wp_add_inline_script(
				'wp-edit-post',
				'
				wp.data.select( "core/edit-post" ).isFeatureActive( "welcomeGuide" ) && wp.data.dispatch( "core/edit-post" ).toggleFeature( "welcomeGuide" );
				wp.domReady(function () {
				  const allowedEmbedBlocks = [
					"twitter",
					"youtube",
					"spotify",
					"reddit",
					"soundcloud"
				  ];
				  const embedVariations = wp.blocks.getBlockVariations( "core/embed" );
				  if ( embedVariations ) {
					  embedVariations.forEach(function (blockVariation) {
						if (-1 === allowedEmbedBlocks.indexOf(blockVariation.name)) {
						  wp.blocks.unregisterBlockVariation( "core/embed", blockVariation.name);
						}
					  });
					}
				});
				'
			);
		}

	}

	/**
	 * Allowed block types.
	 */
	public static function allowed_block_types( $blocks, $object ) {
		if ( ! isset( $object->post ) ) {
			return $blocks;
		}
		$post = $object->post;
		if ( ! empty( $post->post_type ) ) {
			if ( in_array( $post->post_type, newsletterglue_get_main_cpts() ) ) {

				$blocks = array(
					'newsletterglue/group',
					'newsletterglue/form',
					'newsletterglue/article',
					'newsletterglue/author',
					'newsletterglue/callout',
					'newsletterglue/columns',
					'newsletterglue/column',
					'newsletterglue/metadata',
					'newsletterglue/share',
					'newsletterglue/share-link',
					'newsletterglue/latest-posts',
					'newsletterglue/text',
					'newsletterglue/buttons',
					'newsletterglue/button',
					'newsletterglue/container',
					'newsletterglue/sections',
					'newsletterglue/section',
					'newsletterglue/embed',
					'newsletterglue/table',
					'newsletterglue/spacer',
					'newsletterglue/separator',
					'newsletterglue/post-author',
					'newsletterglue/post-embeds',
					'newsletterglue/ad-inserter',
					'newsletterglue/showhide',
					'newsletterglue/social-icons',
					'newsletterglue/social-icon',
					'newsletterglue/optin',
					'newsletterglue/meta-data',
					'newsletterglue/list',
					'newsletterglue/list-item',
					'newsletterglue/image',
					'newsletterglue/heading',
					'core/block',
				);

				return apply_filters( 'newsletterglue_allowed_block_list', $blocks );
			}
		}
		return $blocks;
	}

	/**
	 * Register core post types.
	 */
	public static function admin_head() {
		global $post_type, $post, $ngl_post_id;

		if ( empty( $post_type ) ) {
			return;
		}

		if ( isset( $post ) && ! empty( $post->ID ) ) {
			$ngl_post_id = $post->ID;
		}

		// Add What are patterns?
		if ( in_array( $post_type, newsletterglue_get_core_cpts() ) ) {
			?>
			<style type="text/css">
			.editor-post-taxonomies__hierarchical-terms-input + div {
				opacity: 0;
				visibility: hidden !important;
				height: 0px !important;
			}
			</style>
			<?php
		}

		if ( in_array( $post_type, newsletterglue_get_main_cpts() ) ) {

			$spacer_bg = 'transparent';

			$font_face = esc_attr( newsletterglue_get_font_name( newsletterglue_get_theme_option( 'p_font' ) ) );

			echo '<style>';

			echo 'div.editor-visual-editor div.editor-visual-editor__content-area,
			div.editor-visual-editor__content-area > div, .block-editor-iframe__container { background: ' . esc_attr( newsletterglue_get_theme_option( 'email_bg' ) ) . '; }';
			echo 'div.editor-styles-wrapper.block-editor-writing-flow { background: ' . esc_attr( newsletterglue_get_theme_option( 'email_bg' ) ) . ' !important; }';

			$top_p = newsletterglue_get_theme_option( 'container_padding1' );
			$top_p = strstr( $top_p, 'px' ) ? $top_p : absint( $top_p ) . 'px';
			if ( $top_p ) {
				echo '.editor-styles-wrapper.block-editor-writing-flow div.is-root-container.block-editor-block-list__layout, .is-mobile-preview, .is-tablet-preview { padding-top: ' . esc_attr( $top_p ) . ' !important; }';
			}

			$bottom_p = newsletterglue_get_theme_option( 'container_padding2' );
			$bottom_p = strstr( $bottom_p, 'px' ) ? $bottom_p : absint( $bottom_p ) . 'px';
			if ( $bottom_p ) {
				echo '.editor-styles-wrapper.block-editor-writing-flow div.is-root-container.block-editor-block-list__layout, .is-mobile-preview, .is-tablet-preview { padding-bottom: ' . esc_attr( $bottom_p ) . ' !important; }';
			}

			$top_m = newsletterglue_get_theme_option( 'container_margin1' );
			$top_m = strstr( $top_m, 'px' ) ? $top_m : absint( $top_m ) . 'px';
			if ( $top_m ) {
				echo '.editor-styles-wrapper.block-editor-writing-flow div.is-root-container.block-editor-block-list__layout, .is-mobile-preview, .is-tablet-preview { margin-top: ' . esc_attr( $top_m ) . ' !important; }';
			}

			$bottom_m = newsletterglue_get_theme_option( 'container_margin2' );
			$bottom_m = strstr( $bottom_m, 'px' ) ? $bottom_m : absint( $bottom_m ) . 'px';
			if ( $bottom_m ) {
				echo '.editor-styles-wrapper.block-editor-writing-flow div.is-root-container.block-editor-block-list__layout, .is-mobile-preview, .is-tablet-preview { margin-bottom: ' . esc_attr( $bottom_m ) . ' !important; }';
			}

			echo 'div.editor-visual-editor__content-area .is-desktop-preview { background: inherit !important; }';
			
			echo '.is-root-container { background: ' . esc_attr( newsletterglue_get_theme_option( 'container_bg' ) ) . '; }';
			echo '.is-mobile-preview, .block-editor-iframe__container iframe { background: ' . esc_attr( newsletterglue_get_theme_option( 'container_bg' ) ) . ' !important; }';

			echo 'div.editor-styles-wrapper .wp-block.editor-post-title__block { padding-bottom: 0; margin: 0; max-width: 100%; border: 0; }';

			if ( $font_face ) {
				echo '.editor-styles-wrapper > *, div.editor-styles-wrapper textarea.editor-post-title__input, div.editor-styles-wrapper p, div.editor-styles-wrapper ol:not(.ng-block), div.editor-styles-wrapper ul:not(.ng-block), .editor-styles-wrapper dl, .editor-styles-wrapper dt,div.editor-styles-wrapper .wp-block h1, div.editor-styles-wrapper .wp-block h2, div.editor-styles-wrapper .wp-block h3, div.editor-styles-wrapper .wp-block h4, div.editor-styles-wrapper .wp-block h5, div.editor-styles-wrapper .wp-block h6, div.editor-styles-wrapper h1, div.editor-styles-wrapper h2, div.editor-styles-wrapper h3, div.editor-styles-wrapper h4,
				div.editor-styles-wrapper h5, div.editor-styles-wrapper h6 {
						font-family: ' . esc_attr( $font_face ) . ', Arial, Helvetica, sans-serif; }';
			} else {
				echo '.editor-styles-wrapper > *, div.editor-styles-wrapper textarea.editor-post-title__input, div.editor-styles-wrapper p, div.editor-styles-wrapper ol:not(.ng-block), div.editor-styles-wrapper ul:not(.ng-block), .editor-styles-wrapper dl, .editor-styles-wrapper dt,div.editor-styles-wrapper .wp-block h1, div.editor-styles-wrapper .wp-block h2, div.editor-styles-wrapper .wp-block h3, div.editor-styles-wrapper .wp-block h4, div.editor-styles-wrapper .wp-block h5, div.editor-styles-wrapper .wp-block h6, div.editor-styles-wrapper h1, div.editor-styles-wrapper h2, div.editor-styles-wrapper h3, div.editor-styles-wrapper h4,
				div.editor-styles-wrapper h5, div.editor-styles-wrapper h6 {
						font-family: Arial, Helvetica, sans-serif; }';
			}

			$headings = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p' );
			foreach( $headings as $heading ) {
				$font = newsletterglue_get_theme_option( $heading . '_font' );
				if ( ! empty( $font ) ) {
					$font_label = newsletterglue_get_font_name( $font );
					echo 'div.editor-styles-wrapper ' . esc_attr( $heading ) . '.wp-block { font-family: ' . esc_attr( $font_label ) . ', Arial, Helvetica, sans-serif; }';
				}
			}
			
			echo 'div.editor-styles-wrapper .wp-block.wp-block-quote {
				border-width: 3px;
				border-color: ' . esc_attr( newsletterglue_get_theme_option( 'accent' ) ) . ';
			}';

			echo 'div.editor-styles-wrapper a, div.editor-styles-wrapper .wp-block a { color: ' . esc_attr( newsletterglue_get_theme_option( 'a_colour' ) ) . '; text-decoration: none; }';

			echo 'div.editor-styles-wrapper a, div.editor-styles-wrapper .wp-block.has-text-color a { color: inherit; text-decoration: underline; }';

			echo 'div.editor-styles-wrapper, div.editor-styles-wrapper p, .ngl-article, .ngl-article-excerpt { color: ' . esc_attr( newsletterglue_get_theme_option( 'p_colour' ) ) . '; }';
			echo 'div.editor-styles-wrapper p, div.editor-styles-wrapper li:not(.ng-block), div.editor-styles-wrapper blockquote.wp-block-quote p, div.editor-styles-wrapper blockquote p { font-size: ' . esc_attr( newsletterglue_get_theme_option( 'p_size' ) ) . 'px; }';
			echo '.editor-styles-wrapper blockquote.wp-block-quote p { font-weight: normal; }';

			echo 'div.editor-styles-wrapper .wp-block.editor-post-title__block textarea.editor-post-title__input, div.editor-styles-wrapper h1, div.editor-styles-wrapper .wp-block h1, div.editor-styles-wrapper h1.wp-block-post-title { font-size: ' . esc_attr( newsletterglue_get_theme_option( 'h1_size' ) ) . 'px; color: ' . esc_attr( newsletterglue_get_theme_option( 'h1_colour' ) ) . '; font-weight: bold; }';

			echo 'div.editor-styles-wrapper h2, div.editor-styles-wrapper .wp-block h2, div.editor-styles-wrapper h2.wp-block-post-title { font-size: ' . esc_attr( newsletterglue_get_theme_option( 'h2_size' ) ) . 'px; color: ' . esc_attr( newsletterglue_get_theme_option( 'h2_colour' ) ) . '; font-weight: bold; }';
			
			echo 'div.editor-styles-wrapper h3, div.editor-styles-wrapper .wp-block h3, div.editor-styles-wrapper h3.wp-block-post-title { font-size: ' . esc_attr( newsletterglue_get_theme_option( 'h3_size' ) ) . 'px; color: ' . esc_attr( newsletterglue_get_theme_option( 'h3_colour' ) ) . '; font-weight: bold; }';
			echo 'div.editor-styles-wrapper h4, div.editor-styles-wrapper .wp-block h4, div.editor-styles-wrapper h4.wp-block-post-title { font-size: ' . esc_attr( newsletterglue_get_theme_option( 'h4_size' ) ) . 'px; color: ' . esc_attr( newsletterglue_get_theme_option( 'h4_colour' ) ) . '; font-weight: bold; }';
			echo 'div.editor-styles-wrapper h5, div.editor-styles-wrapper .wp-block h5, div.editor-styles-wrapper h5.wp-block-post-title { font-size: ' . esc_attr( newsletterglue_get_theme_option( 'h5_size' ) ) . 'px; color: ' . esc_attr( newsletterglue_get_theme_option( 'h5_colour' ) ) . '; font-weight: bold; }';
			echo 'div.editor-styles-wrapper h6, div.editor-styles-wrapper .wp-block h6, div.editor-styles-wrapper h6.wp-block-post-title { font-size: ' . esc_attr( newsletterglue_get_theme_option( 'h6_size' ) ) . 'px; color: ' . esc_attr( newsletterglue_get_theme_option( 'h6_colour' ) ) . '; font-weight: bold; }';

			echo 'div.editor-styles-wrapper .wp-block-button__link,
				div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:hover,
				div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:active
				{ font-size: ' . esc_attr( newsletterglue_get_theme_option( 'p_size' ) ) . 'px;text-align: center; text-transform: none; padding: 14px 20px; font-weight: inherit; min-width: ' . esc_attr( newsletterglue_get_theme_option( 'btn_width' ) ) . 'px; background-color: ' . esc_attr( newsletterglue_get_theme_option( 'btn_bg' ) ) . '; color: ' . esc_attr( newsletterglue_get_theme_option( 'btn_colour' ) ) . '; border-radius: 0px; line-height: 1.25; }';

			echo 'div.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link,
				div.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link:hover,
				div.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link:active
			{ padding: 12px 20px; color: ' . esc_attr( newsletterglue_get_theme_option( 'btn_bg' ) ) . '; background-color: transparent !important; border: 2px solid ' . esc_attr( newsletterglue_get_theme_option( 'btn_bg' ) ) . '!important; }';

			echo 'div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:hover, div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:active {
					background-color: 0 !important;
			}';

			echo 'div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:not(:hover):not(:active):not(.has-background),
				div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:hover:active:not(.has-background) {
					background-color: ' . esc_attr( newsletterglue_get_theme_option( 'btn_bg' ) ) . ';
			}';

			echo 'div.editor-styles-wrapper .wp-block .wp-block-newsletterglue-callout.is-color-set .block-editor-block-list__layout > * { color: inherit; }';

			echo '.wp-block-spacer, div.block-library-spacer__resize-container.has-show-handle { background-color: ' . esc_attr( $spacer_bg ) . '; }';

			do_action( 'newsletterglue_add_custom_styles', ! empty( $post ) ? $post : null );

			echo '</style>';
		}

	}

	/**
	 * Register custom post type posts (with the 'pattern' type) as block patterns.
	 */
	public static function load_block_patterns() {

		$query_args = array(
			'post_type'              => 'ngl_pattern',
			'post_status'			 => 'publish',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		$block_patterns_query = new \WP_Query( $query_args );

		wp_reset_postdata();

		if ( empty( $block_patterns_query->posts ) ) {
			return;
		}

		$pattern_categories = '';

		foreach ( $block_patterns_query->posts as $block_pattern ) {
			$pattern_categories = null;

			$categories = get_the_terms( $block_pattern->ID, 'ngl_pattern_category' );

			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$pattern_categories = wp_list_pluck( $categories, 'slug' );
			}

			if ( empty( $pattern_categories ) ) {
				$pattern_categories = array( 'ngl_uncategorized' );
			} else {

				foreach( $pattern_categories as $key => $value ) {
					if ( substr( $value, 0, 4 ) !== 'ngl_' ) {
						$pattern_categories[] = 'ngl_' . str_replace( '-', '_', $value );
					}
				}

			}

			register_block_pattern(
				'ngl_pattern/' . $block_pattern->post_name,
				array(
					'title'       => $block_pattern->post_title,
					'content'     => $block_pattern->post_content,
					'categories'  => $pattern_categories,
				)
			);
		}

	}

	/**
	 * Register custom post type posts (with the 'pattern' type) as block patterns.
	 */
	public static function register_block_category() {

		$unregister_default_patterns = false;

		$post_id 	= isset( $_GET[ 'post' ] ) ? absint( $_GET[ 'post' ] ) : 0; // phpcs:ignore
		$edit	 	= isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'edit' ? true : false; // phpcs:ignore

		if ( $post_id && $edit ) {
			$thepost = get_post( $post_id );
			if ( in_array( $thepost->post_type, newsletterglue_get_main_cpts() ) ) {
				$unregister_default_patterns = true;
			}
		}

		if ( isset( $_GET[ 'post_type' ] ) ) { // phpcs:ignore
			if ( in_array( $_GET[ 'post_type' ], newsletterglue_get_main_cpts() ) ) { // phpcs:ignore
				$unregister_default_patterns = true;
			}
		}

		if ( class_exists( 'WP_Block_Patterns_Registry' ) ) {

			register_block_pattern_category(
				'ngl_headers',
				array( 'label' => _x( 'Newsletter Header', 'Block pattern category', 'newsletter-glue' ) )
			);

			register_block_pattern_category(
				'ngl_signoffs',
				array( 'label' => _x( 'Newsletter Callouts', 'Block pattern category', 'newsletter-glue' ) )
			);

			register_block_pattern_category(
				'ngl_body',
				array( 'label' => _x( 'Newsletter Full Layout', 'Block pattern category', 'newsletter-glue' ) )
			);

			register_block_pattern_category(
				'ngl_footers',
				array( 'label' => _x( 'Newsletter Footer', 'Block pattern category', 'newsletter-glue' ) )
			);

			register_block_pattern_category(
				'ngl_uncategorized',
				array( 'label' => _x( 'Newsletter Uncategorized', 'Block pattern category', 'newsletter-glue' ) )
			);

			// Get all terms.
			$terms = get_terms( array(
				'taxonomy'		=> 'ngl_pattern_category',
				'hide_false' 	=> false,
				'orderby'		=> 'term_id',
				'order'			=> 'asc'
			) );

			if ( $terms ) {
				foreach( $terms as $term ) {
					if ( substr( $term->slug, 0, 4 ) !== 'ngl_' ) {
						register_block_pattern_category(
							'ngl_' . str_replace( '-', '_', $term->slug ),
							array( 'label' => _x( $term->name, 'Block pattern category', 'newsletter-glue' ) )
						);
					}
				}
			}

			// Unregister everything else.
			if ( $unregister_default_patterns ) {
				$categories = WP_Block_Pattern_Categories_Registry::get_instance()->get_all_registered();
				foreach( $categories as $key => $value ) {
					if ( ! strstr( $value[ 'name' ], 'ngl_' ) ) {
						unregister_block_pattern_category( $value[ 'name' ] );
					}
				}
				$patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
				foreach( $patterns as $key => $value ) {
					if ( ! strstr( $value[ 'name' ], 'ngl_pattern/' ) ) {
						unregister_block_pattern( $value[ 'name' ] );
					}
				}
			}

		}

	}

	/**
	 * Remove date filter.
	 */
	public static function months_dropdown_results( $months ) {
		global $typenow;

		if ( $typenow == 'ngl_pattern' ) {
			return array();
		}

		return $months;
	}

	/**
	 * Add category dropdown filter.
	 */
	public static function restrict_manage_posts() {
		global $typenow, $post, $post_id;

		if ( $typenow == 'ngl_pattern' ) {

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
						$label = ( isset( $_GET[ $tax_slug ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ $tax_slug ] ) ) : ''; // phpcs:ignore
						echo '<option value=' . $term->slug, $label == $term->slug ? ' selected="selected"' : '','>' . esc_html( $term->name ) . '</option>';
					}
					echo "</select>";
				}
			}
		}
	}

	/**
	 * Views edit.
	 */
	public static function views_edit( $views ) {

		$terms = get_terms( array( 'taxonomy' => 'ngl_pattern_category', 'hide_empty' => false, 'orderby' => 'term_id', 'order' => 'asc' ) );

		unset( $views[ 'publish' ] );

		$current = '';

		foreach( $terms as $term ) {
			if ( strstr( $term->slug, 'ngl_' ) ) {
				if ( isset( $_GET[ 'ngl_pattern_category' ] ) ) { // phpcs:ignore
					if ( $_GET[ 'ngl_pattern_category' ] == $term->slug ) { // phpcs:ignore
						$current = 'current';
					} else {
						$current = '';
					}
				}
				$views[ $term->slug ] = '<a href="' . admin_url( 'edit.php?post_type=ngl_pattern&ngl_pattern_category=' . $term->slug ) . '" class="' . $current . '">' . str_replace( 'Newsletter ', '', $term->name ) . ' <span class="count">(' . $term->count . ')</span></a>';
			}
		}

		return $views;

	}

	/**
	 * Save a newsletter.
	 */
	public static function save_newsletter( $post_id, $post ) {
		// $post_id and $post are required
		$saved_meta_boxes = false;

		// only for patterns.
		if ( $post->post_type !== 'newsletterglue' ) {
			return;
		}

		// Require post ID and post object.
		if ( empty( $post_id ) || empty( $post ) || $saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		// Only allow published and scheduled posts.
		if ( ! in_array( $post->post_status, array( 'publish' ) ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		$saved_meta_boxes = true;

		$terms = wp_get_object_terms( $post_id, 'ngl_newsletter_cat' );
		if ( empty( $terms ) ) {
			$default = get_option( 'newsletterglue_default_tax_id' );
			if ( isset( $default[ 'term_id' ] ) ) {
				$the_term = get_term_by( 'id', $default[ 'term_id' ], 'ngl_newsletter_cat' );
				wp_set_object_terms( $post_id, array( $the_term->slug ), 'ngl_newsletter_cat' );
			}
		}
	}

	/**
	 * Save a pattern.
	 */
	public static function save_pattern( $post_id, $post ) {
		// $post_id and $post are required
		$saved_meta_boxes = false;

		// only for patterns.
		if ( $post->post_type !== 'ngl_pattern' ) {
			return;
		}

		// Require post ID and post object.
		if ( empty( $post_id ) || empty( $post ) || $saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		// Only allow published and scheduled posts.
		if ( ! in_array( $post->post_status, array( 'publish' ) ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		$saved_meta_boxes = true;

		$terms = wp_get_object_terms( $post_id, 'ngl_pattern_category' );
		if ( empty( $terms ) ) {
			wp_set_object_terms( $post_id, array( 'ngl_uncategorized' ), 'ngl_pattern_category' );
		}
	}

	/**
	 * Duplicate template.
	 */
	public static function duplicate_template() {
		global $wpdb;

		if ( ! ( isset( $_GET['post']) || isset( $_POST['post'] )  || ( isset( $_GET['action']) && 'ngl_duplicate_as_template' == $_GET['action'] ) ) ) {
			wp_die( esc_html__( 'Nothing to duplicate was found.', 'newsletter-glue' ) );
		}

		$nonce = isset( $_GET[ 'ngl_duplicate_nonce' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'ngl_duplicate_nonce' ] ) ) : '';

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, plugin_basename( NGL_PLUGIN_FILE ) ) ) {
			return;
		}

		$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

		$post = get_post( $post_id );
	 
		/*
		 * if post data exists, create the post duplicate
		 */
		if ( isset( $post ) && $post != null ) {

			$new_post = newsletterglue_duplicate_item( $post, $post_id );

			wp_redirect( admin_url( 'edit.php?post_type=ngl_template' ) );

			exit;

		} else {
			wp_die( esc_html__( 'Duplicate template has failed.', 'newsletter-glue' ) );
		}

	}

	/**
	 * Duplicate pattern.
	 */
	public static function duplicate_pattern() {
		global $wpdb;

		if ( ! ( isset( $_GET['post']) || isset( $_POST['post'] )  || ( isset( $_GET['action']) && 'ngl_duplicate_as_pattern' == $_GET['action'] ) ) ) {
			wp_die( esc_html__( 'Nothing to duplicate was found.', 'newsletter-glue' ) );
		}

		$nonce = isset( $_GET[ 'ngl_duplicate_nonce' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'ngl_duplicate_nonce' ] ) ) : '';

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, plugin_basename( NGL_PLUGIN_FILE ) ) ) {
			return;
		}

		$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

		$post = get_post( $post_id );
	 
		/*
		 * if post data exists, create the post duplicate
		 */
		if ( isset( $post ) && $post != null ) {

			$new_post = newsletterglue_duplicate_item( $post, $post_id );

			wp_redirect( admin_url( 'edit.php?post_type=ngl_pattern' ) );

			exit;

		} else {
			wp_die( esc_html__( 'Duplicate pattern has failed.', 'newsletter-glue' ) );
		}

	}

	/**
	 * Duplicate newsletter.
	 */
	public static function duplicate_newsletter() {
		global $wpdb;

		if ( ! ( isset( $_GET['post']) || isset( $_POST['post'] )  || ( isset( $_GET['action']) && 'ngl_duplicate_as_newsletter' == $_GET['action'] ) ) ) {
			wp_die( esc_html__( 'Nothing to duplicate was found.', 'newsletter-glue' ) );
		}

		$nonce = isset( $_GET[ 'ngl_duplicate_nonce' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'ngl_duplicate_nonce' ] ) ) : '';

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, plugin_basename( NGL_PLUGIN_FILE ) ) ) {
			return;
		}

		$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

		$post = get_post( $post_id );
	 
		/*
		 * if post data exists, create the post duplicate
		 */
		if ( isset( $post ) && $post != null ) {

			$new_post = newsletterglue_duplicate_item( $post, $post_id );

			wp_redirect( admin_url( 'edit.php?post_type=newsletterglue' ) );
			exit;

		} else {
			wp_die( esc_html__( 'Duplicate newsletter has failed.', 'newsletter-glue' ) );
		}

	}

	/**
	 * Enqueue block editor js.
	 */
	public static function enqueue_block_editor_assets() {
		global $post_type;

		$js_dir = NGL_PLUGIN_URL . 'assets/js/gutenberg/';

		$app = newsletterglue_default_connection();
		if ( $app && file_exists( newsletterglue_get_path( $app ) . '/functions.php' ) ) {

			$cpts = newsletterglue_get_active_cpts();
			if ( in_array( $post_type, $cpts ) && current_user_can( 'publish_newsletterglue' ) ) {
				include_once newsletterglue_get_path( $app ) . '/functions.php';
				$function = 'newsletterglue_get_' . strtolower( $app ) . '_tags';
				if ( function_exists( $function ) ) {
					wp_enqueue_script(
						'ngl-editor-bw-js',
						$js_dir . 'editor-bw.js',
						array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
						time()
					);
				}
			}

		}

		if ( ! in_array( $post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
			return;
		}

	}

	/**
	 * Pattern tabs.
	 */
	public static function add_topbar() {
		global $post_type, $pagenow;
		return;
		if ( $pagenow == 'edit.php' && $post_type == 'ngl_pattern' ) {

		?>
		<nav class="nav-tab-wrapper" style="padding-top: 30px;">
			<?php
				$tabs = array(
					'ngl_template'		=> __( 'Templates', 'newsletter-glue' ),
					'ngl_pattern'		=> __( 'Patterns', 'newsletter-glue' ),
					'ngl_style'			=> __( 'Styles', 'newsletter-glue' ),
				);

				foreach( $tabs as $key => $name ) {
					$current = $key === $post_type ? 'nav-tab-active' : '';
					echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . esc_attr( $key ) ) ) . '" class="nav-tab ' . esc_attr( $current ) . '">' . esc_html( $name ) . '</a>';
				}
			?>
		</nav>
		<?php
		}
	}

	/**
	 * Force Gutenberg use - compatibility issues.
	 */
	public static function use_block_editor_for_post_type( $is_enabled, $post_type ) {

		if ( in_array( $post_type, newsletterglue_get_main_cpts() ) ) {
			return true;
		}

		return $is_enabled;
	}

	/**
	 * Webview.
	 */
	public static function show_webview() {
		global $post;

		if ( is_single() && isset( $post ) && ! empty( $post->post_type ) && $post->post_type == 'newsletterglue' ) {
			$webview = get_post_meta( $post->ID, '_webview', true );
			if ( $webview === 'email' ) {
				ob_start();

				$post_id 	= $post->ID;
				$data 		= get_post_meta( $post_id, '_newsletterglue', true );
				$app 		= isset( $data[ 'app' ] ) ? $data[ 'app' ] : '';

				if ( $app ) {
					include_once newsletterglue_get_path( $app ) . '/init.php';
					$classname = 'NGL_' . ucfirst( $app );
					$api = new $classname();
				}

				echo newsletterglue_generate_content( $post_id, ! empty( $data[ 'subject' ] ) ? $data[ 'subject' ] : '', $app ); // phpcs:ignore

				$message = ob_get_clean();

				echo $message; // phpcs:ignore

				exit;
			}
		}
	}

}

NGL_CPT::init();
