import React from 'react';

import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_list_lineheight: theme.mobile.list.lineheight,
      mobile_list_size: theme.mobile.list.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_list_padding: attributes.isParent ? theme.mobile.list.main_padding : theme.mobile.list.padding,
      mobile_list_spacing: theme.mobile.list.spacing,
    });
  };

  const typographySettings = [
    { value: 'mobile_list_size', label: 'Font size', default: theme.mobile.list.fontsize, type: 'unit' },
    { value: 'mobile_list_lineheight', label: 'Line height', default: theme.mobile.list.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'mobile_list_padding', label: 'Padding', default: attributes.isParent ? theme.mobile.list.main_padding : theme.mobile.list.padding, type: 'boxcontrol' },
    { value: 'mobile_list_spacing', label: 'List item spacing', default: theme.mobile.list.spacing, type: 'unit', is_single: true },
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