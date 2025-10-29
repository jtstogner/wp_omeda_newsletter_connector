import { useBlockProps } from '@wordpress/block-editor';
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
    paddingTop: attributes.padding.top,
    paddingBottom: attributes.padding.bottom,
    paddingLeft: attributes.padding.left,
    paddingRight: attributes.padding.right,
    color: color,
  }

  const hrStyle = {
    backgroundColor: 'transparent',
    color: 'transparent',
    margin: 0,
    border: 0,
    borderTop: `${attributes.height} solid ${color}`,
    width: attributes.width,
    height: 0,
  };

  return (
    <>
      <table {...blockProps}>
        <tbody>
          <tr>
            <td className="ng-block-td" align={attributes.align} style={tdStyle}>
              <hr style={hrStyle} />
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );

}