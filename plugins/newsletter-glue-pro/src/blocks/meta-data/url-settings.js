import React from 'react';

import { __ } from '@wordpress/i18n';

import {
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

export function URLSettings(props) {

  const { attrs, setAttributes } = props;

  let attributes = attrs;

  return (
    <ToggleGroupControl
      label={__('Read online links to', 'newsletter-glue')}
      value={attributes.read_online_link}
      onChange={(value) => setAttributes({ read_online_link: value })}
      isBlock
    >
      <ToggleGroupControlOption
        value="email"
        label={__('Email HTML')}
      />
      <ToggleGroupControlOption
        value="blog"
        label={__('Website')}
      />
    </ToggleGroupControl>
  );
}