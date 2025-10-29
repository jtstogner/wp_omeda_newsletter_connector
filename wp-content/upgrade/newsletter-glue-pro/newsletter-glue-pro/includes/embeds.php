<?php
/**
 * Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get reddit content.
 */
function newsletterglue_get_reddit_content( $url ) {
	$url = urlencode( untrailingslashit( trim( $url ) ) );

	$request  = wp_remote_get( 'https://www.reddit.com/oembed?url=' . $url ); // phpcs:ignore
	$response = wp_remote_retrieve_body( $request );

	$data = json_decode( $response );

	if ( empty( $data ) ) {
		return false;
	}

	$content    = (string) trim( $data->html );
	$content    = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $content );
	$clean_html = strip_tags( $content, '<a><span><strong>' );

	$x = strrpos( $content, 'by' );

	$split = array( substr( $content, 0, $x ), substr( $content, $x + 2 ) );

	if ( isset( $split[1] ) ) {
		$from = trim( str_replace( 'from', '', $split[1] ) );
		$from = '<a href="https://reddit.com/r/' . wp_strip_all_tags( $from ) . '" target="_blank">/r/' . wp_strip_all_tags( $from ) . '</a>';
	}

	$html = trim( $split[0] );

	ob_start();

	include_once NGL_PLUGIN_DIR . 'includes/templates/reddit/view.php';

	return ob_get_clean();
}

/**
 * Get soundcloud content.
 */
function newsletterglue_get_soundcloud_content( $url ) {
	$url = urlencode( untrailingslashit( trim( $url ) ) );

	$request  = wp_remote_get( 'https://soundcloud.com/oembed?format=json&url=' . $url ); // phpcs:ignore
	$response = wp_remote_retrieve_body( $request );

	$data = json_decode( $response );

	if ( empty( $data ) ) {
		return false;
	}

	ob_start();

	include_once NGL_PLUGIN_DIR . 'includes/templates/soundcloud/view.php';

	return ob_get_clean();
}

/**
 * Get spotify content.
 */
function newsletterglue_get_spotify_content( $url ) {

	$url = urlencode( untrailingslashit( trim( $url ) ) );

	$request  = wp_remote_get( 'https://open.spotify.com/oembed?url=' . $url ); // phpcs:ignore
	$response = wp_remote_retrieve_body( $request );

	$data = json_decode( $response );

	if ( empty( $data ) ) {
		return false;
	}

	ob_start();

	include_once NGL_PLUGIN_DIR . 'includes/templates/spotify/view.php';

	return ob_get_clean();
}

/**
 * Get youtube content.
 */
function newsletterglue_get_youtube_content( $url ) {
	$url = urlencode( untrailingslashit( trim( $url ) ) );

	$request  = wp_remote_get( 'https://www.youtube.com/oembed?url=' . $url ); // phpcs:ignore
	$response = wp_remote_retrieve_body( $request );

	$data = json_decode( $response );

	if ( empty( $data ) ) {
		return false;
	}

	$image_url = str_replace( 'hqdefault', 'maxresdefault', $data->thumbnail_url );

	$url = esc_url( urldecode( trim( $url ) ) );

	ob_start();

	include_once NGL_PLUGIN_DIR . 'includes/templates/youtube/view.php';

	return ob_get_clean();
}

/**
 * Get twitter content.
 */
function newsletterglue_get_twitter_content( $url ) {
	$url = urlencode( untrailingslashit( trim( $url ) ) );

	$request  = wp_remote_get( 'https://publish.twitter.com/oembed?omit_script=true&url=' . $url ); // phpcs:ignore
	$response = wp_remote_retrieve_body( $request );

	$data = json_decode( $response );

	if ( empty( $data->html ) ) {
		return false;
	}

	$html = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', (string) trim( $data->html ) );
	$html = str_replace( 'blockquote', 'div', trim( $html ) );

	$stripped = preg_replace( '/<p\b[^>]*>(.*?)<\/p>/i', '', $html );
	preg_match( '#<a(.*?)</a>#i', $stripped, $match );
	$date           = wp_strip_all_tags( $match[0] );
	$formatted_date = '<a href="' . urldecode( trim( $url ) ) . '" target="_blank" rel="noopener">' . date_i18n( 'M j, Y', strtotime( $date ) ) . '</a>';

	preg_match( '%(<p[^>]*>.*?</p>)%i', $html, $regs );
	$html = $regs[0];

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$replace = 'p';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	$output->save();
	$html = (string) $output;

	if ( preg_match( '/^https?:\/\/(www\.)?twitter\.com\/(#!\/)?(?<name>[^\/]+)(\/\w+)*$/', $data->author_url, $regs ) ) {
		$username = '<a href="' . $data->author_url . '" target="_blank" rel="noopener">@' . $regs['name'] . '</a>';
	} else {
		$username = '<a href="' . $data->author_url . '" target="_blank" rel="noopener">' . $data->author_url . '</a>';
	}

	ob_start();

	include_once NGL_PLUGIN_DIR . 'includes/templates/twitter/view.php';

	return ob_get_clean();
}

/**
 * Get X content.
 */
function newsletterglue_get_x_content( $url ) {
	$url = urlencode( untrailingslashit( trim( $url ) ) );

	$request  = wp_remote_get( 'https://publish.x.com/oembed?omit_script=true&url=' . $url ); // phpcs:ignore
	$response = wp_remote_retrieve_body( $request );

	$data = json_decode( $response );

	if ( empty( $data->html ) ) {
		return false;
	}

	$html = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', (string) trim( $data->html ) );
	$html = str_replace( 'blockquote', 'div', trim( $html ) );

	$stripped = preg_replace( '/<p\b[^>]*>(.*?)<\/p>/i', '', $html );
	preg_match( '#<a(.*?)</a>#i', $stripped, $match );
	$date           = wp_strip_all_tags( $match[0] );
	$formatted_date = '<a href="' . urldecode( trim( $url ) ) . '" target="_blank" rel="noopener">' . date_i18n( 'M j, Y', strtotime( $date ) ) . '</a>';

	preg_match( '%(<p[^>]*>.*?</p>)%i', $html, $regs );
	$html = $regs[0];

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$replace = 'p';
	foreach ( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	$output->save();
	$html = (string) $output;

	if ( preg_match( '/^https?:\/\/(www\.)?twitter\.com\/(#!\/)?(?<name>[^\/]+)(\/\w+)*$/', $data->author_url, $regs ) ) {
		$username = '<a href="' . $data->author_url . '" target="_blank" rel="noopener">@' . $regs['name'] . '</a>';
	} else {
		$username = '<a href="' . $data->author_url . '" target="_blank" rel="noopener">' . $data->author_url . '</a>';
	}

	ob_start();

	include_once NGL_PLUGIN_DIR . 'includes/templates/x/view.php';

	return ob_get_clean();
}
