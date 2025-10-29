import { __ } from "@wordpress/i18n";
import React, { useEffect, useState } from "react";

import { MediaUpload, RichText } from "@wordpress/block-editor";
import { Button } from "@wordpress/components";
import { useSelect } from "@wordpress/data";

import { rotateLeft } from "@wordpress/icons";

import { theme } from "../../defaults/theme.js";
import { calculateColumnWidth } from "./calculateColumnWidth";

export const LpEdit = (props) => {
  const { attributes, setAttributes, blockProps } = props;

  const changeIcon = (
    <svg
      stroke="currentColor"
      fill="none"
      strokeWidth="2"
      viewBox="0 0 24 24"
      strokeLinecap="round"
      strokeLinejoin="round"
      height="20"
      width="20"
      xmlns="http://www.w3.org/2000/svg"
    >
      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
      <circle cx="8.5" cy="8.5" r="1.5"></circle>
      <polyline points="21 15 16 10 5 21"></polyline>
    </svg>
  );

  const [focused, setFocused] = useState(0);
  const [activeId, setActiveId] = useState(0);

  const { deviceType } = useSelect((select) => {
    const { getDeviceType } = select("core/editor")
      ? select("core/editor")
      : select("core/edit-site");
    return { deviceType: getDeviceType() };
  }, []);

  const isMobile = deviceType === "Mobile";

  var color = attributes.text_color ? attributes.text_color : theme.color;

  useEffect(() => {}, []);

  useEffect(() => {
    if (
      !attributes.div1 ||
      !attributes.div2 ||
      !attributes.containerWidth ||
      !attributes.itemBase
    ) {
      calculateColumnWidth(attributes, setAttributes);
    }
  }, []);

  useEffect(() => {
    calculateColumnWidth(attributes, setAttributes);
  }, [
    attributes.image_position,
    attributes.table_ratio,
    attributes.columns_num,
    attributes.padding,
    attributes.margin,
  ]);

  useEffect(() => {}, [attributes.update_posts]);

  function checkForData(id, type) {
    let data = attributes.custom_data;
    for (var i = 0; i < data.length; i++) {
      if (data[i][type + "_" + id] && data[i][type + "_" + id] != null) {
        return data[i][type + "_" + id];
      }
    }
    return false;
  }

  function getExcerpt(item, excerpt) {
    var result = excerpt;
    if (attributes.words_num > 0 && checkForData(item.id, "excerpt")) {
      result = result.split(" ").splice(0, attributes.words_num).join(" ");
      result = result + "...";
    }
    return result;
  }

  const styles = {};

  styles["fontFamily"] = nglue_backend.font_names[attributes.font.key];

  if (attributes.background_color) {
    styles["backgroundColor"] = attributes.background_color;
  }

  styles["color"] = color;

  if (attributes.padding) {
    styles["paddingLeft"] = isMobile
      ? attributes.mobile_padding.left
      : attributes.padding.left;
    styles["paddingRight"] = isMobile
      ? attributes.mobile_padding.right
      : attributes.padding.right;
    styles["paddingTop"] = isMobile
      ? attributes.mobile_padding.top
      : attributes.padding.top;
    styles["paddingBottom"] = isMobile
      ? attributes.mobile_padding.bottom
      : attributes.padding.bottom;
  }

  if (attributes.margin) {
    styles["marginLeft"] = isMobile
      ? attributes.mobile_margin.left
      : attributes.margin.left;
    styles["marginRight"] = isMobile
      ? attributes.mobile_margin.right
      : attributes.margin.right;
    styles["marginTop"] = isMobile
      ? attributes.mobile_margin.top
      : attributes.margin.top;
    styles["marginBottom"] = isMobile
      ? attributes.mobile_margin.bottom
      : attributes.margin.bottom;
  }

  if (attributes.border_radius) {
    styles["borderRadius"] = attributes.border_radius + "px";
  }

  if (attributes.border_size) {
    styles["borderWidth"] = attributes.border_size + "px";
    styles["borderStyle"] = attributes.border_style
      ? attributes.border_style.value
      : "solid";
    styles["borderColor"] = attributes.border_color
      ? attributes.border_color
      : "transparent";
  }

  const titleStyles = {};
  titleStyles["fontFamily"] =
    nglue_backend.font_names[attributes.font_title.key];
  titleStyles["fontSize"] = isMobile
    ? attributes.mobile_fontsize_title
    : attributes.fontsize_title;
  if (attributes.title_color) {
    titleStyles["color"] = attributes.title_color;
  } else {
    titleStyles["color"] = theme.colors.h3;
  }

  const labelStyles = {};
  labelStyles["fontFamily"] =
    nglue_backend.font_names[attributes.font_label.key];
  labelStyles["fontSize"] = isMobile
    ? attributes.mobile_fontsize_label
    : attributes.fontsize_label;
  if (attributes.label_color) {
    labelStyles["color"] = attributes.label_color;
  }

  const authorStyles = {};
  authorStyles["fontFamily"] =
    nglue_backend.font_names[attributes.font_author.key];
  authorStyles["fontSize"] = isMobile
    ? attributes.mobile_fontsize_author
    : attributes.fontsize_author;
  if (attributes.author_color) {
    authorStyles["color"] = attributes.author_color;
  }

  const linkStyles = {};
  linkStyles["fontFamily"] =
    nglue_backend.font_names[attributes.font_button.key];
  linkStyles["fontSize"] = isMobile
    ? attributes.mobile_fontsize_button
    : attributes.fontsize_button;
  if (attributes.link) {
    if (attributes.cta_type == "link") {
      linkStyles["color"] = attributes.link;
    }
    if (attributes.cta_type == "button") {
      linkStyles["backgroundColor"] = attributes.button;
      linkStyles["color"] = attributes.button_text;
    }
  }

  let wrapperStyle = {};
  if (attributes.margin) {
    if (attributes.margin.top || attributes.margin.bottom) {
      wrapperStyle.padding = "1px 0";
    }
  }

  const { embeds } = attributes;

  const hasEmbeds = Object.keys(embeds).length > 0 ? true : false;

  function getItemTitle(item) {
    var entries = attributes.embeds;
    var key = item.key;
    var title = item.title;

    if (entries[key]["custom"]) {
      if (entries[key]["custom"]["title"]) {
        return entries[key]["custom"]["title"];
      }
    }

    return title;
  }

  function getItemLabel(item) {
    var entries = attributes.embeds;
    var key = item.key;

    var label = "";
    if (attributes.label_type == "domain") {
      label = item.domain;
    } else if (attributes.label_type == "category") {
      label = item.categories;
    } else if (attributes.label_type == "tag") {
      label = item.tags;
    } else if (attributes.label_type == "author") {
      label = item.author;
    }

    if (entries[key]["custom"]) {
      if (entries[key]["custom"]["label"]) {
        return entries[key]["custom"]["label"];
      }
    }

    return label;
  }

  function getItemAuthor(item) {
    var entries = attributes.embeds;
    var key = item.key;
    var author = item.author ? item.author : "";

    if (entries[key]["custom"]) {
      if (entries[key]["custom"]["author"]) {
        return entries[key]["custom"]["author"];
      }
    }

    return author;
  }

  function getItemImage(item) {
    var entries = attributes.embeds;
    var key = item.key;
    var image = item.image;

    if (entries[key]["custom"]) {
      if (entries[key]["custom"]["image"]) {
        return entries[key]["custom"]["image"];
      }
    }

    return image;
  }

  function getItemMore(item) {
    var entries = attributes.embeds;
    var more = attributes.cta_link;
    var key = item.key;

    if (entries[key]["custom"]) {
      if (entries[key]["custom"]["more"]) {
        return entries[key]["custom"]["more"];
      }
    }

    return more;
  }

  function getItemContent(item) {
    var content = item.content ? "<p>" + item.content + "</p>" : "";
    var entries = attributes.embeds;
    var key = item.key;

    if (entries[key]["custom"]) {
      if (entries[key]["custom"]["content"]) {
        return entries[key]["custom"]["content"];
      }
    }

    return content;
  }

  function updateCustomItem(i, attr, value) {
    setAttributes({
      embeds: {
        ...embeds,
        [i]: {
          ...embeds[i],
          custom: {
            ...embeds[i]["custom"],
            [attr]: value,
          },
        },
      },
    });
  }

  function hasCustomItem(i) {
    var entries = attributes.embeds;
    if (entries[i]["custom"]) {
      return true;
    }
    return false;
  }

  function resetItem(i) {
    setAttributes({
      embeds: {
        ...embeds,
        [i]: {
          ...embeds[i],
          custom: null,
        },
      },
    });
  }

  const divider_bg = attributes.divider_bg ? attributes.divider_bg : "#eeeeee";

  return (
    <div {...blockProps} style={wrapperStyle}>
      <div className="ngl-lp-items ngl-lp-items-blockeditor" style={styles}>
        {!hasEmbeds && (
          <div className="ngl-lp-noresults">
            You did not add any posts yet to this embed.
          </div>
        )}

        {hasEmbeds &&
          attributes.embeds_order.map(function (i) {
            var item = embeds[i];

            if (!item.enabled) {
              return null;
            }

            var postIsHidden =
              attributes.hidden_posts &&
              attributes.hidden_posts.includes(item.id);

            var postExcerpt = getItemContent(item);

            return (
              <div
                className={`ngl-lp-item-wrap ${
                  deviceType !== "Desktop" ? "on-mobile" : ""
                }`}
                key={i}
                style={{ flexBasis: attributes.itemBase }}
              >
                {hasCustomItem(i) && (
                  <div className="ng-reset-item">
                    <Button
                      variant="secondary"
                      icon={rotateLeft}
                      iconSize={20}
                      onClick={() => {
                        resetItem(i);
                      }}
                    >
                      {__("Reset")}
                    </Button>
                  </div>
                )}

                <div
                  className={`ngl-lp-item ${
                    deviceType != "Desktop" && attributes.stacked_on_mobile
                      ? "is-stacked"
                      : "is-not-stacked"
                  } ${
                    postIsHidden ? "ngl-lp-item-hidden" : "ngl-lp-item-visible"
                  }`}
                >
                  {attributes.show_image && (
                    <MediaUpload
                      multiple={false}
                      render={({ open }) => (
                        <div
                          className={`ngl-lp-image-edit${
                            checkForData(item.id, "image")
                              ? " ngl-lp-image-edit-hasImage"
                              : ""
                          }`}
                          style={{
                            flexBasis: attributes.div1,
                            padding: "2px 0",
                          }}
                        >
                          {postIsHidden && (
                            <div className="ngl-lp-data-hidden"></div>
                          )}
                          <div className="ngl-lp-image">
                            <img
                              src={getItemImage(item)}
                              alt={getItemTitle(item)}
                              style={{
                                borderRadius: attributes.image_radius + "px",
                              }}
                              onClick={open}
                            />

                            <Button
                              variant="link"
                              className="ngl-reset-mods ngl-reset-mods-edit"
                              icon={changeIcon}
                              iconSize={20}
                              onClick={open}
                            />
                          </div>
                        </div>
                      )}
                      onSelect={(media) => {
                        updateCustomItem(i, "image", media.url);
                      }}
                    />
                  )}

                  <div
                    className="ngl-lp-data"
                    style={{ flexBasis: attributes.div2 }}
                  >
                    {postIsHidden && <div className="ngl-lp-data-hidden"></div>}

                    {attributes.order.map((index) => {
                      if (index == 1 && attributes.show_label) {
                        return (
                          <div
                            key={index}
                            className="ngl-lp-labels-edit"
                            style={{ padding: "2px 0" }}
                          >
                            <RichText
                              tagName="div"
                              className="ngl-lp-labels"
                              style={labelStyles}
                              value={getItemLabel(item)}
                              placeholder={__(
                                "Add label...",
                                "newsletter-glue"
                              )}
                              allowedFormats={[
                                "core/bold",
                                "core/link",
                                "core/italic",
                              ]}
                              onChange={(value) => {
                                updateCustomItem(i, "label", value);
                              }}
                            />
                          </div>
                        );
                      }

                      if (index == 2 && attributes.show_author) {
                        return (
                          <div
                            key={index}
                            className="ngl-lp-labels-edit"
                            style={{ padding: "2px 0" }}
                          >
                            <RichText
                              tagName="div"
                              className="ngl-lp-labels"
                              style={authorStyles}
                              value={getItemAuthor(item)}
                              placeholder={__(
                                "Add author...",
                                "newsletter-glue"
                              )}
                              allowedFormats={[
                                "core/bold",
                                "core/link",
                                "core/italic",
                              ]}
                              onChange={(value) => {
                                updateCustomItem(i, "author", value);
                              }}
                            />
                          </div>
                        );
                      }

                      if (index == 3 && attributes.show_heading) {
                        return (
                          <div
                            key={index}
                            className="ngl-lp-title"
                            style={{ padding: "2px 0", lineHeight: 1.2 }}
                          >
                            <RichText
                              tagName="h3"
                              value={getItemTitle(item)}
                              placeholder={__(
                                "Add a heading",
                                "newsletter-glue"
                              )}
                              style={titleStyles}
                              allowedFormats={[
                                "core/bold",
                                "core/link",
                                "core/italic",
                              ]}
                              onChange={(value) =>
                                updateCustomItem(i, "title", value)
                              }
                            />
                          </div>
                        );
                      }

                      if (index == 4 && attributes.show_excerpt) {
                        return (
                          <div
                            key={index}
                            className="ngl-lp-content-edit"
                            style={{ padding: "2px 0" }}
                          >
                            <RichText
                              tagName="div"
                              className="ngl-lp-content"
                              style={{
                                fontFamily:
                                  nglue_backend.font_names[
                                    attributes.font_text.key
                                  ],
                                fontSize: isMobile
                                  ? attributes.mobile_fontsize_text
                                  : attributes.fontsize_text,
                                color: color,
                              }}
                              value={
                                focused === item.key
                                  ? postExcerpt
                                  : getExcerpt(item, postExcerpt)
                              }
                              onMouseEnter={() => {
                                setActiveId(item.key);
                              }}
                              onMouseLeave={() => {}}
                              onFocus={() => {
                                if (item.key == activeId) {
                                  setFocused(item.key);
                                }
                              }}
                              onBlur={() => {
                                return false;
                              }}
                              placeholder={__("Add text...", "newsletter-glue")}
                              allowedFormats={[
                                "core/bold",
                                "core/link",
                                "core/italic",
                                "core/image",
                              ]}
                              onChange={(value) => {
                                updateCustomItem(i, "content", value);
                              }}
                            />
                          </div>
                        );
                      }

                      if (index == 5 && attributes.show_cta) {
                        return (
                          <div
                            key={index}
                            className={`ngl-lp-cta ngl-lp-cta-${attributes.cta_type}`}
                            style={{ padding: "2px 0" }}
                          >
                            <RichText
                              className={`ngl-lp-cta-link${
                                attributes.cta_type == "button"
                                  ? " wp-block-button__link"
                                  : ""
                              }`}
                              multiline={false}
                              tagName="a"
                              style={linkStyles}
                              value={getItemMore(item)}
                              placeholder={__(
                                "Add link text...",
                                "newsletter-glue"
                              )}
                              allowedFormats={["core/bold", "core/italic"]}
                              onChange={(value) => {
                                updateCustomItem(i, "more", value);
                              }}
                            />
                          </div>
                        );
                      }
                    })}
                  </div>
                </div>
                {attributes.show_divider && (
                  <div
                    style={{
                      backgroundColor: divider_bg,
                      height: parseInt(attributes.divider_size) + "px",
                      margin: "10px 0",
                    }}
                  />
                )}
              </div>
            );
          })}
      </div>
    </div>
  );
};
