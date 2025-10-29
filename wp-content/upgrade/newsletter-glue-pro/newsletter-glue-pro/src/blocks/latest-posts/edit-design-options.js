import React from 'react';

import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

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