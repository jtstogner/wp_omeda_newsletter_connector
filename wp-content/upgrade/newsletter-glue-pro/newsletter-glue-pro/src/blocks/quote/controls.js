import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { ColorSettingsPane } from '../../components/colors.js';
import { SettingsPane } from '../../components/settings.js';

import { fonts } from '../../defaults/fonts.js';
import { theme } from '../../defaults/theme.js';
import { fontweights } from '../../defaults/weights.js';

export const Controls = props => {

  const { attributes, setAttributes } = props;

  const resetColors = () => {
    setAttributes({
      background: undefined,
      color: undefined,
      cite_color: undefined,
      border: theme.quote.border,
      link: theme.colors.primary,
    });
  };

  const resetTypography = () => {
    setAttributes({
      fontweight: theme.fontweight,
      lineheight: theme.quote.lineheight,
      font: theme.font,
      fontsize: theme.quote.fontsize,
      fontsizeCite: theme.block.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: theme.quote.padding,
    });
  };

  const colors = [
    { value: 'color', label: 'Text' },
    { value: 'background', label: 'Background' },
    { value: 'cite_color', label: 'Citation' },
    { value: 'border', label: 'Border', default: theme.quote.border, required: true },
    { value: 'link', label: 'Link', default: theme.colors.primary, required: true }
  ];

  const typographySettings = [
    { value: 'font', label: __('Font family', 'newsletter-glue'), default: theme.font, type: 'customselect', options: fonts },
    { value: 'fontsize', label: __('Font size', 'newsletter-glue'), default: theme.quote.fontsize, type: 'unit' },
    { value: 'fontsizeCite', label: __('Citation font size', 'newsletter-glue'), default: theme.block.fontsize, type: 'unit' },
    { value: 'fontweight', label: __('Font weight', 'newsletter-glue'), default: theme.fontweight, type: 'customselect', options: fontweights, is_single: true },
    { value: 'lineheight', label: __('Line height', 'newsletter-glue'), default: theme.quote.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: theme.quote.padding, type: 'boxcontrol' },
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