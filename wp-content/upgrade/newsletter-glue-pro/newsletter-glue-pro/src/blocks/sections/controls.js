import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { ColorSettingsPane } from '../../components/colors.js';
import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const Controls = props => {

  const { attributes, setAttributes } = props;

  const resetColors = () => {
    setAttributes({
      background: undefined,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: theme.columns.padding,
    });
  };

  const colors = [
    { value: 'background', label: 'Background' },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: theme.columns.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <ToolsPanel label={__('Colors')} resetAll={resetColors} hasInnerWrapper={true} className="color-block-support-panel">
        <ColorSettingsPane attributes={attributes} setAttributes={setAttributes} colors={colors} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}