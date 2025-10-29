import React from 'react';

import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

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
    });
  };

  const resetTypography = () => {
    setAttributes({
      fontweight: theme.fontweight,
      lineheight: theme.lineheight,
      font: theme.font,
      fontsize: theme.block.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: attributes.isParent ? theme.list.main_padding : theme.list.padding,
      spacing: theme.list.spacing,
    });
  };

  const colors = [
    { value: 'color', label: 'Text' },
    { value: 'background', label: 'Background' },
    { value: 'link', label: 'Link', default: theme.colors.primary, required: true }
  ];

  const typographySettings = [
    { value: 'font', label: 'Font family', default: theme.font, type: 'customselect', options: fonts },
    { value: 'fontsize', label: 'Font size', default: theme.block.fontsize, type: 'unit' },
    { value: 'fontweight', label: 'Font weight', default: theme.fontweight, type: 'customselect', options: fontweights, is_single: true },
    { value: 'lineheight', label: 'Line height', default: theme.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: attributes.isParent ? theme.list.main_padding : theme.list.padding, type: 'boxcontrol' },
    { value: 'spacing', label: 'List item spacing', default: theme.list.spacing, type: 'unit', is_single: true },
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