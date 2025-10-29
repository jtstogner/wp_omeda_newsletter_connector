import React from 'react';

import { useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
    bgcolor: attributes.background,
    style: {
      backgroundColor: attributes.background,
    }
  };

  const blockProps = useBlockProps.save(attrs);

  const tdStyle = {
    height: attributes.height
  }

  return (
    <>
      <table {...blockProps}>
        <tbody>
          <tr>
            <td className="ng-block-td" height={parseInt(attributes.height)} style={tdStyle}></td>
          </tr>
        </tbody>
      </table>
    </>
  );

}