import { __ } from '@wordpress/i18n';
import React from 'react';

import {
  BaseControl,
  Button,
  Card,
  CardMedia,
  PanelBody,
  PanelRow,
  RangeControl
} from '@wordpress/components';

import {
  Component,
} from '@wordpress/element';

export default class TemplateWizard2 extends Component {

  render() {

    let frame


    const runUploader = (event) => {
      event.preventDefault()

      // If the media frame already exists, reopen it.
      if (frame) {
        frame.open()
        return
      }

      // Create a new media frame
      frame = wp.media({
        title: __('Select or upload your logo', 'newsletter-glue'),
        button: {
          text: __('Use this image', 'newsletter-glue'),
        },
        multiple: false,
      })

      frame.on('select', function () {
        var attachment = frame.state().get('selection').toJSON()[0];
        if (attachment.id) {
          data.changeState({ logo_id: attachment.id });
          data.changeState({ logo_url: attachment.url });
        }
      });

      // Finally, open the modal on click
      frame.open();
    }

    var props = this.props.state;
    var data = this.props;

    var maxLogoWidth = parseInt(props.logo_width) > 0 ? parseInt(props.logo_width) : 165;

    return (
      <PanelBody>

        <PanelRow>
          <h2>{__('Add a logo', 'newsletter-glue')}</h2>
          <p>{__('You can always change this later in the Newsletter Theme Designer under Settings.', 'newsletter-glue')}</p>
        </PanelRow>

        <PanelRow>
          <BaseControl
            label={__("Your newsletter's logo", 'newsletter-glue')}
            id="nglue-admin-logo"
          >
            {props.logo_id > 0 && <Card>
              <CardMedia>
                <img src={props.logo_url ? props.logo_url : ""} style={{ maxWidth: maxLogoWidth }} />
              </CardMedia>
            </Card>}
            <div className="ngl-button-group">
              <Button
                isSecondary
                disabled={false}
                onClick={runUploader}
              >
                {props.logo_id > 0 ? __('Change logo', 'newsletter-glue') : __('Select logo', 'newsletter-glue')}
              </Button>
              {props.logo_id > 0 &&
                <Button
                  isTertiary
                  onClick={() => {
                    data.changeState({ logo_id: '' });
                    data.changeState({ logo_url: '' });
                  }}
                >
                  {__('Remove logo', 'newsletter-glue')}
                </Button>
              }
            </div>
            <div className="extra-help b">{__('This will be the default logo in your newsletter patterns.', 'newsletter-glue')}</div>
          </BaseControl>
        </PanelRow>

        <PanelRow>
          <BaseControl className="ngl-range-top">
            <RangeControl
              label={__("Your logo's max width", 'newsletter-glue')}
              value={parseInt(props.logo_width)}
              min={0}
              max={600}
              step={10}
              resetFallbackValue={165}
              allowReset={true}
              help={__('You can always resize the logo in the post editor.', 'newsletter-glue')}
              showTooltip={false}
              trackColor="#ddd"
              railColor="#eee"
              onChange={(value) => {
                data.changeState({ logo_width: value });
              }}
            />
          </BaseControl>
        </PanelRow>

        <PanelRow>
          <Button
            isPrimary
            disabled={false}
            onClick={() => {
              data.changeState({ step: 6, completedStep: 5 });
              data.changeOptions();
            }}
          >
            {__('Next: Add social media accounts', 'newsletter-glue')}
          </Button>
        </PanelRow>
      </PanelBody>
    );
  }

}