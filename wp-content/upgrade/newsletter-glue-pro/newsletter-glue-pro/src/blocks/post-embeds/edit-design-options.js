import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

export const LpDesignOptions = () => {

  return (

    <PanelBody
      title={__('Design options', 'newsletter-glue')}
      initialOpen={true}
      className="ngl-panel-body"
    >

    </PanelBody>

  );
}