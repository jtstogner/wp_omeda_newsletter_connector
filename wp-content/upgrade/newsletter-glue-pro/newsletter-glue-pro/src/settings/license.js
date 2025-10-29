import { __ } from '@wordpress/i18n';
import React from 'react';

import apiFetch from '@wordpress/api-fetch';

import {
  Component
} from '@wordpress/element';

import {
  BaseControl,
  Button,
  ExternalLink,
  PanelBody,
  PanelRow,
} from '@wordpress/components';

import { iconArrowRight, iconCheck, iconIssue } from '../common/icons';

const iconUri = nglue_backend.images_uri + 'icon-license.svg';

export default class Connections extends Component {

  constructor(props) {

    super(props);

    this.attemptConnect = this.attemptConnect.bind(this);
    this.removeConnection = this.removeConnection.bind(this);

  }

  attemptConnect(retryCount = 0, maxRetries = 2) {

    var props = this.props;

    props.changeState({
      licenseConnecting: true,
      licenseStatus: 0,
      licenseTest: false,
    });

    var license_test = false;

    // Show retry message if this is a retry attempt
    if (retryCount > 0) {
      console.log(`Retrying license verification (attempt ${retryCount} of ${maxRetries})`);
    }

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/verify_license',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: this.props.getState
    }).then(d => {
      // Check if d and d.data exist before accessing properties
      if (d && d.data && d.data.success) {
        props.changeState({
          notLicensed: false,
          licenseConnecting: false,
          licenseStatus: 1,
          licenseShowConnect: true,
          licenseRenew: d.licenseRenew || '',
          licenseName: d.licenseName || '',
          tier: d.licenseTier || '',
        });

        setTimeout(() => {
          if (!license_test) {
            props.changeState({
              licenseTest: true,
              licenseShowConnect: false,
            });
          }
        }, 1500);
      } else if (d && d.message && d.message.includes('cURL error 28') && retryCount < maxRetries) {
        // If we got a timeout error and haven't exceeded max retries, try again with exponential backoff
        const backoffTime = Math.pow(2, retryCount) * 1000; // 1s, 2s, 4s, etc.
        console.log(`License verification timed out. Retrying in ${backoffTime/1000} seconds...`);
        
        setTimeout(() => {
          this.attemptConnect(retryCount + 1, maxRetries);
        }, backoffTime);
      } else {
        // Handle failed verification or null response
        console.log('License verification failed:', d ? d.message : 'Unknown error');
        props.changeState({
          notLicensed: true,
          licenseConnecting: false,
          licenseStatus: 2,
        });
      }
    }).catch(error => {
      // Handle connection errors or timeouts
      console.error('License verification error:', error);
      
      if (error.message && error.message.includes('timeout') && retryCount < maxRetries) {
        // If we got a timeout error and haven't exceeded max retries, try again with exponential backoff
        const backoffTime = Math.pow(2, retryCount) * 1000; // 1s, 2s, 4s, etc.
        console.log(`License verification timed out. Retrying in ${backoffTime/1000} seconds...`);
        
        setTimeout(() => {
          this.attemptConnect(retryCount + 1, maxRetries);
        }, backoffTime);
      } else {
        props.changeState({
          notLicensed: true,
          licenseConnecting: false,
          licenseStatus: 2,
        });
      }
    });

  }

  removeConnection() {

    var props = this.props;

    props.changeState({
      licenseConnecting: false,
      licenseStatus: 0,
      licenseTest: false,
      licenseKey: '',
      licenseConnected: false,
      licenseRenew: '',
      licenseName: '',
      tier: '',
    });

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/remove_license',
      method: 'get',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
    }).then(() => {
      props.changeState({ notLicensed: true });
    });

  }

  render() {

    var props = this.props;

    const { licenseKey, licenseConnected, licenseRenew, licenseConnecting, licenseShowConnect, licenseTest, licenseStatus, licenseName } = this.props.getState;

    const isDemo = nglue_backend.is_demo;

    var connectBtnText = '';
    if (licenseStatus === 1) {
      if (licenseShowConnect) {
        connectBtnText = __('Connected', 'newsletter-glue');
      } else {
        if (licenseTest || licenseConnected) {
          connectBtnText = __('Test', 'newsletter-glue');
        } else {
          connectBtnText = __('Connected', 'newsletter-glue');
        }
      }
    } else if (!licenseConnecting) {
      connectBtnText = __('Connect', 'newsletter-glue');
    } else {
      connectBtnText = __('Connecting...', 'newsletter-glue');
    }

    var connectBtnClass = 'is-primary';
    if (licenseStatus === 1) {
      if (licenseShowConnect) {
        connectBtnClass = 'nglue-btn-valid';
      } else {
        if (licenseTest || licenseConnected) {
          connectBtnClass = 'is-tertiary';
        } else {
          connectBtnClass = 'nglue-btn-valid';
        }
      }
    } else if (licenseConnecting) {
      connectBtnClass = 'nglue-btn-wait';
    }

    return (
      <>
        <div className="nglue-main">
          <PanelBody>
            <div className="nglue-title-bar">
              <div className="nglue-title">
                <span className="nglue-title-main">{__('Pro license', 'newsletter-glue')}</span>
                <span className="nglue-title-sub">{__('Connect your license key to receive plugin updates and support.', 'newsletter-glue')}</span>
              </div>
              <div className="nglue-title-icon"><img src={iconUri} /></div>
            </div>

            <div className="nglue-base-parent">
              <div className="nglue-base">
                <PanelRow className={licenseConnecting && "nglue-disabled-row"}>
                  <div>
                    {!licenseName && <div className="nglue-base-text nglue-text-b">{__('Connect license key to start', 'newsletter-glue')} {iconArrowRight}</div>}
                    {licenseName &&
                      <div className="nglue-base-text nglue-text-b">{licenseName}
                        {licenseConnecting && <span className="nglue-hc-state nglue-hc-state-wait">{__('Connecting...', 'newsletter-glue')}</span>}
                        {!licenseConnecting && licenseStatus == 1 && <span className="nglue-hc-state">{iconCheck} {__('Connected', 'newsletter-glue')}</span>}
                        {!licenseConnecting && licenseStatus == 2 && <span className="nglue-hc-state nglue-hc-state-failed">{iconIssue} {__('Failed', 'newsletter-glue')}</span>}
                      </div>
                    }
                    <div className="nglue-base-text">{__('Renews/Expires on:', 'newsletter-glue')} <span className="nglue-base-text-data">{licenseRenew}</span></div>
                    <div className="nglue-base-text"><ExternalLink href="https://newsletterglue.com/account">{__('Go to My Account', 'newsletter-glue')}</ExternalLink></div>
                  </div>
                </PanelRow>
              </div>

              <div className="nglue-base">
                <PanelRow>
                  <BaseControl
                    label={__('Enter your license key', 'newsletter-glue')}
                    id="nglue-license-key"
                    className={`nglue-esp-input`}
                  >
                    <input
                      type="password"
                      id="nglue-license-key"
                      value={licenseKey}
                      onChange={e => props.changeState({ licenseKey: e.target.value })}
                      disabled={licenseConnected || licenseConnecting}
                      autoComplete="new-password"
                    />
                    <div className="nglue-input-help">
                      <ExternalLink href="https://newsletterglue.com/account">{__('Get license key', 'newsletter-glue')}</ExternalLink>
                    </div>
                  </BaseControl>
                </PanelRow>

                <PanelRow className="nglue-buttons">
                  <Button
                    disabled={!licenseKey}
                    onClick={this.attemptConnect}
                    className={connectBtnClass}
                    style={{ margin: '0 10px 0 0' }}
                    icon={(licenseStatus === 1 && !licenseTest && !licenseConnected && iconCheck) || licenseShowConnect && iconCheck}
                  >
                    {connectBtnText}
                  </Button>

                  {licenseConnecting &&
                    <Button
                      isLink
                      onClick={() => props.changeState({ licenseConnecting: false })}
                    >{__('Cancel connecting', 'newsletter-glue')}</Button>}

                  {!isDemo && licenseStatus === 1 &&
                    <Button
                      isLink
                      onClick={() => {
                        this.removeConnection();
                      }}
                    >{__('Deactivate license', 'newsletter-glue')}</Button>}

                  {!isDemo && licenseStatus == 2 && <div className="nglue-form-err">{__('Please make sure your enter a valid license key.', 'newsletter-glue')}</div>}

                </PanelRow>
              </div>
            </div>

          </PanelBody>
        </div>
      </>
    );

  }

}