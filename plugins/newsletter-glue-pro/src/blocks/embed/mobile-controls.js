import React from 'react';

import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_lineheight: theme.mobile.embed.lineheight,
      mobile_size: theme.mobile.embed.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_padding: theme.mobile.embed.padding,
      mobile_margin: theme.mobile.embed.margin,
    });
  };

  const typographySettings = [
    { value: 'mobile_size', label: 'Font size', default: theme.mobile.embed.fontsize, type: 'unit' },
    { value: 'mobile_lineheight', label: 'Line height', default: theme.mobile.embed.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'mobile_padding', label: 'Padding', default: theme.mobile.embed.padding, type: 'boxcontrol' },
    { value: 'mobile_margin', label: 'Margin', default: theme.mobile.embed.margin, type: 'boxcontrol' },
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