import { __ } from '@wordpress/i18n';
import React from 'react';

import {
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOptionIcon as ToggleGroupControlOptionIcon,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';

import { justifyCenter, justifyLeft, justifyRight, justifySpaceBetween } from '@wordpress/icons';

export const JustifyControl = props => {

  const { attributes, setAttributes } = props;

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  let justify;
  if (deviceType !== 'Mobile') {
    justify = attributes.justify;
  } else {
    justify = attributes.mobile_justify ? attributes.mobile_justify : attributes.justify;
  }

  return (
    <ToggleGroupControl
      label={__('Justification')}
      value={justify}
      onChange={(newJustify) => {
        var key = deviceType !== 'Mobile' ? 'justify' : 'mobile_justify';
        setAttributes({ [key]: newJustify });
      }}
    >
      <ToggleGroupControlOptionIcon
        value="left"
        icon={justifyLeft}
        label={__('Justify items left')}
      />
      <ToggleGroupControlOptionIcon
        value="center"
        icon={justifyCenter}
        label={__('Justify items center')}
      />
      <ToggleGroupControlOptionIcon
        value="right"
        icon={justifyRight}
        label={__('Justify items right')}
      />
      <ToggleGroupControlOptionIcon
        value="space-between"
        icon={justifySpaceBetween}
        label={__('Space between items')}
      />
    </ToggleGroupControl>
  );

}