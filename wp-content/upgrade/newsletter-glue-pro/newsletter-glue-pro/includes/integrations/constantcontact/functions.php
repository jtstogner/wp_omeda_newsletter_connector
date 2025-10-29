<?php
/**
 * Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get Merge Tags.
 */
function newsletterglue_get_constantcontact_tags() {

	$merge_tags = array(
		'personalization'	=> array(
			'title'		=> __( 'Personalization', 'newsletter-glue' ),
			'tags'	=> array(
				'first_name' => array(
                    'title' => __( 'First Name', 'newsletter-glue' ),
                ),
                'last_name' => array(
                    'title' => __( 'Last Name', 'newsletter-glue' ),
                ),
                'company' => array(
                    'title' => __( 'Company', 'newsletter-glue' ),
                ),
                'job_title' => array(
                    'title' => __( 'Job Title', 'newsletter-glue' ),
                ),
                'address_line_1' => array(
                    'title' => __( 'Address Line 1', 'newsletter-glue' ),
                ),
                'address_line_2' => array(
                    'title' => __( 'Address Line 2', 'newsletter-glue' ),
                ),
                'address_line_3' => array(
                    'title' => __( 'Address Line 3', 'newsletter-glue' ),
                ),
                'city' => array(
                    'title' => __( 'City', 'newsletter-glue' ),
                ),
                'state_name' => array(
                    'title' => __( 'State Name', 'newsletter-glue' ),
                ),
                'postal_code' => array(
                    'title' => __( 'Postal Code', 'newsletter-glue' ),
                ),
                'home_phone' => array(
                    'title' => __( 'Home Phone', 'newsletter-glue' ),
                ),
                'work_phone' => array(
                    'title' => __( 'Work Phone', 'newsletter-glue' ),
                ),
                'birthday' => array(
                    'title' => __( 'Birthday', 'newsletter-glue' ),
                ),
                'anniversary' => array(
                    'title' => __( 'Anniversary', 'newsletter-glue' ),
                ),
                'email_address' => array(
                    'title' => __( 'Email Address', 'newsletter-glue' ),
                ),
                'organization_name' => array(
                    'title' => __( 'Organization Name', 'newsletter-glue' ),
                ),
                'organization_website_address' => array(
                    'title' => __( 'Website Address', 'newsletter-glue' ),
                ),
                'organization_logo_url' => array(
                    'title' => __( 'Logo URL', 'newsletter-glue' ),
                ),
                'organization_address_line_1' => array(
                    'title' => __( 'Address Line 1', 'newsletter-glue' ),
                ),
                'organization_address_line_2' => array(
                    'title' => __( 'Address Line 2', 'newsletter-glue' ),
                ),
                'organization_address_line_3' => array(
                    'title' => __( 'Address Line 3', 'newsletter-glue' ),
                ),
                'organization_city' => array(
                    'title' => __( 'City', 'newsletter-glue' ),
                ),
                'organization_state' => array(
                    'title' => __( 'State Name', 'newsletter-glue' ),
                ),
                'organization_us_state' => array(
                    'title' => __( 'Two-letter State', 'newsletter-glue' ),
                ),
                'organization_country' => array(
                    'title' => __( 'Country', 'newsletter-glue' ),
                ),
                'organization_country_code' => array(
                    'title' => __( 'Country Code', 'newsletter-glue' ),
                ),
                'organization_postal_code' => array(
                    'title' => __( 'Postal Code', 'newsletter-glue' ),
                ),
                'organization_signature_name' => array(
                    'title' => __( 'Signature Name', 'newsletter-glue' ),
                ),
                'organization_signature_email' => array(
                    'title' => __( 'Signature Email', 'newsletter-glue' ),
                ),
                'organization_signature_image_url' => array(
                    'title' => __( 'Signature Image URL', 'newsletter-glue' ),
                ),
			),
		),
		'read_online'		=> array(
			'title'			=> __( 'Read online', 'newsletter-glue' ),
			'tags'			=> array(
				'blog_post' => array(
					'title'		=> __( 'Blog post', 'newsletter-glue' ),
					'default_link_text'	=> __( 'Read online', 'newsletter-glue' ),
				),
				'webversion' => array(
					'title'		=> __( 'Email HTML', 'newsletter-glue' ),
					'default_link_text'	=> __( 'Read online', 'newsletter-glue' ),
				),
				'webpage_link' => array(
					'title'		=> __( 'Webpage link', 'newsletter-glue' ),
					'default_link_text'	=> __( 'Read online', 'newsletter-glue' ),
				),
			),
		),
		'footer'			=> array(
			'title'			=> __( 'Footer', 'newsletter-glue' ),
			'tags'			=> array(
				'admin_address' => array(
					'title'	=> __( 'Admin address', 'newsletter-glue' ),
					'require_fallback' => 'yes',
				),
				'unsubscribe_link' => array(
					'title'	=> __( 'Unsubscribe link', 'newsletter-glue' ),
					'default_link_text'	=> __( 'Unsubscribe', 'newsletter-glue' ),
					'helper' => __( 'Your subscribers click this text to unsubscribe.', 'newsletter-glue' ),
				),
			),
		),
	);

	return apply_filters( 'newsletterglue_get_constantcontact_tags', $merge_tags );
}
