<?php
/**
 * Rewrite Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allow pagination in newsletter category archive.
 */
function newsletterglue_generate_taxonomy_rewrite_rules( $wp_rewrite ) {

	$rules = array();

	$post_type_name = 'newsletterglue';
	$terms          = get_categories(
		array(
			'type'       => $post_type_name,
			'taxonomy'   => 'ngl_newsletter_cat',
			'hide_empty' => 0,
		)
	);

	foreach ( $terms as $term ) {
		$rules[ get_option( 'newsletterglue_post_type_ep', 'newsletter' ) . '/' . $term->slug . '/page/?([0-9]{1,})/?$' ] = 'index.php?' . 'type=' . $post_type_name . '&' . $term->taxonomy . '=' . $term->slug . '&paged=' . $wp_rewrite->preg_index( 1 );
	}

	$wp_rewrite->rules = $rules + $wp_rewrite->rules;
}
add_action( 'generate_rewrite_rules', 'newsletterglue_generate_taxonomy_rewrite_rules', 100 );

/**
 * Generates custom link for each newsletter.
 */
function newsletterglue_generate_newsletter_post_link( $post_link, $id = 0 ) {
	$post = get_post( $id );
	if ( is_object( $post ) ) {
		$terms = wp_get_object_terms( $post->ID, 'ngl_newsletter_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			return str_replace( '%newsletter%', $terms[0]->slug, $post_link );
		} else {

			$default = get_option( 'newsletterglue_default_tax_id' );
			// WP error, fail.
			if ( is_wp_error( $default ) ) {
				delete_option( 'newsletterglue_default_tax_id' );
			} elseif ( isset( $default['term_id'] ) ) {
				$the_term = get_term_by( 'id', $default['term_id'], 'ngl_newsletter_cat' );
				if ( ! empty( $the_term ) && ! empty( $the_term->slug ) ) {
					wp_set_object_terms( $post->ID, array( $the_term->slug ), 'ngl_newsletter_cat' );
					return str_replace( '%newsletter%', $the_term->slug, $post_link );
				}
			}
		}
	}
	return $post_link;
}
add_filter( 'post_type_link', 'newsletterglue_generate_newsletter_post_link', 1, 3 );

/**
 * Rewrite archive link for a newsletter.
 */
function newsletterglue_archive_rewrite_rules() {

	add_rewrite_rule(
		'^' . get_option( 'newsletterglue_post_type_ep', 'newsletter' ) . '/(.*)/(.*)/?$',
		'index.php?post_type=newsletterglue&name=$matches[2]',
		'top'
	);
}
add_action( 'init', 'newsletterglue_archive_rewrite_rules' );

/**
 * Flush rewrite rules.
 */
function newsletterglue_flush_rewrite_rules() {

	if ( ! get_option( 'newsletterglue_flushed_rewrite' ) ) {
		flush_rewrite_rules(); // phpcs:ignore
		update_option( 'newsletterglue_flushed_rewrite', 'yes' );
	}
}
add_action( 'admin_init', 'newsletterglue_flush_rewrite_rules', 99999 );
