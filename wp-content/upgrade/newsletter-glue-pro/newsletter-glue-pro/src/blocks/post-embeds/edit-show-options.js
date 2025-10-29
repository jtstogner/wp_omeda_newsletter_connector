import { PanelBody, PanelRow, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

export const LpShowOptions = props => {

  const { attributes, setAttributes } = props;

  return (

    <PanelBody
      title={__('Show/hide block', 'newsletter-glue')}
      initialOpen={true}
      className="ngl-panel-body"
    >

      <PanelRow>
        <ToggleControl
          label={__('Show in blog post', 'newsletter-glue')}
          checked={attributes.show_in_web}
          onChange={(value) => {
            setAttributes({ show_in_web: value });
          }}
        />
      </PanelRow>

      <PanelRow>
        <ToggleControl
          label={__('Show in email newsletter ', 'newsletter-glue')}
          checked={attributes.show_in_email}
          onChange={(value) => {
            setAttributes({ show_in_email: value });
          }}
        />
      </PanelRow>

    </PanelBody>

  );
}