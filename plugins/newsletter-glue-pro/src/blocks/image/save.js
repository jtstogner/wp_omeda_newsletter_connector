import React from 'react';

/**
 * External dependencies
 */
import classnames from 'classnames';
import { isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
import {
  __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
  RichText,
  useBlockProps,
} from '@wordpress/block-editor';

import { theme } from '../../defaults/theme.js';

export default function save({ attributes }) {
  const {
    url,
    alt,
    caption,
    align,
    href,
    rel,
    linkClass,
    width,
    height,
    id,
    linkTarget,
    sizeSlug,
    title,
  } = attributes;

  const newRel = isEmpty(rel) ? undefined : rel;
  const borderProps = getBorderClassesAndStyles(attributes);

  const classes = classnames({
    'ng-block': true,
    [`align${align}`]: align,
    [`size-${sizeSlug}`]: sizeSlug,
    'is-resized': width || height,
    'has-custom-border':
      !!borderProps.className || !isEmpty(borderProps.style),
  });

  const imageClasses = classnames(borderProps.className, {
    [`wp-image-${id}`]: !!id,
    'ng-image': true,
    'ng-mobile-keep-size': attributes.mobile_keep_size,
  });

  let imageStyles = {
    borderWidth: attributes.borderSize ? attributes.borderSize : undefined,
    borderStyle: attributes.borderSize ? 'solid' : 'none',
    borderColor: attributes.borderSize ? attributes.border : 'transparent',
    borderRadius: attributes.radius ? attributes.radius : undefined,
    boxSizing: 'border-box',
  };

  const image = (
    <img
      src={url}
      alt={alt}
      className={imageClasses || undefined}
      style={imageStyles}
      width={width}
      height={height}
      title={title}
    />
  );

  const figure = (
    <>
      {href ? (
        <a
          className={linkClass}
          href={href}
          target={linkTarget}
          rel={newRel}
        >
          {image}
        </a>
      ) : (
        image
      )}
    </>
  );

  const imageAlign = ['center', 'right', 'left'].includes(align) ? align : 'center';

  const color = attributes.color ? attributes.color : theme.color;

  const captionStyle = {
    color: color,
    fontSize: attributes.fontsize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    fontWeight: attributes.fontweight.key,
  };

  const bottom = attributes.padding.bottom;

  let tdStyle;
  let tdCaptionStyle;
  tdStyle = {
    paddingTop: attributes.padding.top,
    paddingBottom: bottom,
    paddingLeft: attributes.padding.left,
    paddingRight: attributes.padding.right,
  };

  if (caption) {
    tdStyle.paddingBottom = 0;
    tdCaptionStyle = {
      paddingLeft: attributes.padding.left,
      paddingRight: attributes.padding.right,
      paddingBottom: bottom,
      lineHeight: 1.5,
      fontSize: attributes.fontsize,
      fontFamily: nglue_backend.font_names[attributes.font.key],
    }
  }

  return (
    <table {...useBlockProps.save({
      width: '100%',
      cellPadding: 0,
      cellSpacing: 0,
      className: classes,
      bgcolor: attributes.background,
      style: { backgroundColor: attributes.background }
    })}>
      <tbody>
        <tr>
          <td className="ng-block-td" align={imageAlign} style={tdStyle}>
            {figure}
          </td>
        </tr>
        {!RichText.isEmpty(caption) && (
          <tr>
            <td className="ng-block-caption" align={imageAlign} style={tdCaptionStyle}>
              <RichText.Content
                tagName="span"
                value={caption}
                style={captionStyle}
              />
            </td>
          </tr>
        )}
      </tbody>
    </table>
  );
}
