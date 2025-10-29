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
      link: theme.colors.primary,
      border: undefined,
    });
  };

  const resetTypography = () => {
    setAttributes({
      fontweight: theme.container.fontweight,
      lineheight: theme.container.lineheight,
      font: theme.font,
      fontsize: theme.container.fontsize,
    });
  };

  const resetBorder = () => {
    setAttributes({
      borderSize: undefined,
      radius: undefined,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: theme.container.padding,
      margin: theme.container.margin,
    });
  };

  const colors = [
    { value: 'color', label: 'Text' },
    { value: 'background', label: 'Background' },
    { value: 'link', label: 'Link', default: theme.colors.primary, required: true },
    { value: 'border', label: 'Border' }
  ];

  const typographySettings = [
    { value: 'font', label: 'Font family', default: theme.font, type: 'customselect', options: fonts },
    { value: 'fontsize', label: 'Font size', default: theme.container.fontsize, type: 'unit' },
    { value: 'fontweight', label: 'Font weight', default: theme.container.fontweight, type: 'customselect', options: fontweights, is_single: true },
    { value: 'lineheight', label: 'Line height', default: theme.container.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const borderSettings = [
    { value: 'borderSize', label: 'Border width', type: 'unit', is_single: true },
    { value: 'radius', label: 'Border radius', type: 'unit', is_single: true },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: theme.container.padding, type: 'boxcontrol' },
    { value: 'margin', label: 'Margin', default: theme.container.margin, type: 'boxcontrol' },
  ];

  return (
    <>
      <ToolsPanel label={__('Colors')} resetAll={resetColors} hasInnerWrapper={true} className="color-block-support-panel">
        <ColorSettingsPane attributes={attributes} setAttributes={setAttributes} colors={colors} />
      </ToolsPanel>

      <ToolsPanel label={__('Typography')} resetAll={resetTypography}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={typographySettings} />
      </ToolsPanel>

      <ToolsPanel label={__('Border')} resetAll={resetBorder}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={borderSettings} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}