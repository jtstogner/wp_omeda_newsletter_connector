import React from 'react';

import { PanelBody, PanelRow, ToggleControl, __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_fontsize_title: theme.mobile.posts.fontsize_title,
      mobile_fontsize_text: theme.mobile.posts.fontsize_text,
      mobile_fontsize_label: theme.mobile.posts.fontsize_label,
      mobile_fontsize_author: theme.mobile.posts.fontsize_author,
      mobile_fontsize_button: theme.mobile.posts.fontsize_button,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_padding: theme.mobile.posts.padding,
      mobile_margin: theme.mobile.posts.margin,
    });
  };

  const typographySettings = [
    { value: 'mobile_fontsize_title', label: 'Heading font size', default: theme.mobile.posts.fontsize_title, type: 'unit' },
    { value: 'mobile_fontsize_text', label: 'Text font size', default: theme.mobile.posts.fontsize_text, type: 'unit' },
    { value: 'mobile_fontsize_label', label: 'Label font size', default: theme.mobile.posts.fontsize_label, type: 'unit' },
    { value: 'mobile_fontsize_author', label: 'Author font size', default: theme.mobile.posts.fontsize_author, type: 'unit' },
    { value: 'mobile_fontsize_button', label: 'Button/link font size', default: theme.mobile.posts.fontsize_button, type: 'unit' },
  ];

  const spacingSettings = [
    { value: 'mobile_padding', label: 'Padding', default: theme.mobile.posts.padding, type: 'boxcontrol' },
    { value: 'mobile_margin', label: 'Margin', default: theme.mobile.posts.margin, type: 'boxcontrol' },
  ];

  return (
    <>
      <PanelBody title={__('Settings')}>
        <PanelRow>
          <ToggleControl
            label={__('Collapse into 1 column', 'newsletter-glue')}
            checked={attributes.stacked_on_mobile}
            onChange={(value) => {
              setAttributes({ stacked_on_mobile: value });
            }}
          />
        </PanelRow>
      </PanelBody>
      <ToolsPanel label={__('Typography')} resetAll={resetTypography}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={typographySettings} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}