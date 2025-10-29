import React from 'react';

import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_p_lineheight: theme.mobile.paragraph.lineheight,
      mobile_p_size: theme.mobile.paragraph.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_p_padding: theme.mobile.paragraph.padding,
    });
  };

  const typographySettings = [
    { value: 'mobile_p_size', label: 'Font size', default: theme.mobile.paragraph.fontsize, type: 'unit' },
    { value: 'mobile_p_lineheight', label: 'Line height', default: theme.mobile.paragraph.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'mobile_p_padding', label: 'Padding', default: theme.mobile.paragraph.padding, type: 'boxcontrol' },
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