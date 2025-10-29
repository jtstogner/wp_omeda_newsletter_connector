import React from 'react';

import {
  PanelBody,
  __experimentalToolsPanel as ToolsPanel
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingsPane } from '../../components/settings.js';

import { JustifyControl } from '../../components/justify-control.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetSpacing = () => {
    setAttributes({
      padding: theme.buttons.padding,
      spacing: theme.buttons.spacing,
    });
  };

  const spacingSettings = [
    { value: 'mobile_padding', label: 'Padding', default: theme.mobile.buttons.padding, type: 'boxcontrol' },
    { value: 'mobile_spacing', label: 'Button spacing', default: theme.mobile.buttons.spacing, type: 'unit', is_single: true },
  ];

  return (
    <>
      <PanelBody title={__('Layout')}>
        <JustifyControl {...props} />
      </PanelBody>
      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}