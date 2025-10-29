import React from 'react';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

import { theme } from '../../defaults/theme.js';

export default function save({ attributes }) {
  const { ordered, type, reversed, start } = attributes;
  const TagName = ordered ? 'ol' : 'ul';

  const color = attributes.color ? attributes.color : theme.color;

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
    bgcolor: attributes.background,
    style: {
      color: color,
      backgroundColor: attributes.background,
    }
  };

  const blockProps = useBlockProps.save(attrs);

  const tdStyle = {
    fontSize: attributes.fontsize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: attributes.padding.top,
    paddingBottom: attributes.padding.bottom,
    paddingLeft: attributes.padding.left,
    paddingRight: attributes.padding.right,
    color: color,
  }

  return (
    <table {...blockProps}>
      <tbody>
        <tr>
          <td className="ng-block-td" style={tdStyle}>
            <TagName {...useBlockProps.save({ type, reversed, start })}>
              <InnerBlocks.Content />
            </TagName>
          </td>
        </tr>
      </tbody>
    </table>
  );
}