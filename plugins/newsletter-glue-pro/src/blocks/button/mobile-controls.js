import React from 'react';

import {
  __experimentalToolsPanel as ToolsPanel,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

import { WidthPanel } from './utils';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_lineheight: theme.mobile.button.lineheight,
      mobile_fontsize: theme.mobile.button.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_padding: theme.mobile.button.padding,
    });
  };

  const typographySettings = [
    { value: 'mobile_fontsize', label: 'Font size', default: theme.mobile.button.fontsize, type: 'unit' },
    { value: 'mobile_lineheight', label: 'Line height', default: theme.mobile.button.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'mobile_padding', label: 'Padding', default: theme.mobile.button.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <WidthPanel
        attributes={attributes}
        setAttributes={setAttributes}
      />

      <ToolsPanel label={__('Typography')} resetAll={resetTypography}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={typographySettings} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}