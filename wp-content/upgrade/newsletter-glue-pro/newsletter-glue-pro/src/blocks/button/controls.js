import React from 'react';

import {
  __experimentalToolsPanel as ToolsPanel,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { ColorSettingsPane } from '../../components/colors.js';
import { SettingsPane } from '../../components/settings.js';

import { fonts } from '../../defaults/fonts.js';
import { theme } from '../../defaults/theme.js';
import { fontweights } from '../../defaults/weights.js';

import { WidthPanel } from './utils';

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
      fontweight: theme.fontweight,
      lineheight: theme.button.lineheight,
    });
  };

  const resetBorder = () => {
    setAttributes({
      borderSize: undefined,
      radius: theme.button.radius,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: theme.button.padding,
    });
  };

  const colors = [
    { value: 'color', label: 'Text' },
    { value: 'background', label: 'Background' },
    { value: 'border', label: 'Border' }
  ];

  const typographySettings = [
    { value: 'font', label: 'Font family', default: theme.font, type: 'customselect', options: fonts },
    { value: 'fontsize', label: 'Font size', default: theme.block.fontsize, type: 'unit' },
    { value: 'fontweight', label: 'Font weight', default: theme.fontweight, type: 'customselect', options: fontweights, is_single: true },
    { value: 'lineheight', label: 'Line height', default: theme.button.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const borderSettings = [
    { value: 'borderSize', label: 'Border width', type: 'unit', is_single: true },
    { value: 'radius', label: 'Border radius', default: theme.button.radius, type: 'unit', is_single: true },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: theme.button.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <WidthPanel
        attributes={attributes}
        setAttributes={setAttributes}
      />
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