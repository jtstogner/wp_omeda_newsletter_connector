import { __ } from '@wordpress/i18n';
import React from 'react';

import { MediaUpload } from '@wordpress/block-editor';
import {
  BaseControl,
  Button,
  PanelBody,
  RangeControl,
  SelectControl,
  TextControl,
  ToggleControl,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
  __experimentalToolsPanel as ToolsPanel,
} from '@wordpress/components';

import { image as buttonIcon } from "@wordpress/icons";
import { BiUserCircle as imageIcon } from "react-icons/bi";

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
      button: theme.colors.primary,
    });
  };

  const resetTypography = () => {
    setAttributes({
      fontweight: theme.fontweight,
      lineheight: theme.lineheight,
      font: theme.font,
      fontsize: theme.author.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: theme.author.padding,
    });
  };

  var colors = [
    { value: 'color', label: 'Text' },
    { value: 'background', label: 'Background' },
    { value: 'link', label: 'Link', default: theme.colors.primary, required: true },
  ];

  if (attributes.show_button && attributes.social === 'custom') {
    colors.push({ value: 'button', label: 'Custom URL button', default: theme.colors.primary, required: true });
  }

  const typographySettings = [
    { value: 'font', label: 'Font family', default: theme.font, type: 'customselect', options: fonts },
    { value: 'fontsize', label: 'Font size', default: theme.author.fontsize, type: 'unit' },
    { value: 'fontweight', label: 'Font weight', default: theme.fontweight, type: 'customselect', options: fontweights, is_single: true },
    { value: 'lineheight', label: 'Line height', default: theme.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: theme.author.padding, type: 'boxcontrol' },
  ];

  var onSelectImage = function (media) {
    return setAttributes({ profile_pic: media.url });
  };

  var removeImage = function () {
    setAttributes({ profile_pic: newsletterglue_meta.profile_pic });
  };

  var onSelectIcon = function (media) {
    return setAttributes({ button_icon: media.url });
  };

  var removeIcon = function () {
    setAttributes({ button_icon: '' });
  };

  return (
    <>
      <PanelBody title={__('Settings')}>
        <BaseControl className="ngl-base--flex">
          <MediaUpload
            onSelect={onSelectImage}
            type="image"
            render={function (obj) {
              return <>
                <Button
                  onClick={obj.open}
                  icon={imageIcon}
                  variant="link"
                >Change profile picture</Button>
                {attributes.profile_pic && (attributes.profile_pic != newsletterglue_meta.profile_pic) && (
                  <Button
                    onClick={removeImage}
                    variant="link"
                    isDestructive
                  >Reset</Button>)}
              </>
            }}
          />
        </BaseControl>
      </PanelBody>

      <PanelBody title={__('Button')}>
        <ToggleControl
          label={__('Show button')}
          checked={attributes.show_button}
          onChange={(value) => { setAttributes({ show_button: value }) }}
        />

        {attributes.show_button && (
          <>
            <SelectControl
              label={__('Social media platform')}
              value={attributes.social}
              onChange={(value) => setAttributes({ social: value })}
              options={[
                { value: 'instagram', label: 'Instagram' },
                { value: 'twitter', label: 'Twitter' },
                { value: 'facebook', label: 'Facebook' },
                { value: 'twitch', label: 'Twitch' },
                { value: 'tiktok', label: 'Tiktok' },
                { value: 'youtube', label: 'YouTube' },
                { value: 'linkedin', label: 'LinkedIn' },
                { value: 'custom', label: 'Custom URL' },
              ]}
            />

            <TextControl
              label={__('URL')}
              value={attributes.social_user}
              onChange={(value) => setAttributes({ social_user: value })}
            />

            <ToggleGroupControl
              label={__('Button style', 'newsletter-glue')}
              value={attributes.button_style}
              onChange={(newStyle) => setAttributes({ button_style: newStyle })}
              isBlock
            >
              <ToggleGroupControlOption
                value="solid"
                label={__('Solid')}
              />
              <ToggleGroupControlOption
                value="outline"
                label={__('Outline')}
              />
            </ToggleGroupControl>

            <RangeControl
              label={__('Border radius (pixels)')}
              value={attributes.border_radius}
              initialPosition={5}
              min={0}
              max={50}
              allowReset={true}
              resetFallbackValue={5}
              onChange={(value) => props.setAttributes({ border_radius: value })}
              style={{ margin: 0 }}
            />

            <ToggleGroupControl
              label={__('Icon', 'newsletter-glue')}
              value={attributes.icon_style}
              onChange={(newStyle) => setAttributes({ icon_style: newStyle })}
              isBlock
            >
              <ToggleGroupControlOption
                value="default"
                label={__('Default')}
              />
              <ToggleGroupControlOption
                value="custom"
                label={__('Custom')}
              />
              <ToggleGroupControlOption
                value="no_icon"
                label={__('No icon')}
              />
            </ToggleGroupControl>

            {attributes.icon_style === 'custom' && (
              <BaseControl className="ngl-base--flex">
                <MediaUpload
                  onSelect={onSelectIcon}
                  type="image"
                  render={function (obj) {
                    return <>
                      <Button
                        onClick={obj.open}
                        icon={buttonIcon}
                        variant="link"
                      >Change button icon</Button>
                      {attributes.button_icon && (attributes.button_icon != newsletterglue_meta.button_icon) && (
                        <Button
                          onClick={removeIcon}
                          variant="link"
                          isDestructive
                        >Reset</Button>)}
                    </>
                  }}
                />
              </BaseControl>
            )}

          </>
        )}
      </PanelBody>

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