import { __ } from '@wordpress/i18n';
import React from 'react';

import apiFetch from '@wordpress/api-fetch';

import {
  Component
} from '@wordpress/element';

import {
  BaseControl,
  ExternalLink,
  Notice,
  PanelBody,
  PanelRow,
  ToggleControl
} from '@wordpress/components';

const iconUri = nglue_backend.images_uri + 'icon-css.svg';

export default class CustomCSS extends Component {

  constructor(props) {

    super(props);

    this.saveSettings = this.saveSettings.bind(this);

    const data = {
      customCSS: nglue_backend.custom_css,
      disableCSS: parseInt(nglue_backend.disable_css),
      isSaving: false,
      hasSaved: false,
      isSaved: false,
      unsavedChanges: false,
    };

    this.state = data;

  }

  saveSettings() {

    this.setState({
      isSaving: true,
    });

    const data = {
      customCSS: this.state.customCSS,
      disableCSS: this.state.disableCSS
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/save_css_settings',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

      setTimeout(() => {
        this.setState({
          isSaving: false,
          hasSaved: true,
        });
      }, 1500);

      setTimeout(() => {
        this.setState({
          hasSaved: false,
          unsavedChanges: false,
        });
      }, 2500);

    });

  }

  render() {

    const { notLicensed } = this.props.getState;

    const panelClass = notLicensed ? 'nglue-panel-off' : '';

    return (
      <>
        <div className="nglue-main">
          {notLicensed && <div className="nglue-panel-overlay"></div>}
          <PanelBody className={panelClass}>
            <div className={`nglue-title-bar ${panelClass}`}>
              <div className="nglue-title">
                <span className="nglue-title-main">{__('Custom CSS', 'newsletter-glue')}</span>
                <span className="nglue-title-sub">{__('Add custom CSS to all your newsletters. CSS added here will not show up on your site.', 'newsletter-glue')} <ExternalLink href="https://newsletterglue.com/docs/style-newsletter-with-css-classes/">{__('Learn more', 'newsletter-glue')}</ExternalLink></span>
              </div>
              <div className="nglue-title-icon"><img src={iconUri} /></div>
            </div>

            <div className={`nglue-panel-body ${panelClass}`}>

              <div className="nglue-base-parent" style={{ padding: '20px 10px 0 10px' }}>
                <PanelRow className="nglue-row-full">
                  <BaseControl>
                    <Notice status="warning" isDismissible={false} className="ngl-settings-notice">
                      <p dangerouslySetInnerHTML={{ __html: nglue_backend.css_moved }}></p>
                    </Notice>
                  </BaseControl>
                </PanelRow>
              </div>

              <div className="nglue-base-parent" style={{ padding: '0 10px' }}>
                <PanelRow className="nglue-row-full">
                  <div className="nglue-head-part">
                    <div className="nglue-head">{__('Advanced', 'newsletter-glue')}</div>
                  </div>
                </PanelRow>
              </div>

              <div className="nglue-base-parent" style={{ padding: '0 10px' }}>
                <PanelRow className="nglue-row-full">
                  <ToggleControl
                    label={__('Only use custom CSS from the above box?', 'newsletter-glue')}
                    help={__('All default and Newsletter Theme Designer styling will be removed from your newsletter. Only check this box if you plan to style your entire newsletter from scratch.', 'newsletter-glue')}
                    checked={this.state.disableCSS}
                    disabled={true}
                  />
                </PanelRow>
              </div>

              <div className="nglue-base-parent" style={{ padding: '0 10px' }}>
                <PanelRow className="nglue-buttons">
                  <Notice status="warning" isDismissible={false} className="ngl-settings-notice">
                    <p dangerouslySetInnerHTML={{ __html: __('We’ve stopped supporting this feature. If you need this back, <a href="mailto:support@newsletterglue.com">please let us know</a>.', 'newsletter-glue') }}></p>
                  </Notice>
                </PanelRow>
              </div>

            </div>

          </PanelBody>
        </div>
      </>
    );

  }

}