import { __ } from '@wordpress/i18n';
import React from 'react';

import {
  BaseControl,
  Button,
  PanelBody,
  PanelRow,
  TextControl,
  TextareaControl
} from '@wordpress/components';

import {
  Component,
} from '@wordpress/element';

export default class TemplateWizard1 extends Component {

  render() {

    var props = this.props.state;
    var data = this.props;

    var showAdminName = __('For example: {{ admin_name,fallback=' + props.admin_name + ' }}', 'newsletter-glue');

    var showAdminAddress = __('For example: {{ admin_address,fallback=221B Baker Street }}', 'newsletter-glue');
    if (props.admin_address) {
      showAdminAddress = __('For example: {{ admin_address,fallback=' + props.admin_address + ' }}', 'newsletter-glue');
    }

    return (
      <PanelBody>

        <PanelRow>
          {props.showPatternWelcome && <h4>{__('Welcome! This will only take a minute.', 'newsletter-glue')}</h4>}
          <h2>{__('Personalise your patterns', 'newsletter-glue')}</h2>
          <p>{__('You can always change this later in each specific mergetag.', 'newsletter-glue')}</p>
        </PanelRow>

        <PanelRow>
          <BaseControl
            label={__('Your name/Company name', 'newsletter-glue')}
            id="nglue-admin-name"
          >
            <TextControl
              id="nglue-admin-name"
              value={props.admin_name}
              placeholder={props.admin_name}
              onChange={
                (value) => {
                  data.changeState({ admin_name: value });
                }
              }
            />
            <div className="extra-help b">{__('This will be used in the name mergetag.', 'newsletter-glue')}</div>
            <div className="extra-help">{showAdminName}</div>
          </BaseControl>
        </PanelRow>

        <PanelRow>
          <BaseControl
            label={__('Your physical address', 'newsletter-glue')}
            id="nglue-admin-address"
          >
            <TextareaControl
              id="nglue-admin-address"
              value={props.admin_address}
              placeholder={__('Street address', 'newsletter-glue')}
              rows="4"
              onChange={
                (value) => {
                  data.changeState({ admin_address: value });
                }
              }
            />
            <div className="extra-help b">{__('This will be used in the address mergetag to comply with international anti-spam laws.', 'newsletter-glue')}</div>
            <div className="extra-help">{showAdminAddress}</div>
          </BaseControl>
        </PanelRow>

        <PanelRow>
          <Button
            isPrimary
            disabled={false}
            onClick={() => {
              data.changeState({ step: 5, completedStep: 4 });
              data.changeOptions();
            }}
          >
            {__('Next: Add a logo', 'newsletter-glue')}
          </Button>
        </PanelRow>
      </PanelBody>
    );
  }

}