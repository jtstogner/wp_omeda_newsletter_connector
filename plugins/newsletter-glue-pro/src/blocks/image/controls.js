import React from 'react';

import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { ColorSettingsPane } from '../../components/colors.js';
import { SettingsPane } from '../../components/settings.js';

import { fonts } from '../../defaults/fonts.js';
import { theme } from '../../defaults/theme.js';

export const Controls = props => {

  const { attributes, setAttributes } = props;

  const resetColors = () => {
    setAttributes({
      background: undefined,
      color: undefined,
      border: undefined,
    });
  };

  const resetTypography = () => {
    setAttributes({
      font: theme.font,
      fontsize: theme.block.fontsize,
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
      padding: theme.block.padding,
    });
  };

  const colors = [
    { value: 'background', label: 'Background' },
    { value: 'color', label: 'Caption' },
    { value: 'border', label: 'Border' }
  ];

  const typographySettings = [
    { value: 'font', label: 'Font family', default: theme.font, type: 'customselect', options: fonts },
    { value: 'fontsize', label: 'Font size', default: theme.image.fontsize, type: 'unit' },
  ];

  const borderSettings = [
    { value: 'borderSize', label: 'Border width', type: 'unit', is_single: true },
    { value: 'radius', label: 'Border radius', type: 'unit', is_single: true },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: theme.image.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <ToolsPanel label={__('Colors')} resetAll={resetColors} hasInnerWrapper={true} className="color-block-support-panel">
        <ColorSettingsPane attributes={attributes} setAttributes={setAttributes} colors={colors} />
      </ToolsPanel>

      <ToolsPanel label={__('Caption')} resetAll={resetTypography}>
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