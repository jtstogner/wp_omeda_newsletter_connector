import {
  BaseControl,
  PanelBody,
  RangeControl,
  SelectControl,
  ToggleControl,
  __experimentalToolsPanel as ToolsPanel
} from '@wordpress/components';
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
      padding: theme.block.padding,
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
    { value: 'padding', label: 'Padding', default: theme.block.padding, type: 'boxcontrol' },
  ];

  function onChangeShape(value) {
    setAttributes({ icon_shape: value });
    var children = wp.data.select('core/block-editor').getBlocksByClientId(props.clientId)[0].innerBlocks;
    children.forEach(function (child) {
      wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { icon_shape: value });
    });
  }

  function onChangeColor(value) {
    setAttributes({ icon_color: value });
    var children = wp.data.select('core/block-editor').getBlocksByClientId(props.clientId)[0].innerBlocks;
    children.forEach(function (child) {
      wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { icon_color: value });
    });
  }

  function onChangeSize(value) {
    setAttributes({ icon_size: value });
    var children = wp.data.select('core/block-editor').getBlocksByClientId(props.clientId)[0].innerBlocks;
    children.forEach(function (child) {
      wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { icon_size: value });
    });
  }

  return (
    <>
      <ToolsPanel label={__('Description')} resetAll={resetTypography}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={typographySettings} />
      </ToolsPanel>

      <PanelBody title={__('Icons')}>
        <BaseControl>
          <ToggleControl
            label={__('Show description text', 'newsletter-glue')}
            onChange={(val) => setAttributes({ add_description: val })}
            checked={attributes.add_description}
          />
        </BaseControl>
        <BaseControl>
          <SelectControl
            label={__('Icon shape')}
            value={attributes.icon_shape}
            onChange={onChangeShape}
            options={[
              { value: 'round', label: 'Circle' },
              { value: 'round_stroke', label: 'Outline circle' },
              { value: 'square', label: 'Square' },
              { value: 'rounded_corners', label: 'Rounded square' },
              { value: 'rounded_stroke', label: 'Outlined square' },
              { value: 'default', label: 'Default' },
            ]}
          />
        </BaseControl>
        <BaseControl>
          <SelectControl
            label={__('Icon color')}
            value={attributes.icon_color}
            onChange={onChangeColor}
            options={[
              { value: 'black', label: 'Black' },
              { value: 'color', label: 'Color' },
              { value: 'grey', label: 'Gray' },
              { value: 'white', label: 'White' },
            ]}
          />
        </BaseControl>
        <BaseControl>
          <RangeControl
            label={__('Icon size (pixels)')}
            value={attributes.icon_size}
            onChange={onChangeSize}
            resetFallbackValue={24}
            allowReset
            initialPosition={12}
            min={12}
            max={128}
          />
        </BaseControl>
      </PanelBody>

      <ToolsPanel label={__('Colors')} resetAll={resetColors} hasInnerWrapper={true} className="color-block-support-panel">
        <ColorSettingsPane attributes={attributes} setAttributes={setAttributes} colors={colors} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}