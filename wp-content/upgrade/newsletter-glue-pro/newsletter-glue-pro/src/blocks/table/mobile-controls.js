import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_lineheight: theme.mobile.table.lineheight,
      mobile_size: theme.mobile.table.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_margin: theme.mobile.table.margin,
      mobile_padding: theme.mobile.table.padding,
    });
  };

  const typographySettings = [
    { value: 'mobile_size', label: 'Font size', default: theme.mobile.table.fontsize, type: 'unit' },
    { value: 'mobile_lineheight', label: 'Line height', default: theme.mobile.table.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'mobile_margin', label: 'Margin', default: theme.mobile.table.margin, type: 'boxcontrol' },
    { value: 'mobile_padding', label: 'Cell spacing', default: theme.mobile.table.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <ToolsPanel label={__('Typography')} resetAll={resetTypography}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={typographySettings} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}