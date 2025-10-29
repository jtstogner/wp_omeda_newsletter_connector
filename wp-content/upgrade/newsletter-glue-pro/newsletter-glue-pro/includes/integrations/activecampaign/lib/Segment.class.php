<?php

class AC_Segment extends ActiveCampaign {

	public $version;
	public $url_base;
	public $url;
	public $api_key;

	function __construct($version, $url_base, $url, $api_key) {
		$this->version = $version;
		$this->url_base = $url_base;
		$this->url = $url;
		$this->api_key = $api_key;
	}

	function list_($params, $postdata = array() ) {

		// version 2 only
		if ( ! empty( $postdata[ 'list_id' ] ) ) {
			$args = array(
				'filters' => array(
					'list_id' => $postdata[ 'list_id' ],
				),
			);
		} else if ( ! empty( $postdata[ 'page' ] ) ) {
			$args = array(
				'page'	=> absint( $postdata[ 'page' ] ),
			);
		} else {
			$args = array();
		}

		$args = http_build_query($args);

		$request_url = "{$this->url_base}/api/2/segment/list?api_key={$this->api_key}&{$args}";
		$response = $this->curl($request_url, '', "GET", "segment_list");
		return json_decode( $response );
	}

}
