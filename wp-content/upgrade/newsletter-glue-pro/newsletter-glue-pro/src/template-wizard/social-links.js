import { __ } from '@wordpress/i18n';
import React from 'react';

import apiFetch from '@wordpress/api-fetch';

import {
  BaseControl,
  Button,
  PanelBody,
  PanelRow,
  TextControl,
} from '@wordpress/components';

import {
  Component
} from '@wordpress/element';

import { items } from './social-links-items';

export default class TemplateWizard3 extends Component {

  constructor(props) {

    super(props);

    this.finishSetup = this.finishSetup.bind(this);

    const data = {
      finishingSetup: false,
    };

    this.state = data;
  }

  finishSetup() {

    var props = this.props.state;

    this.setState({ finishingSetup: true });

    let newdata = props;
    newdata['recreate_demo'] = true;

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/pattern_settings',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: newdata
    }).then(() => {

      apiFetch({
        path: 'newsletterglue/' + nglue_backend.api_version + '/update_patterns',
        method: 'post',
        headers: {
          'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
        },
        data: newdata
      }).then(response => {

        var redirect_to = response.redirect_to ? response.redirect_to : props.successRedirect;
        window.location.href = redirect_to.replace(/&amp;/g, "&");

      });

    });

  }

  render() {

    var props = this.props.state;
    var data = this.props;

    const listItems = items.map((d) => <PanelRow key={`row-${d.value}`}>
      <BaseControl
        label={[d.label, <i key={d.value} className="ngl-optional">(optional)</i>]}
        id={`nglue-admin-${d.value}`}
      >
        <TextControl
          id={`nglue-admin-${d.value}`}
          value={props[d.value]}
          onChange={
            (value) => {
              data.changeState({ [d.value]: value });
            }
          }
        />
      </BaseControl>
    </PanelRow>);

    return (
      <PanelBody>
        <PanelRow>
          <h2>{__('Social media accounts', 'newsletter-glue')}</h2>
          <p>{__('This will be used in our custom Social Follow block. You can always change it later on.', 'newsletter-glue')}</p>
        </PanelRow>

        <div className="ngl-wizard-flex">
          {listItems}
        </div>

        {!props.isOnboarding &&
          <PanelRow>
            <Button
              isPrimary
              disabled={false}
              onClick={() => {
                data.changeState({ step: 7, completedStep: 6 });
                data.changeOptions();
              }}
            >
              {__('Done! Create custom patterns', 'newsletter-glue')}
            </Button>
          </PanelRow>}

        {props.isOnboarding &&
          <>
            <PanelRow>
              <h3>{__('Your setup is complete! Ready to send your first test email?', 'newsletter-glue')}</h3>
              <p style={{ marginTop: 0 }}>{__("We’ve created a demo post for you to play with and send as your first test email.  Let’s head there now.", 'newsletter-glue')}</p>
            </PanelRow>
            <PanelRow>
              <Button
                isPrimary
                className="has-icon-right"
                onClick={this.finishSetup}
                isBusy={this.state.finishingSetup}
                disabled={this.state.finishingSetup}
              >
                {this.state.finishingSetup && __('Finalizing setup...', 'newsletter-glue')}
                {!this.state.finishingSetup && __('Complete onboarding and head to demo post', 'newsletter-glue')}
              </Button>
            </PanelRow>
          </>
        }

      </PanelBody>
    );
  }

}