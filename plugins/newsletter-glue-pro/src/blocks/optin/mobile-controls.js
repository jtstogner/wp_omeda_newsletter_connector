import React from 'react';

import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_fontsize_heading: theme.mobile.optin.fontsize_heading,
      mobile_fontsize_desc: theme.mobile.optin.fontsize_desc,
      mobile_fontsize_label: theme.mobile.optin.fontsize_label,
      mobile_fontsize_input: theme.mobile.optin.fontsize_input,
      mobile_fontsize_text: theme.mobile.optin.fontsize_text,
      mobile_fontsize_checkbox: theme.mobile.optin.fontsize_checkbox,
      mobile_fontsize_button: theme.mobile.optin.fontsize_button,
      mobile_fontsize_success: theme.mobile.optin.fontsize_success,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_padding: theme.mobile.optin.padding,
      mobile_margin: theme.mobile.optin.margin,
    });
  };

  const typographySettings = [
    { value: 'mobile_fontsize_heading', label: 'Heading font size', default: theme.mobile.optin.fontsize_heading, type: 'unit' },
    { value: 'mobile_fontsize_desc', label: 'Description font size', default: theme.mobile.optin.fontsize_desc, type: 'unit' },
    { value: 'mobile_fontsize_label', label: 'Name and email title font size', default: theme.mobile.optin.fontsize_label, type: 'unit' },
    { value: 'mobile_fontsize_input', label: 'Name and email input font size', default: theme.mobile.optin.fontsize_input, type: 'unit' },
    { value: 'mobile_fontsize_text', label: 'Text beneath button font size', default: theme.mobile.optin.fontsize_text, type: 'unit' },
    { value: 'mobile_fontsize_checkbox', label: 'Checkbox font size', default: theme.mobile.optin.fontsize_checkbox, type: 'unit' },
    { value: 'mobile_fontsize_button', label: 'Button font size', default: theme.mobile.optin.fontsize_button, type: 'unit' },
    { value: 'mobile_fontsize_success', label: 'Success message font size', default: theme.mobile.optin.fontsize_success, type: 'unit' },
  ];

  const spacingSettings = [
    { value: 'mobile_padding', label: 'Padding', default: theme.mobile.optin.padding, type: 'boxcontrol' },
    { value: 'mobile_margin', label: 'Margin', default: theme.mobile.optin.margin, type: 'boxcontrol' },
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