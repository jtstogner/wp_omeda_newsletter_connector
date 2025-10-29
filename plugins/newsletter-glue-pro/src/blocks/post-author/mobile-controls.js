import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_lineheight: theme.mobile.author.lineheight,
      mobile_size: theme.mobile.author.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_padding: theme.mobile.author.padding,
    });
  };

  const typographySettings = [
    { value: 'mobile_size', label: 'Font size', default: theme.mobile.author.fontsize, type: 'unit' },
    { value: 'mobile_lineheight', label: 'Line height', default: theme.mobile.author.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'mobile_padding', label: 'Padding', default: theme.mobile.author.padding, type: 'boxcontrol' },
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