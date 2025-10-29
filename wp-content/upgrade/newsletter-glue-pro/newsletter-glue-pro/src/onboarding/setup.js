import React from 'react';

import { __ } from '@wordpress/i18n';

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

import Select from 'react-select';

import Loader from '../common/loader';

export default class ESPSetup extends Component {

  constructor(props) {

    super(props);

    this.state = {
      isAPILoaded: false,
      isValidated: false,
      name: '',
      email: '',
      email_help: '',
      verifyingEmail: false,
      emailVerified: false,
      emailUnverified: false,
      disableSelect: '',
      updatingSegments: false,
    }

    this.updateOptions = this.updateOptions.bind(this);
    this.verifyEmail = this.verifyEmail.bind(this);
    this.updateItems = this.updateItems.bind(this);
  }

  componentDidMount() {

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/get_esp_options',
      method: 'get',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      }
    }).then(response => {

      const newstate = {
        isAPILoaded: true,
        isValidated: Boolean(response.is_validated),
      };

      for (var key in response) {
        newstate[key] = response[key];
      };

      this.setState(newstate);

    });

  }

  updateOptions() {

    var props = this.props;

    const data = {};
    for (var key in this.state) {
      data[key] = this.state[key];
    }

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/update_esp_options',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

      props.changeState({ step: 4, completedStep: 3 });

    });

  }

  handleChange(name, event) {
    var _newoptions = this.state.options;
    _newoptions[name]['value'] = event.target.value;
    this.setState({ options: _newoptions });
  }

  verifyEmail(value) {

    let unverified = false;
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if (!value) {
      unverified = true;
    }

    if (!re.test(value)) {
      unverified = true;
    }

    if (unverified) {
      this.setState({ emailVerified: false, emailUnverified: true, verifyingEmail: false, isValidated: false });
    } else {

      apiFetch({
        path: 'newsletterglue/' + nglue_backend.api_version + '/verify_email',
        method: 'post',
        headers: {
          'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
        },
        data: {
          esp: this.state.esp,
          email: value
        }
      }).then(response => {

        if (response.failed) {
          this.setState({ emailVerified: false, emailUnverified: true, verifyingEmail: false, isValidated: false });
        } else {
          this.setState({ emailVerified: true, emailUnverified: false, verifyingEmail: false, isValidated: true });
        }

      });

    }

  }

  updateItems(child, selected) {
    this.setState({ updatingSegments: true, disableSelect: child, isValidated: false });

    let callback = this.state.options[child]['callback'];

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/get_esp_items?esp=' + this.state.esp + '&parameter=' + selected.value + '&callback=' + callback,
      method: 'get',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
    }).then(response => {

      let new_state = this.state;

      new_state.options[child]['items'] = response;
      new_state.options[child]['value'] = '';
      new_state.options[child]['default'] = '';
      new_state.updatingSegments = false;
      new_state.disableSelect = '';
      new_state.isValidated = true;

      this.setState(new_state);

    });

  }

  render() {

    if (!this.state.isAPILoaded) {
      return <Loader isAlt />;
    }

    const { name, email, email_help, isValidated, options, verifyingEmail, emailVerified, emailUnverified, disableSelect, updatingSegments } = this.state;

    var viewOptions = [];

    for (var option in options) {

      var item = options[option];

      viewOptions.push(
        <PanelRow key={`nglue-option-${option}`}>
          <BaseControl
            label={item.title}
            id={option}
            className={`nglue-field-${option}`}
          >
            {item.type == 'select' &&
              <Select
                name={option}
                inputId={option}
                isMulti={item.is_multi}
                classNamePrefix="nglue"
                options={item.items}
                value={item.value || item.is_multi ? item.value : item.default}
                isDisabled={disableSelect === option && updatingSegments}
                isLoading={disableSelect === option && updatingSegments}
                placeholder={item.placeholder}
                onChange={
                  (selected, action) => {
                    var _newoptions = this.state.options;

                    if (selected === _newoptions[action.name]['value']) {
                      return;
                    }

                    _newoptions[action.name]['value'] = selected;
                    this.setState({ options: _newoptions });
                    if (_newoptions[action.name]['onchange']) {
                      this.updateItems(_newoptions[action.name]['onchange'], selected);
                    }
                  }
                }
              />}
            {item.type == 'text' &&
              <input
                type="text"
                id={option}
                name={option}
                className="components-text-control__input"
                value={item.value ? item.value : item.default}
                onChange={this.handleChange.bind(this, option)}
              />
            }
            {item.help && <div className="extra-help b" dangerouslySetInnerHTML={{ __html: item.help }}></div>}
          </BaseControl>
        </PanelRow>
      );
    };

    return (
      <PanelBody>

        <PanelRow>
          <h2>{__('Set your default newsletter settings', 'newsletter-glue')}</h2>
          <p>{__('You can easily set different newsletter settings when publishing a newsletter or change your newsletter defaults in the Settings.', 'newsletter-glue')}</p>
        </PanelRow>

        {viewOptions}

        <PanelRow>
          <BaseControl
            label={__('From name', 'newsletter-glue')}
            id="nglue-esp-name"
            help={__('Your subscribers will see this name in their inboxes.', 'newsletter-glue')}
          >
            <TextControl
              id="nglue-esp-name"
              value={name}
              onChange={
                (value) => {
                  this.setState({ name: value })
                }
              }
            />
          </BaseControl>
        </PanelRow>

        <PanelRow>
          <BaseControl
            label={__('From email', 'newsletter-glue')}
            id="nglue-esp-email"
            className={emailUnverified ? 'nglue-has-loading-indicator nglue-input-error' : 'nglue-has-loading-indicator'}
          >
            <TextControl
              id="nglue-esp-email"
              value={email}
              onChange={
                (value) => {
                  this.setState({ email: value, isValidated: false, verifyingEmail: true });
                  this.verifyEmail(value);
                }
              }
            />
            {verifyingEmail &&
              <div className="nglue__indicator nglue__loading-indicator">
                <span></span>
                <span></span>
                <span></span>
              </div>
            }
            {emailVerified && !verifyingEmail && <div className="nglue__indicator nglue__loading-indicator"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" d="M0 0h24v24H0z" /><path fill="#2FCC71" d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-.997-6l7.07-7.071-1.414-1.414-5.656 5.657-2.829-2.829-1.414 1.414L11.003 16z" /></svg></div>}
            {emailUnverified && !verifyingEmail && <div className="nglue__indicator nglue__loading-indicator"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" d="M0 0h24v24H0z" /><path d="M12.866 3l9.526 16.5a1 1 0 0 1-.866 1.5H2.474a1 1 0 0 1-.866-1.5L11.134 3a1 1 0 0 1 1.732 0zm-8.66 16h15.588L12 5.5 4.206 19zM11 16h2v2h-2v-2zm0-7h2v5h-2V9z" fill="rgba(246,0,0,1)" /></svg></div>}
            <div className="extra-help b ignore-error">{__('Subscribers will see and reply to this email address.', 'newsletter-glue')}</div>
            <div className="extra-help ignore-error">{__('Only use verified email addresses.', 'newsletter-glue')} <ExternalLink href={email_help}>{__('Learn more', 'newsletter-glue')}</ExternalLink></div>
          </BaseControl>
        </PanelRow>

        <PanelRow>
          <Button
            isPrimary
            disabled={!isValidated}
            onClick={this.updateOptions}
          >
            {__('Next: Personalise your patterns', 'newsletter-glue')}
          </Button>
        </PanelRow>

      </PanelBody>
    );

  }

}