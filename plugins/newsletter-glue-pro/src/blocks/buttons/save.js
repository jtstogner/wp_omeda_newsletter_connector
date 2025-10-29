import React from 'react';

/**
 * WordPress dependencies
 */
import classnames from 'classnames';

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function save({ attributes, className }) {

  const { fontSize, style } = attributes;

  const blockGap = attributes.spacing ? attributes.spacing : '20px';
  const justify = attributes.justify ? attributes.justify : 'left';

  const blockProps = useBlockProps.save({
    style: {
      gap: blockGap,
      justifyContent: justify,
    },
    className: classnames(className, {
      'has-custom-font-size': fontSize || style?.typography?.fontSize,
    }),
  });

  const innerBlocksProps = useInnerBlocksProps.save(blockProps);

  const tdStyle = {
    paddingTop: attributes.padding.top,
    paddingBottom: attributes.padding.bottom,
    paddingLeft: attributes.padding.left,
    paddingRight: attributes.padding.right,
  }

  return <table width="100%" cellPadding="0" cellSpacing="0" className="ng-block">
    <tbody>
      <tr>
        <td style={tdStyle}>
          <div {...innerBlocksProps} />
        </td>
      </tr>
    </tbody>
  </table>
}