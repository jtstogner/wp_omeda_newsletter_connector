import React from 'react';

import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { ColorSettingsPane } from '../../components/colors.js';
import { SettingsPane } from '../../components/settings.js';

import { fonts } from '../../defaults/fonts.js';
import { theme } from '../../defaults/theme.js';

export const Controls = props => {

  const { attributes, setAttributes } = props;

  const level = 'h' + attributes.level;

  const resetColors = () => {
    setAttributes({
      background: undefined,
      h1_colour: undefined,
      h2_colour: undefined,
      h3_colour: undefined,
      h4_colour: undefined,
      h5_colour: undefined,
      h6_colour: undefined,
      link: theme.colors.primary,
    });
  };

  const resetTypography = () => {
    setAttributes({
      h1_lineheight: theme.headings.h1.lineheight,
      h2_lineheight: theme.headings.h2.lineheight,
      h3_lineheight: theme.headings.h3.lineheight,
      h4_lineheight: theme.headings.h4.lineheight,
      h5_lineheight: theme.headings.h5.lineheight,
      h6_lineheight: theme.headings.h6.lineheight,
      h1_font: theme.headings.h1.font,
      h2_font: theme.headings.h2.font,
      h3_font: theme.headings.h3.font,
      h4_font: theme.headings.h4.font,
      h5_font: theme.headings.h5.font,
      h6_font: theme.headings.h6.font,
      h1_size: theme.headings.h1.fontsize,
      h2_size: theme.headings.h2.fontsize,
      h3_size: theme.headings.h3.fontsize,
      h4_size: theme.headings.h4.fontsize,
      h5_size: theme.headings.h5.fontsize,
      h6_size: theme.headings.h6.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      h1_padding: theme.headings.h1.padding,
      h2_padding: theme.headings.h2.padding,
      h3_padding: theme.headings.h3.padding,
      h4_padding: theme.headings.h4.padding,
      h5_padding: theme.headings.h5.padding,
      h6_padding: theme.headings.h6.padding,
    });
  };

  const colors = [
    { value: level + '_colour', label: 'Text' },
    { value: 'background', label: 'Background' },
    { value: 'link', label: 'Link', default: theme.colors.primary, required: true }
  ];

  const typographySettings = [
    { value: level + '_font', label: 'Font family', default: theme.headings[level].font, type: 'customselect', options: fonts },
    { value: level + '_size', label: 'Font size', default: theme.headings[level].fontsize, type: 'unit' },
    { value: level + '_lineheight', label: 'Line height', default: theme.headings[level].lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: level + '_padding', label: 'Padding', default: theme.headings[level].padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <ToolsPanel label={__('Colors')} resetAll={resetColors} hasInnerWrapper={true} className="color-block-support-panel">
        <ColorSettingsPane attributes={attributes} setAttributes={setAttributes} colors={colors} />
      </ToolsPanel>

      <ToolsPanel label={__('Typography')} resetAll={resetTypography}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={typographySettings} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}