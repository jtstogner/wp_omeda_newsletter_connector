import React from "react";

import {
  AlignmentControl,
  BlockControls,
  InspectorControls,
  RichText,
  useBlockProps,
} from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { __ } from "@wordpress/i18n";

import { theme } from "../../defaults/theme.js";
import { Controls } from "./controls.js";
import { MobileControls } from "./mobile-controls.js";
import { useOnEnter } from "./use-enter";

export default function Edit({
  attributes,
  setAttributes,
  className,
  isSelected,
  onReplace,
  onRemove,
  mergeBlocks,
  clientId,
}) {
  const { deviceType } = useSelect((select) => {
    const { getDeviceType } = select("core/editor")
      ? select("core/editor")
      : select("core/edit-site");
    return { deviceType: getDeviceType() };
  }, []);

  const isMobile = deviceType === "Mobile";

  var color = attributes.color ? attributes.color : theme.color;

  const { content } = attributes;

  var attrs = {
    ref: useOnEnter({ clientId, content }),
    width: "100%",
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
    bgcolor: attributes.background,
    style: {
      color: color,
      backgroundColor: attributes.background,
    },
  };

  const blockProps = useBlockProps(attrs);

  let fontSize = isMobile ? attributes.mobile_p_size : attributes.fontsize;
  if (typeof fontSize === "string" && !fontSize.includes("px")) {
    fontSize = fontSize + "px";
  }

  const tdStyle = {
    fontSize: fontSize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: isMobile
      ? attributes.mobile_p_lineheight
      : attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: isMobile
      ? attributes.mobile_p_padding.top
      : attributes.padding.top,
    paddingBottom: isMobile
      ? attributes.mobile_p_padding.bottom
      : attributes.padding.bottom,
    paddingLeft: isMobile
      ? attributes.mobile_p_padding.left
      : attributes.padding.left,
    paddingRight: isMobile
      ? attributes.mobile_p_padding.right
      : attributes.padding.right,
    textAlign: attributes.align,
    color: color,
  };

  return (
    <>
      <BlockControls group="block">
        <AlignmentControl
          value={attributes.align}
          onChange={(nextAlign) => {
            setAttributes({
              align: nextAlign === undefined ? "none" : nextAlign,
            });
          }}
        />
      </BlockControls>

      <InspectorControls>
        {deviceType !== "Mobile" && (
          <Controls
            attributes={attributes}
            setAttributes={setAttributes}
            className={className}
            isSelected={isSelected}
            clientId={clientId}
          />
        )}
        {deviceType === "Mobile" && (
          <MobileControls
            attributes={attributes}
            setAttributes={setAttributes}
            className={className}
            isSelected={isSelected}
            clientId={clientId}
          />
        )}
      </InspectorControls>

      <table {...blockProps} data-empty={!attributes.content ? true : false}>
        <tbody>
          <tr>
            <td
              className="ng-block-td"
              align={attributes.align}
              style={tdStyle}
            >
              <RichText
                identifier="content"
                tagName="p"
                value={attributes.content}
                onChange={(content) => setAttributes({ content })}
                onMerge={mergeBlocks}
                onReplace={onReplace}
                onRemove={onRemove}
                data-empty={!attributes.content ? true : false}
                placeholder={
                  attributes.placeholder ||
                  __("Type / to choose a block", "newsletter-glue")
                }
                data-custom-placeholder={
                  attributes.placeholder ? true : undefined
                }
                __unstableEmbedURLOnPaste
                __unstableAllowPrefixTransformations
              />
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );
}
