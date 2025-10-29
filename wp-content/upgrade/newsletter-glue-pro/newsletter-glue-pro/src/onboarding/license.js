import React from 'react';

import { __, sprintf } from '@wordpress/i18n';

import apiFetch from '@wordpress/api-fetch';

import {
  BaseControl,
  Button,
  ExternalLink,
  PanelBody,
  PanelRow,
  TextControl
} from '@wordpress/components';

import {
  Component,
} from '@wordpress/element';

import Loader from '../common/loader';

export default class OnboadingLicense extends Component {

  constructor(props) {

    super(props);

    this.state = {
      isAPILoaded: false,
      isVerifying: false,
      license_status: '',
      message: '',
      license_key: '',
    }

    this.verifyLicense = this.verifyLicense.bind(this);
  }

  componentDidMount() {

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/get_license_key',
      method: 'get',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      }
    }).then(response => {
      this.setState({ isAPILoaded: true, license_key: response.license_key });
    });

  }

  verifyLicense() {

    var props = this.props;

    this.setState({ isVerifying: true });

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/verify_license_key',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: {
        license_key: this.state.license_key
      }
    }).then(response => {
      this.setState({ isVerifying: false, license_status: response.status, message: response.message });

      if (response.status == 'valid') {
        setTimeout(() => {
          props.changeState({ step: 2, completedStep: 1 });
        }, 1500);
      }
    });

  }

  render() {

    if (!this.state.isAPILoaded) {
      return <Loader isAlt />;
    }

    const firstName = nglue_backend.first_name;
    const isDemo = nglue_backend.is_demo;

    const { license_key, isVerifying, message, license_status } = this.state;

    const checkIcon = <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z" /><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-.997-6l7.07-7.071-1.414-1.414-5.656 5.657-2.829-2.829-1.414 1.414L11.003 16z" /></svg>;

    return (
      <PanelBody>

        <PanelRow>
          <h4>{sprintf(__('Welcome, %s!', 'newsletter-glue'), firstName)}</h4>
          <h2>{__('Connect your Newsletter Glue account', 'newsletter-glue')}</h2>
          {!isDemo && <p>{sprintf(__('Get your license key from your purchase confirmation email or', 'newsletter-glue'))} <ExternalLink href="https://newsletterglue.com/account">{__('account dashboard', 'newsletter-glue')}</ExternalLink></p>}
          {isDemo && <p>{__("Since this is a test site, we’ve filled out your license key for you. Just click 'Activate license' to start!", 'newsletter-glue')}</p>}
        </PanelRow>

        <PanelRow>
          <BaseControl
            label={__('Your plugin license key', 'newsletter-glue')}
            id="nglue-license-key"
            className={license_status && license_status != 'valid' ? 'nglue-input-error' : ''}
          >
            <TextControl
              id="nglue-license-key"
              type="password"
              value={license_key}
              placeholder="eg. be6f7eb68d7deb1685a49021ed416635"
              disabled={license_status == 'valid' || isVerifying || isDemo}
              onChange={
                (value) => {
                  this.setState({ license_key: value })
                }
              }
            />
            {message && <div className="extra-help">{message}</div>}
          </BaseControl>
        </PanelRow>

        <PanelRow>
          <Button
            isPrimary
            disabled={isVerifying || !license_key ? true : false}
            isBusy={isVerifying}
            onClick={this.verifyLicense}
            className={license_status == 'valid' ? 'nglue-valid-button' : ''}
            icon={license_status == 'valid' ? checkIcon : null}
          >
            {!isVerifying && license_status != 'valid' && __('Activate license', 'newsletter-glue')}
            {isVerifying && __('Verifying your license key...', 'newsletter-glue')}
            {license_status == 'valid' && __('License key validated', 'newsletter-glue')}
          </Button>
        </PanelRow>

      </PanelBody>
    );
  }

}