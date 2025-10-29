import React from 'react';

import { RichText, useBlockProps } from '@wordpress/block-editor';

import { theme } from '../../defaults/theme.js';

export default function save({ attributes }) {

  const font_key = attributes[`h${attributes.level}_font`].key;

  const color = attributes[`h${attributes.level}_colour`] ? attributes[`h${attributes.level}_colour`] : theme.headings[`h${attributes.level}`].color;

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block ng-block-h${attributes.level}`,
    bgcolor: attributes.background,
    style: {
      color: color,
      backgroundColor: attributes.background,
    }
  };

  const blockProps = useBlockProps.save(attrs);

  const tdStyle = {
    fontSize: attributes[`h${attributes.level}_size`],
    fontFamily: nglue_backend.font_names[font_key],
    lineHeight: attributes[`h${attributes.level}_lineheight`],
    fontWeight: attributes.fontweight.key,
    paddingTop: attributes[`h${attributes.level}_padding`].top,
    paddingBottom: attributes[`h${attributes.level}_padding`].bottom,
    paddingLeft: attributes[`h${attributes.level}_padding`].left,
    paddingRight: attributes[`h${attributes.level}_padding`].right,
    textAlign: attributes.textAlign,
    color: color,
  }

  const elStyle = {
    fontSize: attributes[`h${attributes.level}_size`],
    fontFamily: nglue_backend.font_names[font_key],
    lineHeight: attributes[`h${attributes.level}_lineheight`],
    fontWeight: attributes.fontweight.key,
    textAlign: attributes.textAlign,
    color: color,
  }

  const tagName = 'h' + attributes.level;

  return (
    <>
      <table {...blockProps}>
        <tbody>
          <tr>
            <td className="ng-block-td" align={attributes.textAlign} style={tdStyle} >
              <RichText.Content tagName={tagName} value={attributes.content} style={elStyle} />
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );

}