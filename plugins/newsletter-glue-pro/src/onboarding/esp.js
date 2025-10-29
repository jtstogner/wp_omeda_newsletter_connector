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
  Component
} from '@wordpress/element';

var espList = nglue_backend.esp_list;

import Loader from '../common/loader';

export default class ESPConnection extends Component {

  constructor(props) {

    super(props);

    this.state = {
      isAPILoaded: false,
      isVerifying: false,
      key_status: '',
      esp: '',
      esp_title: '',
      esp_index: 0,
      api_key: '',
      api_url: '',
      api_secret: '',
    }

    this.verifyConnection = this.verifyConnection.bind(this);
  }

  componentDidMount() {

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/get_api',
      method: 'get',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      }
    }).then(response => {

      if (response.esp_list) {
        espList = response.esp_list;
      }

      var index = 0;
      if (response.service) {
        espList.map((d, i) => {
          if (d.value === response.service) {
            index = i;
          }
        });
        this.setState({ isAPILoaded: true, esp: response.service, api_key: response.api_key, api_url: response.api_url, api_secret: response.api_secret, esp_title: response.esp_title, esp_index: index });
      } else {
        this.setState({ isAPILoaded: true });
      }

    });

  }

  verifyConnection() {

    var props = this.props;

    this.setState({ isVerifying: true });

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/verify_api',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: {
        api_key: this.state.api_key,
        api_url: this.state.api_url,
        api_secret: this.state.api_secret,
        esp: this.state.esp
      }
    }).then(response => {
      if (response.success) {
        this.setState({ isVerifying: false, key_status: 'valid' });
        setTimeout(() => {
          props.changeState({ step: 3, completedStep: 2 });
        }, 1500);
      } else {
        this.setState({ isVerifying: false, key_status: 'invalid' });
      }
    });

  }

  render() {

    if (!this.state.isAPILoaded || !espList) {
      return <Loader isAlt />;
    }

    const { esp, api_key, api_url, api_secret, esp_title, esp_index, key_status, isVerifying } = this.state;

    const espItems = espList.map((d, i) => <PanelRow key={["esp-", d.value]}>
      <Button
        isSecondary
        isPressed={esp == d.value ? true : false}
        disabled={key_status == 'valid' || isVerifying}
        className={['nglue-button-toggle', d.requires && d.requires != '' ? d.requires : '']}
        icon={<span className={"nglue-esp-icon nglue-esp-" + d.value} style={{ backgroundColor: d.bg }}><img src={nglue_backend.esp_icons[d.value]} /></span>}
        onClick={() => {
          if (d.requires && d.requires != '') {
            window.open(nglue_backend.newsroom_upg, "_blank");
            return false;
          }
          if (d.value != esp) {
            this.setState({ esp_index: i, esp: d.value, esp_title: d.label, key_status: '', api_key: '', api_url: '', api_secret: '' });
          } else {
            this.setState({ esp_index: i, esp: d.value, esp_title: d.label });
          }
        }}
      >
        {d.label}
        {d.requires && d.requires != '' && <span className="nglue-lock-icon"><svg stroke="currentColor" fill="currentColor" strokeWidth="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M368 192h-16v-80a96 96 0 10-192 0v80h-16a64.07 64.07 0 00-64 64v176a64.07 64.07 0 0064 64h224a64.07 64.07 0 0064-64V256a64.07 64.07 0 00-64-64zm-48 0H192v-80a64 64 0 11128 0z"></path></svg></span>}
        {d.requires && d.requires != '' && <span className="nglue-feature-lock"><span className="nglue-feature-title">Head to account:</span><span className="nglue-feature-action">Upgrade to unlock</span></span>}
      </Button>
    </PanelRow>);

    const checkIcon = <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z" /><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-.997-6l7.07-7.071-1.414-1.414-5.656 5.657-2.829-2.829-1.414 1.414L11.003 16z" /></svg>;

    var apiSecretLabel = esp && esp === 'campaignmonitor' ? 'Client ID' : 'API secret';

    return (
      <PanelBody>

        <PanelRow>
          <h2>{__('Connect your email service provider', 'newsletter-glue')}</h2>
        </PanelRow>

        <div className="ngl-wizard-flex ngl-wizard-flex-compact">
          {espItems}
        </div>

        {esp != '' && (espList[esp_index].requires == undefined || espList[esp_index].requires == '') &&
          <>

            <PanelRow>
              <BaseControl
                label={espList[esp_index].key_name || sprintf(__('%s API key', 'newsletter-glue'), esp_title)}
                id="nglue-api-key"
                help={espList[esp_index].help && <ExternalLink href={espList[esp_index].help}>{__('Get API key', 'newsletter-glue')}</ExternalLink>}
                className={key_status == 'invalid' ? 'nglue-input-error' : ''}
              >
                <TextControl
                  id="nglue-api-key"
                  type="password"
                  value={api_key}
                  disabled={key_status == 'valid' || isVerifying}
                  onChange={
                    (value) => {
                      this.setState({ api_key: value })
                    }
                  }
                />
                {key_status == 'invalid' && <div className="extra-help">{__('API connection failed. Your API key could be invalid.', 'newsletter-glue')}</div>}
              </BaseControl>
            </PanelRow>

            {espList[esp_index].extra_setting && (espList[esp_index].extra_setting == 'secret' || espList[esp_index].extra_setting == 'both') &&
              <PanelRow>
                <BaseControl
                  label={espList[esp_index].secret_name || sprintf(__(`%s ${apiSecretLabel}`, 'newsletter-glue'), esp_title)}
                  id="nglue-api-secret"
                >
                  <TextControl
                    id="nglue-api-secret"
                    type="password"
                    value={api_secret}
                    disabled={key_status == 'valid' || isVerifying}
                    onChange={
                      (value) => {
                        this.setState({ api_secret: value })
                      }
                    }
                  />
                </BaseControl>
              </PanelRow>
            }

            {espList[esp_index].extra_setting && (espList[esp_index].extra_setting == 'url' || espList[esp_index].extra_setting == 'both') &&
              <PanelRow>
                <BaseControl
                  label={espList[esp_index].url_name || sprintf(__('%s API URL', 'newsletter-glue'), esp_title)}
                  id="nglue-api-url"
                >
                  <TextControl
                    id="nglue-api-url"
                    value={api_url}
                    disabled={key_status == 'valid' || isVerifying}
                    onChange={
                      (value) => {
                        this.setState({ api_url: value })
                      }
                    }
                  />
                  {espList[esp_index].url_help &&
                    <div className="extra-help">{espList[esp_index].url_help}</div>
                  }
                </BaseControl>
              </PanelRow>
            }

            <PanelRow>
              <Button
                isPrimary
                disabled={isVerifying || !api_key ? true : false}
                isBusy={isVerifying}
                onClick={this.verifyConnection}
                className={key_status == 'valid' ? 'nglue-valid-button' : ''}
                icon={key_status == 'valid' ? checkIcon : null}
              >
                {!isVerifying && key_status != 'valid' && __('Connect', 'newsletter-glue')}
                {isVerifying && sprintf(__('Connecting to %s API...', 'newsletter-glue'), esp_title)}
                {key_status == 'valid' && __('API key connected', 'newsletter-glue')}
              </Button>
            </PanelRow>
          </>
        }
      </PanelBody>
    );
  }

}