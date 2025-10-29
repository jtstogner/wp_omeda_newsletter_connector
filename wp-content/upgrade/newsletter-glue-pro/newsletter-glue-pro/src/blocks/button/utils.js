import React from 'react';

import {
  PanelBody,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
  __experimentalUnitControl as UnitControl
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { useSelect } from '@wordpress/data';

import { theme } from '../../defaults/theme.js';
import { units } from '../../defaults/units.js';

export function WidthPanel({ attributes, setAttributes }) {

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  var width = attributes.mobile_width && isMobile ? attributes.mobile_width : attributes.width;
  var custom_width = attributes.mobile_custom_width && isMobile ? attributes.mobile_custom_width : attributes.custom_width;

  const mobile = deviceType === 'Mobile' ? 'mobile_' : '';

  return (
    <PanelBody title={__('Width')}>
      <ToggleGroupControl
        value={width}
        onChange={(newWidth) => {
          setAttributes({ [`${mobile}width`]: newWidth });
        }}
        isBlock
      >
        <ToggleGroupControlOption
          value="relative"
          label={__('Default')}
        />
        <ToggleGroupControlOption
          value="full"
          label={__('Full')}
        />
        <ToggleGroupControlOption
          value="custom"
          label={__('Custom')}
        />
      </ToggleGroupControl>

      {width === 'custom' &&
        <div className="ngl-input-small">
          <UnitControl
            label={__('Custom width')}
            value={custom_width}
            onChange={(newValue) => {
              setAttributes({ [`${mobile}custom_width`]: newValue });
            }}
            units={units}
            placeholder={theme.button.width}
          />
        </div>
      }

    </PanelBody>
  );
}