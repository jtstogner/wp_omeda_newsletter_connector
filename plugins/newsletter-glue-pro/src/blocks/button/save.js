import React from 'react';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
  RichText,
  useBlockProps,
} from '@wordpress/block-editor';

import { theme } from '../../defaults/theme.js';

export default function save({ attributes, className }) {
  const {
    linkTarget,
    rel,
    text,
    title,
    url,
  } = attributes;

  if (!text) {
    return null;
  }

  const buttonClasses = classnames(
    'ng-block-button__link',
  );

  const isMobile = false;

  let buttonWidth;
  if (attributes.width === 'custom') {
    buttonWidth = attributes.custom_width ? attributes.custom_width : theme.button.width + 'px';
  } else {
    buttonWidth = 'auto';
  }
  if (attributes.width === 'full') {
    buttonWidth = '100%';
  }

  let mbuttonWidth;
  if (isMobile) {
    if (attributes.mobile_custom_width) {
      mbuttonWidth = attributes.mobile_custom_width ? attributes.mobile_custom_width : buttonWidth;
    }
  }

  const defaultButtonBg = attributes.buttonstyle === 'filled' ? theme.button.bg : '#ffffff';
  const defaultButtonColor = attributes.buttonstyle === 'filled' ? theme.button.color : theme.button.bg;
  const defaultBorder = attributes.buttonstyle === 'filled' ? (attributes.background ? attributes.background : defaultButtonBg) : (attributes.color ? attributes.color : defaultButtonColor);
  const defaultBorderSize = '2px';

  let radius = attributes.radius ? attributes.radius : '0px';
  radius = parseInt(radius) + 'px';

  const buttonStyle = {
    fontFamily: nglue_backend.font_names[attributes.font.key],
    fontSize: isMobile ? attributes.mobile_fontsize : attributes.fontsize,
    lineHeight: isMobile ? attributes.mobile_lineheight : attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: isMobile ? attributes.mobile_padding.top : attributes.padding.top,
    paddingBottom: isMobile ? attributes.mobile_padding.bottom : attributes.padding.bottom,
    paddingLeft: isMobile ? attributes.mobile_padding.left : attributes.padding.left,
    paddingRight: isMobile ? attributes.mobile_padding.right : attributes.padding.right,
    textAlign: attributes.textAlign ? attributes.textAlign : 'center',
    width: isMobile && mbuttonWidth ? mbuttonWidth : buttonWidth,
    backgroundColor: attributes.background ? attributes.background : defaultButtonBg,
    color: attributes.color ? attributes.color : defaultButtonColor,
    borderWidth: attributes.borderSize ? attributes.borderSize : defaultBorderSize,
    borderStyle: 'solid',
    borderColor: attributes.border ? attributes.border : defaultBorder,
    borderRadius: radius,
    boxSizing: 'border-box',
  }

  // The use of a `title` attribute here is soft-deprecated, but still applied
  // if it had already been assigned, for the sake of backward-compatibility.
  // A title will no longer be assigned for new or updated button block links.

  const wrapperClasses = classnames(className, {

  });

  return (
    <div
      {...useBlockProps.save({ className: wrapperClasses })}
      style={{
        flexBasis: buttonWidth,
      }}
    >
      <RichText.Content
        tagName="a"
        className={buttonClasses}
        href={url}
        title={title}
        style={buttonStyle}
        value={text}
        target={linkTarget}
        rel={rel}
      />
    </div>
  );
}
