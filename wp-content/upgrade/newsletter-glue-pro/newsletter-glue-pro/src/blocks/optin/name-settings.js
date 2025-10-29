import React from 'react';

import { __ } from '@wordpress/i18n';

import {
  ToggleControl
} from '@wordpress/components';

export function NameSettings(props) {

  const { attrs, setAttributes } = props;

  let attributes = attrs;

  return (
    <div style={{ padding: '10px 0 0' }}>
      <ToggleControl
        label={__('Make it required', 'newsletter-glue')}
        checked={attributes.name_required}
        onChange={(value) => {
          setAttributes({ name_required: value });
        }}
      />
    </div>
  );
}