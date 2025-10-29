import React from "react";

import apiFetch from "@wordpress/api-fetch";
import {
  BlockControls,
  InspectorControls,
  useBlockProps,
} from "@wordpress/block-editor";
import { ToolbarButton, ToolbarGroup } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { edit } from "@wordpress/icons";
import { View } from "@wordpress/primitives";

import { theme } from "../../defaults/theme.js";
import { Controls } from "./controls.js";
import { embedContentIcon } from "./icons";
import { MobileControls } from "./mobile-controls.js";

import { fallback } from "./util";

import EmbedLoading from "./embed-loading";
import EmbedPlaceholder from "./embed-placeholder";
import providers from "./providers";

export default function Edit({
  attributes,
  setAttributes,
  className,
  isSelected,
  onReplace,
  clientId,
  onFocus,
}) {
  var color = attributes.color ? attributes.color : theme.color;

  const { deviceType } = useSelect((select) => {
    const { getDeviceType } = select("core/editor")
      ? select("core/editor")
      : select("core/edit-site");
    return { deviceType: getDeviceType() };
  }, []);

  const isMobile = deviceType === "Mobile";

  const { content, cannotEmbed } = attributes;

  const defaultEmbedInfo = {
    title: __("Embed", "newsletter-glue"),
    icon: embedContentIcon,
  };
  const { icon, title } = defaultEmbedInfo;

  const label = sprintf(__("%s URL"), title);

  const attributesUrl = attributes.url;

  const [fetching, setFetching] = useState(false);
  const [url, setURL] = useState(attributesUrl);
  const [isEditingURL, setIsEditingURL] = useState(false);

  var attrs = {
    width: "100%",
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
  };

  const blockProps = useBlockProps(attrs);

  var tdStyle = {
    fontSize: isMobile ? attributes.mobile_size : attributes.fontsize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: isMobile ? attributes.mobile_lineheight : attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: isMobile
      ? attributes.mobile_padding.top
      : attributes.padding.top,
    paddingBottom: isMobile
      ? attributes.mobile_padding.bottom
      : attributes.padding.bottom,
    paddingLeft: isMobile
      ? attributes.mobile_padding.left
      : attributes.padding.left,
    paddingRight: isMobile
      ? attributes.mobile_padding.right
      : attributes.padding.right,
    textAlign: attributes.align,
    color: color,
    backgroundColor: attributes.background,
    borderRadius: "5px",
  };

  if (attributes.border) {
    tdStyle["border"] = "1px solid " + attributes.border;
  }

  if (fetching) {
    return (
      <View {...blockProps}>
        <EmbedLoading />
      </View>
    );
  }

  const showEmbedPlaceholder = !attributes.content || isEditingURL;

  if (showEmbedPlaceholder) {
    return (
      <View {...blockProps}>
        <EmbedPlaceholder
          icon={icon}
          label={label}
          onFocus={onFocus}
          onSubmit={(event) => {
            if (event) {
              event.preventDefault();
            }
            if (!url) {
              return false;
            }
            setIsEditingURL(false);
            setAttributes({ url });
            setFetching(true);
            embedURL(attributes, url);
          }}
          value={url}
          cannotEmbed={cannotEmbed}
          onChange={(event) => {
            setURL(event.target.value);
          }}
          fallback={() => {
            fallback(url, onReplace);
          }}
          tryAgain={() => {
            setIsEditingURL(false);
            setFetching(true);
            embedURL(attributes, url);
          }}
        />
      </View>
    );
  }

  function embedURL(attributes, url) {
    const data = {
      url: url,
    };

    apiFetch({
      path: "newsletterglue/" + nglue_backend.api_version + "/embed_url",
      method: "post",
      data: data,
      headers: {
        "NEWSLETTERGLUE-API-KEY": nglue_backend.api_key,
      },
    }).then((response) => {
      console.log(response);
      if (response.error) {
        setAttributes({ cannotEmbed: true, content: "" });
        setFetching(false);
      }
      if (response.content) {
        var provider = response.provider;
        var props = {
          cannotEmbed: false,
          content: response.content,
          provider: provider,
        };
        Object.keys(providers).map((name) => {
          if (providers[name] && name === provider) {
            Object.keys(providers[name]).map((attr) => {
              props[attr] = providers[name][attr];
            });
          }
        });
        setAttributes(props);
        setFetching(false);
      }
    });
  }

  const showEditButton = attributes.content ? true : false;

  var margin = isMobile ? attributes.mobile_margin : attributes.margin;

  let colspan = 1;
  if (margin.left) {
    colspan = colspan + 1;
  }

  if (margin.right) {
    colspan = colspan + 1;
  }

  return (
    <>
      <BlockControls>
        <ToolbarGroup>
          {showEditButton && (
            <ToolbarButton
              className="components-toolbar__control"
              label={__("Edit URL")}
              icon={edit}
              onClick={() => setIsEditingURL(true)}
            />
          )}
        </ToolbarGroup>
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

      <table {...blockProps}>
        <tbody>
          {margin && margin.top && (
            <tr>
              <td
                className="ng-block-vs ng-block-vs-1"
                style={{ height: margin.top }}
                height={parseInt(margin.top, 10)}
                colSpan={colspan}
              ></td>
            </tr>
          )}
          <tr>
            {margin && margin.left && (
              <td
                className="ng-block-hs ng-block-hs-1"
                style={{ width: margin.left }}
                height={parseInt(margin.left, 10)}
              ></td>
            )}
            <td className="ng-block-td" style={tdStyle}>
              <div dangerouslySetInnerHTML={{ __html: content }} />
            </td>
            {margin && margin.right && (
              <td
                className="ng-block-hs ng-block-hs-2"
                style={{ width: margin.right }}
                height={parseInt(margin.right, 10)}
              ></td>
            )}
          </tr>
          {margin && margin.bottom && (
            <tr>
              <td
                className="ng-block-vs ng-block-vs-2"
                style={{ height: margin.bottom }}
                height={parseInt(margin.bottom, 10)}
                colSpan={colspan}
              ></td>
            </tr>
          )}
        </tbody>
      </table>
    </>
  );
}
