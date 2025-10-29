<?php
/**
 * Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get Merge Tags.
 */
function newsletterglue_get_convertkit_tags() {

	$merge_tags = array(
		'personalization'	=> array(
			'title'		=> __( 'Personalization', 'newsletter-glue' ),
			'tags'	=> array(

			),
		),
		'read_online'		=> array(
			'title'			=> __( 'Read online', 'newsletter-glue' ),
			'tags'			=> array(

			),
		),
		'footer'			=> array(
			'title'			=> __( 'Footer', 'newsletter-glue' ),
			'tags'			=> array(

			),
		),
	);

	return apply_filters( 'newsletterglue_get_convertkit_tags', $merge_tags );
}