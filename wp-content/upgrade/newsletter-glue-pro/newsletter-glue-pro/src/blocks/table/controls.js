import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { ColorSettingsPane } from '../../components/colors.js';
import { SettingsPane } from '../../components/settings.js';

import { fonts } from '../../defaults/fonts.js';

import { theme } from '../../defaults/theme.js';

export const Controls = props => {

  const { attributes, setAttributes } = props;

  const resetColors = () => {
    setAttributes({
      background: undefined,
      background2: undefined,
      backgroundhead: undefined,
      backgroundfoot: undefined,
      color: undefined,
      link: theme.colors.primary,
      border: undefined,
    });
  };

  const resetTypography = () => {
    setAttributes({
      lineheight: theme.table.lineheight,
      font: theme.font,
      fontsize: theme.table.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      margin: theme.table.margin,
      padding: theme.table.padding,
    });
  };

  const { head, foot, hasBorder } = attributes;
  const isStriped = attributes.style === 'stripes' ? true : false;

  let headerbg = '';
  let footerbg = '';
  let stripebg = '';
  let bordercolor = '';

  if (head && head.length) {
    headerbg = { value: 'backgroundhead', label: 'Header background' };
  }

  if (foot && foot.length) {
    footerbg = { value: 'backgroundfoot', label: 'Footer background' };
  }

  if (hasBorder) {
    bordercolor = { value: 'border', label: 'Border' };
  }

  if (isStriped) {
    stripebg = { value: 'background2', label: 'Stripe background 2' };
  }

  let colors = [
    { value: 'color', label: 'Text' },
    { value: 'background', label: isStriped ? 'Stripe background 1' : 'Background' },
    { ...stripebg },
    { ...headerbg },
    { ...footerbg },
    { value: 'link', label: 'Link', default: theme.colors.primary, required: true },
    { ...bordercolor }
  ];

  const typographySettings = [
    { value: 'font', label: 'Font family', default: theme.font, type: 'customselect', options: fonts },
    { value: 'fontsize', label: 'Font size', default: theme.table.fontsize, type: 'unit' },
    { value: 'lineheight', label: 'Line height', default: theme.table.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'margin', label: 'Margin', default: theme.table.margin, type: 'boxcontrol' },
    { value: 'padding', label: 'Cell spacing', default: theme.table.padding, type: 'boxcontrol' },
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