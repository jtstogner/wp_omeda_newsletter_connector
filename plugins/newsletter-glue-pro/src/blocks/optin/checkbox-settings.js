import { __ } from '@wordpress/i18n';
import React from 'react';

import {
  ToggleControl
} from '@wordpress/components';

export function CheckboxSettings(props) {

  const { attrs, setAttributes } = props;

  let attributes = attrs;

  return (
    <div style={{ padding: '10px 0 0' }}>
      <ToggleControl
        label={__('Make it required', 'newsletter-glue')}
        checked={attributes.cb_required}
        onChange={(value) => {
          setAttributes({ cb_required: value });
        }}
      />
    </div>
  );
}