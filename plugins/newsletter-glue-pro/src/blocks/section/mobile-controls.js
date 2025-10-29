import React from 'react';

import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetSpacing = () => {
    setAttributes({
      padding: theme.mobile.column.padding,
    });
  };

  const spacingSettings = [
    { value: 'mobile_padding', label: 'Padding', default: theme.mobile.column.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}