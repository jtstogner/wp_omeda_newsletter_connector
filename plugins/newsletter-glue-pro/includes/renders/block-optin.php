<?php
/**
 * Form block render.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Render_Optin class.
 */
class NGL_Render_Optin {

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

		if ( ! defined( 'NGL_IN_EMAIL' ) ) {
			return $block_content;
        }

		if ( "newsletterglue/optin" !== $block['blockName'] ) {
			return $block_content;
        }

		if ( defined( 'NGL_IN_EMAIL' ) ) {

            $html = $block_content;

            $output = new simple_html_dom();
            $output->load( $html, true, false );

            $replace = '.ngl-form-field, .ngl-form-checkbox, .ng-form-overlay, .ngl-form-errors, .ng-form-inputs';
            foreach( $output->find( $replace ) as $key => $element ) {
                $element->outertext = '';
            }

            $replace = '.ng-form-button';
            foreach( $output->find( $replace ) as $key => $element ) {
                $element->style = rtrim( $element->style, ';' ) . ';display: block;text-decoration: none !important;';
            }

            $output->save();

            $block_content = (string) $output;

            $block_content = str_replace( '<form', '<div', $block_content );
            $block_content = str_replace( '</form>', '</div>', $block_content );
            $block_content = str_replace( '<button', '<a href="{{ blog_post }}"', $block_content );
            $block_content = str_replace( '</button>', '</a>', $block_content );
		}

		return $block_content;
	}

}

return new NGL_Render_Optin;