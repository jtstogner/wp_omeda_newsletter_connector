import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetSpacing = () => {
    setAttributes({
      padding: theme.mobile.columns.padding,
    });
  };

  const spacingSettings = [
    { value: 'mobile_padding', label: 'Padding', default: theme.mobile.columns.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}