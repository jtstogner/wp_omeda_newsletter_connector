import React from 'react';

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

import { theme } from '../../defaults/theme.js';

export default function save({ attributes }) {

  const color = attributes.color ? attributes.color : theme.color;

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
    style: {
      color: color,
    }
  };

  const blockProps = useBlockProps.save(attrs);

  var tdStyle = {
    fontSize: attributes.fontsize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: attributes.padding.top,
    paddingBottom: attributes.padding.bottom,
    paddingLeft: attributes.padding.left,
    paddingRight: attributes.padding.right,
    textAlign: attributes.align,
    color: color,
    backgroundColor: attributes.background,
    borderRadius: attributes.radius ? attributes.radius : '0px',
  }

  if (attributes.borderSize) {
    tdStyle['borderWidth'] = attributes.borderSize;
    tdStyle['borderStyle'] = 'solid',
      tdStyle['borderColor'] = attributes.border ? attributes.border : attributes.background;
  }

  const margin = attributes.margin;

  let colspan = 1;
  if (margin.left) {
    colspan = colspan + 1;
  }

  if (margin.right) {
    colspan = colspan + 1;
  }

  return (
    <>
      <table {...blockProps}>
        <tbody>
          {margin && margin.top && (
            <tr><td className="ng-block-vs ng-block-vs-1" style={{ height: margin.top }} height={parseInt(margin.top, 10)} colSpan={colspan}></td></tr>
          )}
          <tr>
            {margin && margin.left && (
              <td className="ng-block-hs ng-block-hs-1" style={{ width: margin.left }} height={parseInt(margin.left, 10)}></td>
            )}
            <td className="ng-block-td" align={attributes.align} style={tdStyle}>
              <InnerBlocks.Content />
            </td>
            {margin && margin.right && (
              <td className="ng-block-hs ng-block-hs-2" style={{ width: margin.right }} height={parseInt(margin.right, 10)}></td>
            )}
          </tr>
          {margin && margin.bottom && (
            <tr><td className="ng-block-vs ng-block-vs-2" style={{ height: margin.bottom }} height={parseInt(margin.bottom, 10)} colSpan={colspan}></td></tr>
          )}
        </tbody>
      </table>
    </>
  );

}