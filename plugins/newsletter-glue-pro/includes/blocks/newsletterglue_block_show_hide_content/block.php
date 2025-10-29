<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Show_Hide_Content extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_show_hide_content';

	public $is_pro = false;

	private $app;

	public $asset_id;

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		add_action( 'init', array( $this, 'register_block' ), 10 );
		add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );

        $this->app = newsletterglue_default_connection();

        // Ajax hooks.
        add_action( 'wp_ajax_newsletterglue_block_show_hide_refresh', array( $this, 'newsletterglue_block_show_hide_refresh' ) );
        add_action( 'wp_ajax_nopriv_newsletterglue_block_show_hide_refresh', array( $this, 'newsletterglue_block_show_hide_refresh' ) );

	}

	/**
	 * Demo URL.
	 */
	public function get_demo_url() {
		return 'https://www.youtube.com/embed/cblUTTpCHg0?autoplay=1&modestbranding=1&autohide=1&showinfo=0&controls=0&start=15';
	}

	/**
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45 36" class="ngl-block-svg-icon"><path d="M22.5,28.125a10.087,10.087,0,0,1-10.048-9.359l-7.376-5.7a23.435,23.435,0,0,0-2.582,3.909,2.275,2.275,0,0,0,0,2.052A22.552,22.552,0,0,0,22.5,31.5a21.84,21.84,0,0,0,5.477-.735l-3.649-2.823a10.134,10.134,0,0,1-1.828.184ZM44.565,32.21,36.792,26.2a23.291,23.291,0,0,0,5.713-7.177,2.275,2.275,0,0,0,0-2.052A22.552,22.552,0,0,0,22.5,4.5,21.667,21.667,0,0,0,12.142,7.151L3.2.237a1.125,1.125,0,0,0-1.579.2L.237,2.211a1.125,1.125,0,0,0,.2,1.579L41.8,35.763a1.125,1.125,0,0,0,1.579-.2l1.381-1.777a1.125,1.125,0,0,0-.2-1.579ZM31.648,22.226,28.884,20.09a6.663,6.663,0,0,0-8.164-8.573,3.35,3.35,0,0,1,.655,1.984,3.279,3.279,0,0,1-.108.7l-5.176-4A10.006,10.006,0,0,1,22.5,7.875,10.119,10.119,0,0,1,32.625,18a9.885,9.885,0,0,1-.977,4.226Z" transform="translate(0 0)"/></svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Show/hide content', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Hide selected content from your blog/newsletter.', 'newsletter-glue' );
	}

	/**
	 * Get defaults.
	 */
	public function get_defaults() {

		return array(
			'showemail'	=> true,
			'showblog'	=> false,
		);

	}

	/**
	 * Register the block.
	 */
	public function register_block() {

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {

			$operators = array(
				'activecampaign' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field,tag' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field,tag' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field,tag' ),
					array( 'label' => 'is less than', 'value' => 'lt', 'for' => 'field' ),
					array( 'label' => 'is greater than', 'value' => 'gt', 'for' => 'field' ),
					array( 'label' => 'is less than or equal to', 'value' => 'lte', 'for' => 'field' ),
					array( 'label' => 'is greater than or equal to', 'value' => 'gte', 'for' => 'field' ),
					array( 'label' => 'contains', 'value' => 'con', 'for' => 'field' ),
					array( 'label' => 'not contains', 'value' => 'ncon', 'for' => 'field' ),
					array( 'label' => 'is exist', 'value' => 'ex', 'for' => 'field,tag' ),
					array( 'label' => 'does not exist', 'value' => 'nex', 'for' => 'field,tag' ),
				),
				'aweber' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field,tag' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field,tag' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field' ),
					array( 'label' => 'is less than', 'value' => 'lt', 'for' => 'field' ),
					array( 'label' => 'is greater than', 'value' => 'gt', 'for' => 'field' ),
					array( 'label' => 'is less than or equal to', 'value' => 'lte', 'for' => 'field' ),
					array( 'label' => 'is greater than or equal to', 'value' => 'gte', 'for' => 'field' ),
					array( 'label' => 'is exist', 'value' => 'ex', 'for' => 'tag' ),
				),
				'brevo' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field' ),
					array( 'label' => 'is less than', 'value' => 'lt', 'for' => 'field' ),
					array( 'label' => 'is greater than', 'value' => 'gt', 'for' => 'field' ),
					array( 'label' => 'is less than or equal to', 'value' => 'lte', 'for' => 'field' ),
					array( 'label' => 'is greater than or equal to', 'value' => 'gte', 'for' => 'field' ),
					array( 'label' => 'contains', 'value' => 'con', 'for' => 'field' ),
					array( 'label' => 'not contains', 'value' => 'ncon', 'for' => 'field' ),
					array( 'label' => 'is exist', 'value' => 'ex', 'for' => 'field' ),
					array( 'label' => 'does not exist', 'value' => 'nex', 'for' => 'field' ),
				),
				'campaignmonitor' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field' ),
					array( 'label' => 'is exist', 'value' => 'ex', 'for' => 'field' ),
					array( 'label' => 'does not exist', 'value' => 'nex', 'for' => 'field' ),
				),
				'getresponse' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field,tag' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field,tag' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field,tag' ),
					array( 'label' => 'is less than', 'value' => 'lt', 'for' => 'field' ),
					array( 'label' => 'is greater than', 'value' => 'gt', 'for' => 'field' ),
					array( 'label' => 'is less than or equal to', 'value' => 'lte', 'for' => 'field' ),
					array( 'label' => 'is greater than or equal to', 'value' => 'gte', 'for' => 'field' ),
					array( 'label' => 'is exist', 'value' => 'ex', 'for' => 'field,tag' ),
					array( 'label' => 'does not exist', 'value' => 'nex', 'for' => 'field,tag' ),
				),
				'klaviyo' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field' ),
					array( 'label' => 'is less than', 'value' => 'lt', 'for' => 'field' ),
					array( 'label' => 'is greater than', 'value' => 'gt', 'for' => 'field' ),
					array( 'label' => 'is less than or equal to', 'value' => 'lte', 'for' => 'field' ),
					array( 'label' => 'is greater than or equal to', 'value' => 'gte', 'for' => 'field' ),
					array( 'label' => 'is exist', 'value' => 'ex', 'for' => 'field' ),
					array( 'label' => 'does not exist', 'value' => 'nex', 'for' => 'field' ),
				),
				'mailchimp' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field' ),
					array( 'label' => 'is less than', 'value' => 'lt', 'for' => 'field' ),
					array( 'label' => 'is greater than', 'value' => 'gt', 'for' => 'field' ),
					array( 'label' => 'is less than or equal to', 'value' => 'lte', 'for' => 'field' ),
					array( 'label' => 'is greater than or equal to', 'value' => 'gte', 'for' => 'field' ),
					array( 'label' => 'is exist', 'value' => 'ex', 'for' => 'field' ),
					array( 'label' => 'does not exist', 'value' => 'nex', 'for' => 'field' ),
				),
				'moosend' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field' ),
				),
				'sailthru' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field' ),
					array( 'label' => 'is less than', 'value' => 'lt', 'for' => 'field' ),
					array( 'label' => 'is greater than', 'value' => 'gt', 'for' => 'field' ),
					array( 'label' => 'is less than or equal to', 'value' => 'lte', 'for' => 'field' ),
					array( 'label' => 'is greater than or equal to', 'value' => 'gte', 'for' => 'field' ),
					array( 'label' => 'is exist', 'value' => 'ex', 'for' => 'field' ),
					array( 'label' => 'does not exist', 'value' => 'nex', 'for' => 'field' ),
					array( 'label' => 'contains', 'value' => 'con', 'for' => 'field' ),
					array( 'label' => 'not contains', 'value' => 'ncon', 'for' => 'field' ),
					array( 'label' => 'and', 'value' => 'and', 'for' => 'field' ),
					array( 'label' => 'or', 'value' => 'or', 'for' => 'field' ),
				),
				'sendgrid' => array(
					array( 'label' => 'Select an option', 'value' => '', 'for' => 'field' ),
					array( 'label' => 'is equal to', 'value' => 'eq', 'for' => 'field' ),
					array( 'label' => 'is not equal to', 'value' => 'neq', 'for' => 'field' ),
					array( 'label' => 'is less than', 'value' => 'lt', 'for' => 'field' ),
					array( 'label' => 'is greater than', 'value' => 'gt', 'for' => 'field' ),
					array( 'label' => 'is exist', 'value' => 'ex', 'for' => 'field' ),
					array( 'label' => 'does not exist', 'value' => 'nex', 'for' => 'field' ),
					array( 'label' => 'and', 'value' => 'and', 'for' => 'field' ),
					array( 'label' => 'or', 'value' => 'or', 'for' => 'field' ),
				),
			);

			// Commented out for testing MC conditional logic.
			// $licensed = in_array( newsletterglue_get_tier(), array( 'newsroom' ) );
			$licensed = true;

			$defaults = array(
				'showemail'	     => true,
				'showblog'	     => false,
				'showconditions' => ( ! empty( $this->app ) && in_array( $this->app, array_keys( $operators ) ) && $licensed ) ? true : false,
				'apps' 		     => array_keys( $operators ),
				'operators'      => ( ! empty( $this->app ) && in_array( $this->app, array_keys( $operators ) ) && $licensed ) ? wp_json_encode( $operators[ $this->app ] ) : wp_json_encode( [] ),
			);

			$tier_id = get_option( 'newsletterglue_pricing_id' );
			if ( $defaults['showconditions'] && in_array( $tier_id, array( 24, 3 ) ) ) {
				$defaults['showconditions'] = false;
			}
		}

		$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/js/';
		$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/css/';

		$suffix  = '';

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/group', array(
			'attributes'		=> array(
				'showblog'	=> array(
					'type'	=> 'boolean',
				),
				'showemail' => array(
					'type'	=> 'boolean'
				),
			),
			'editor_script' 	=> $this->asset_id,
			'editor_style'      => $this->asset_id . '-style',
			'render_callback'	=> array( $this, 'render_block' ),
		) );

	}

	/**
	 * Render the block.
	 */
	public function render_block( $attributes, $content ) {

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array(
				'showemail'	=> true,
				'showblog'	=> false,
			);
		}

		$show_in_blog  = isset( $attributes[ 'showblog' ] ) ? $attributes[ 'showblog' ] : $defaults[ 'showblog' ];
		$show_in_email = isset( $attributes[ 'showemail' ] ) ? $attributes[ 'showemail' ] : $defaults[ 'showemail' ];

		// Hidden from blog.
		if ( ! defined( 'NGL_IN_EMAIL' ) && ! $show_in_blog ) {
			$content = '';
		}

		// Hidden from email.
		if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
			$content = '';
		}

		if ( defined( 'NGL_IN_EMAIL' ) ) {
			$content = str_replace( '<section', '<div', $content );
			$content = str_replace( '/section>', '/div>', $content );

			$conditions = isset( $attributes[ $this->app . '_conditions' ] ) ? $attributes[ $this->app . '_conditions' ] : array();
			
			$conditions = array_filter($conditions, function( $item ) {
				$key = isset( $item[ 'key' ] ) ? $item[ 'key' ] : '';
				$key_manual = isset( $item[ 'key_manual' ] ) ? $item[ 'key_manual' ] : '';
				$operator = isset( $item[ 'operator' ] ) ? $item[ 'operator' ] : '';
				$value = isset( $item[ 'value' ] ) ? $item[ 'value' ] : '';
				$isPassed = false;
			
				if( ! empty( $key ) || ! empty( $key_manual ) ) {
					if( ! empty( $operator ) ) {
						if( $key == 'tag' && is_array( $value ) && count( $value ) ) {
							$isPassed = true;
						} else if( $key != 'tag' && ( $operator == 'ex' || $operator == 'nex' ) ) {
							$isPassed = true;
						} else if( ! empty( $value ) ) {
							$isPassed = true;
						}
					}
				}
			
				return $isPassed;
			});
			
			if( count( $conditions ) ) {
				$content = "<div data-conditions='" . wp_json_encode( $conditions ) . "'>$content</div>";
			}
		}

		return $content;

	}

	/**
	 * CSS.
	 */
	public function email_css() {

	}

	/**
	 * Refresh custom tags, custom fields.
	 */
	public function newsletterglue_block_show_hide_refresh() {
		$data   = array();
		
		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		// App Instance.
		if ( ! in_array( $this->app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
			wp_die( -1 );
		}

		include_once newsletterglue_get_path( $this->app ) . '/init.php';

		$classname 	= 'NGL_' . ucfirst( $this->app );
		$api		= new $classname();

		if ( method_exists( $api, 'get_custom_tags' ) ) {
			$response = $api->get_custom_tags();
			
			if( count( $response ) ) {
				set_transient( $this->app . '_custom_tags', $response, DAY_IN_SECONDS );
				$data[ 'custom_tag_list' ] = $response;
			}
		}

		
		if ( method_exists( $api, 'get_custom_fields' ) ) {
			$response = $api->get_custom_fields();

			if( count( $response ) ) {
				set_transient( $this->app . '_custom_fields', $response, DAY_IN_SECONDS );
				$data[ 'custom_field_list' ] = $response;
			}
		}

		// Return data.
		if ( count( $data ) ) {
			wp_send_json_success( $data );
		} else {
			wp_send_json( array(
				'success'	=> false,
				'message' 	=> __( 'We could not fetch data for you at this time. Try again later.', 'newsletter-glue' )
			) );
		}
	}

}

return new NGL_Block_Show_Hide_Content;
