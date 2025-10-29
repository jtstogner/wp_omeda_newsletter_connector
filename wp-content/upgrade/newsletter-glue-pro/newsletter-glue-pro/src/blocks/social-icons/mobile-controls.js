import {
  BaseControl,
  PanelBody,
  __experimentalToolsPanel as ToolsPanel,
  __experimentalUnitControl as UnitControl,
} from "@wordpress/components";

import { __ } from "@wordpress/i18n";
import React from "react";

import { SettingsPane } from "../../components/settings.js";

import { theme } from "../../defaults/theme.js";
import { units } from "../../defaults/units.js";

export const MobileControls = (props) => {
  const { attributes, setAttributes, clientId } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_lineheight: theme.mobile.share.lineheight,
      mobile_size: theme.mobile.share.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_padding: theme.mobile.share.padding,
    });
  };

  const typographySettings = [
    {
      value: "mobile_size",
      label: "Font size",
      default: theme.mobile.share.fontsize,
      type: "unit",
    },
    {
      value: "mobile_lineheight",
      label: "Line height",
      default: theme.mobile.share.lineheight,
      type: "number",
      step: 0.1,
      is_single: true,
    },
  ];

  const spacingSettings = [
    {
      value: "mobile_padding",
      label: "Padding",
      default: theme.mobile.share.padding,
      type: "boxcontrol",
    },
  ];

  function onChangeSize(value) {
    setAttributes({ mobile_icon_size: value });
    var children = wp.data
      .select("core/block-editor")
      .getBlocksByClientId(clientId)[0].innerBlocks;
    children.forEach(function (child) {
      wp.data
        .dispatch("core/block-editor")
        .updateBlockAttributes(child.clientId, { mobile_icon_size: value });
    });
  }

  function onChangeGap(value) {
    setAttributes({ mobile_gap: value });
    var children = wp.data
      .select("core/block-editor")
      .getBlocksByClientId(clientId)[0].innerBlocks;
    children.forEach(function (child) {
      wp.data
        .dispatch("core/block-editor")
        .updateBlockAttributes(child.clientId, { mobile_gap: value });
    });
  }

  return (
    <>
      {attributes.add_description && (
        <ToolsPanel label={__("Description")} resetAll={resetTypography}>
          <SettingsPane
            attributes={attributes}
            setAttributes={setAttributes}
            settings={typographySettings}
          />
        </ToolsPanel>
      )}

      <PanelBody title={__("Icons")}>
        <BaseControl>
          <UnitControl
            label={__("Icon size")}
            value={attributes.mobile_icon_size}
            onChange={onChangeSize}
            units={units}
          />
        </BaseControl>
        <BaseControl>
          <UnitControl
            label={__("Spacing between icons")}
            value={attributes.mobile_gap}
            onChange={onChangeGap}
            units={units}
          />
        </BaseControl>
      </PanelBody>

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
