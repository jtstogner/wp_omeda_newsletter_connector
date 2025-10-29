<?php
/**
 * Container block render.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Render_Container class.
 */
class NGL_Render_Container {

	public $app;

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_filter( 'render_block', array( $this, 'render_block_email' ), 50, 3 );

		$this->app = newsletterglue_default_connection();
	}

	/**
	 * Render.
	 */
	public function render_block_email( $block_content, $block ) {

		if ( ! defined( 'NGL_IN_EMAIL' ) )
			return $block_content;

		if ( "newsletterglue/container" !== $block['blockName'] )
			return $block_content;

		if ( isset( $block[ 'attrs' ][ 'show_in_email' ] ) ) {
			return null;
		}

		if ( defined( 'NGL_IN_EMAIL' ) ) {
			$attributes = $block['attrs'];
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
				$block_content = "<div class='ng-block ng-conditional' data-conditions='" . wp_json_encode( $conditions ) . "'>{$block_content}</div>";
			}
		}

		return $block_content;
	}

}

return new NGL_Render_Container;