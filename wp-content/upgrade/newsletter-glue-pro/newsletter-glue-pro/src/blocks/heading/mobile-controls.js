import React from 'react';

import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const level = 'h' + attributes.level;

  const resetTypography = () => {
    setAttributes({
      mobile_h1_lineheight: theme.mobile.headings.h1.lineheight,
      mobile_h2_lineheight: theme.mobile.headings.h2.lineheight,
      mobile_h3_lineheight: theme.mobile.headings.h3.lineheight,
      mobile_h4_lineheight: theme.mobile.headings.h4.lineheight,
      mobile_h5_lineheight: theme.mobile.headings.h5.lineheight,
      mobile_h6_lineheight: theme.mobile.headings.h6.lineheight,
      mobile_h1_size: theme.mobile.headings.h1.fontsize,
      mobile_h2_size: theme.mobile.headings.h2.fontsize,
      mobile_h3_size: theme.mobile.headings.h3.fontsize,
      mobile_h4_size: theme.mobile.headings.h4.fontsize,
      mobile_h5_size: theme.mobile.headings.h5.fontsize,
      mobile_h6_size: theme.mobile.headings.h6.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_h1_padding: theme.mobile.headings.h1.padding,
      mobile_h2_padding: theme.mobile.headings.h2.padding,
      mobile_h3_padding: theme.mobile.headings.h3.padding,
      mobile_h4_padding: theme.mobile.headings.h4.padding,
      mobile_h5_padding: theme.mobile.headings.h5.padding,
      mobile_h6_padding: theme.mobile.headings.h6.padding,
    });
  };

  const typographySettings = [
    { value: 'mobile_' + level + '_size', label: 'Font size', default: theme.mobile.headings[level].fontsize, type: 'unit' },
    { value: 'mobile_' + level + '_lineheight', label: 'Line height', default: theme.mobile.headings[level].lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'mobile_' + level + '_padding', label: 'Padding', default: theme.mobile.headings[level].padding, type: 'boxcontrol' },
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