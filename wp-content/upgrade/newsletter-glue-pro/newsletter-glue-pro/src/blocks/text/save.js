import { RichText, useBlockProps } from '@wordpress/block-editor';
import React from 'react';

import { theme } from '../../defaults/theme.js';

export default function save({ attributes }) {

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

  let fontSize = attributes.fontsize;
  if (typeof fontSize === 'string' && !fontSize.includes('px')) {
    fontSize = fontSize + 'px';
  }

  const tdStyle = {
    fontSize: fontSize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: attributes.padding.top,
    paddingBottom: attributes.padding.bottom,
    paddingLeft: attributes.padding.left,
    paddingRight: attributes.padding.right,
    textAlign: attributes.align,
    color: color,
  }

  return (
    <>
      <table {...blockProps}>
        <tbody>
          <tr>
            <td className="ng-block-td" align={attributes.align} style={tdStyle}>
              <RichText.Content tagName="p" value={attributes.content} />
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );

}