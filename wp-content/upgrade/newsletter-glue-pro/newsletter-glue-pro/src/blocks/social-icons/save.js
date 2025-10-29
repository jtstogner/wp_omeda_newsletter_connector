import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
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

  const tdStyle = {
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
  }

  return (
    <>
      <table {...blockProps}>
        <tbody>
          <tr>
            <td className="ng-block-td" align={attributes.align} style={tdStyle}>
              {attributes.add_description && (<RichText.Content
                tagName="div"
                className="ngl-share-description ng-div"
                value={attributes.description}
                style={{ color: color, marginBottom: '8px' }}
              />)}
              <div className="ngl-share-wrap ng-div" style={{ lineHeight: 1, fontSize: '1px' }}>
                <InnerBlocks.Content />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );

}