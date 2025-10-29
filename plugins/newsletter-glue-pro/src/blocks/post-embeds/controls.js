import { __experimentalToolsPanel as ToolsPanel } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import React from "react";

import { ColorSettingsPane } from "../../components/colors.js";
import { SettingsPane } from "../../components/settings.js";

import { fonts } from "../../defaults/fonts.js";
import { theme } from "../../defaults/theme.js";

export const Controls = (props) => {
  const { attributes, setAttributes } = props;

  const resetColors = () => {
    setAttributes({
      background_color: undefined,
      text_color: undefined,
      title_color: undefined,
      label_color: undefined,
      author_color: undefined,
      link: theme.colors.primary,
      button: theme.colors.btn_bg,
      button_text: theme.colors.btn_colour,
      border_color: undefined,
      divider_bg: undefined,
    });
  };

  const resetTypography = () => {
    setAttributes({
      font: theme.font,
      font_title: theme.font,
      fontsize_title: theme.posts.fontsize_title,
      font_text: theme.font,
      fontsize_text: theme.posts.fontsize_text,
      font_label: theme.font,
      fontsize_label: theme.posts.fontsize_label,
      font_author: theme.font,
      fontsize_author: theme.posts.fontsize_author,
      font_button: theme.font,
      fontsize_button: theme.posts.fontsize_button,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: theme.posts.padding,
      margin: theme.posts.margin,
    });
  };

  let colors = [
    { value: "text_color", label: "Excerpt" },
    { value: "background_color", label: "Background" },
    { value: "title_color", label: "Title" },
    { value: "label_color", label: "Label" },
    { value: "author_color", label: "Author" },
  ];

  if (attributes.cta_type === "link" && attributes.show_cta) {
    colors.push({
      value: "link",
      label: "Link",
      default: theme.colors.primary,
      required: true,
    });
  }

  if (attributes.cta_type === "button" && attributes.show_cta) {
    colors.push({
      value: "button",
      label: "Button",
      default: theme.colors.btn_bg,
      required: true,
    });
    colors.push({
      value: "button_text",
      label: "Button text",
      default: theme.colors.btn_colour,
      required: true,
    });
  }

  if (attributes.border_size) {
    colors.push({ value: "border_color", label: "Border" });
  }

  if (attributes.divider_size && attributes.show_divider) {
    colors.push({ value: "divider_bg", label: "Separator" });
  }

  const typographySettings = [
    { type: "section", label: "Title" },
    {
      value: "font_title",
      label: "Font family",
      default: theme.font,
      type: "customselect",
      options: fonts,
      is_single: true,
    },
    {
      value: "fontsize_title",
      label: "Font size",
      default: theme.posts.fontsize_title,
      type: "unit",
      is_single: true,
    },

    { type: "section", label: "Excerpt" },
    {
      value: "font_text",
      label: "Font family",
      default: theme.font,
      type: "customselect",
      options: fonts,
      is_single: true,
    },
    {
      value: "fontsize_text",
      label: "Font size",
      default: theme.posts.fontsize_text,
      type: "unit",
      is_single: true,
    },

    { type: "section", label: "Label" },
    {
      value: "font_label",
      label: "Font family",
      default: theme.font,
      type: "customselect",
      options: fonts,
      is_single: true,
    },
    {
      value: "fontsize_label",
      label: "Label font size",
      default: theme.posts.fontsize_label,
      type: "unit",
      is_single: true,
    },

    { type: "section", label: "Author" },
    {
      value: "font_author",
      label: "Font family",
      default: theme.font,
      type: "customselect",
      options: fonts,
      is_single: true,
    },
    {
      value: "fontsize_author",
      label: "Font size",
      default: theme.posts.fontsize_author,
      type: "unit",
      is_single: true,
    },

    { type: "section", label: "Button/link" },
    {
      value: "font_button",
      label: "Font family",
      default: theme.font,
      type: "customselect",
      options: fonts,
      is_single: true,
    },
    {
      value: "fontsize_button",
      label: "Font size",
      default: theme.posts.fontsize_button,
      type: "unit",
      is_single: true,
    },
  ];

  const spacingSettings = [
    {
      value: "padding",
      label: "Padding",
      default: theme.posts.padding,
      type: "boxcontrol",
    },
    {
      value: "margin",
      label: "Margin",
      default: theme.posts.margin,
      type: "boxcontrol",
    },
  ];

  return (
    <>
      <ToolsPanel
        label={__("Colors")}
        resetAll={resetColors}
        hasInnerWrapper={true}
        className="color-block-support-panel"
      >
        <ColorSettingsPane
          attributes={attributes}
          setAttributes={setAttributes}
          colors={colors}
        />
      </ToolsPanel>

      <ToolsPanel label={__("Typography")} resetAll={resetTypography}>
        <SettingsPane
          attributes={attributes}
          setAttributes={setAttributes}
          settings={typographySettings}
        />
      </ToolsPanel>

      <ToolsPanel label={__("Spacing")} resetAll={resetSpacing}>
        <SettingsPane
          attributes={attributes}
          setAttributes={setAttributes}
          settings={spacingSettings}
        />
      </ToolsPanel>
    </>
  );
};
