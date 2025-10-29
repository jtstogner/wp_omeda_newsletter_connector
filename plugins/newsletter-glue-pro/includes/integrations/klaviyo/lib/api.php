<?php

use KlaviyoAPI\KlaviyoAPI;
use KlaviyoAPI\ApiException;

class NG_Klaviyo_API {
	private $api_key;

	private $klaviyo;

	public function __construct( $api_key ) {
		$this->api_key = $api_key;
		$this->klaviyo = new KlaviyoAPI( $api_key );
	}

	/**
	 * Get account.
	 */
	public function get_account() {
		try {
			$accounts = $this->klaviyo->Accounts->getAccounts();
		} catch(ApiException $ex) {
			return false;
		} catch(\InvalidArgumentException $ex) {
			return false;
		}

		return $accounts['data'][0];
	}

	/**
	 * Get lists.
	 */
	public function get_lists() {
		try {
			$lists = $this->klaviyo->Lists->getLists();
		} catch(ApiException $ex) {
			return false;
		} catch(\InvalidArgumentException $ex) {
			return false;
		}
	
		return $lists['data'];
	}

	/**
	 * Add a subscriber.
	 */
	public function add_subscriber( $list_id, $args = array() ) {
		$profile_attributes = array();
		$profile_attributes['email'] = $args['email'];
		if( ! empty( $args['name'] ) ) {
			$profile_attributes['first_name'] = $args['name'];
		}

		try {
			$profile = $this->klaviyo->Profiles->createOrUpdateProfile(
				array(
					'data' => array(
						'type' => 'profile',
						'attributes' => $profile_attributes
					)
				)
			);
	
			$profile_id = isset( $profile['data']['id'] ) ? $profile['data']['id'] : null;

			$this->klaviyo->Lists->createListRelationships( $list_id, array( 'data' => array( array( 'type' => 'profile', 'id' => $profile_id ) ) ) );
		} catch(ApiException $ex) {
			return false;
		} catch(\InvalidArgumentException $ex) {
			return false;
		}

		return true;
	}

	/**
	 * Create a template.
	 */
	public function create_template( $args = array() ) {
		try {
			$template = $this->klaviyo->Templates->createTemplate( $args );
		} catch(ApiException $ex) {
			return null;
		} catch(\InvalidArgumentException $ex) {
			return null;
		}

		return $template['data'];
	}

	/**
	 * Assign a template to campaign message.
	 */
	public function set_campaign_content( $args = array() ) {
		try {
			$content = $this->klaviyo->Campaigns->createCampaignMessageAssignTemplate( $args );
		} catch(ApiException $ex) {
			return null;
		} catch(\InvalidArgumentException $ex) {
			return null;
		}

		return $content;
	}

	/**
	 * Create a campaign.
	 */
	public function create_campaign( $args = array() ) {
		$campaign = $this->klaviyo->Campaigns->createCampaign( $args );

		if( isset( $campaign['errors'] ) ) {
			return null;
		}

		return $campaign['data'];
	}

	/**
	 * Send a campaign.
	 */
	public function send_campaign( $args = array() ) {
		try {
			$campaign = $this->klaviyo->Campaigns->createCampaignSendJob( $args );
		} catch(ApiException $ex) {
			return null;
		} catch(\InvalidArgumentException $ex) {
			return null;
		}

		return $campaign['data'];
	}

}