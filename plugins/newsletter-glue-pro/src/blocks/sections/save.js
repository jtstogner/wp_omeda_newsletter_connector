import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';
import React from 'react';

export default function save({ attributes }) {

  const classes = classnames({
    'ng-block': true,
    'is-stacked-on-mobile': attributes.stacked_on_mobile
  });

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: classes,
    bgcolor: attributes.background,
    style: {
      backgroundColor: attributes.background,
    }
  };

  const blockProps = useBlockProps.save(attrs);

  const tdStyle = {
    paddingTop: attributes.padding.top,
    paddingBottom: attributes.padding.bottom,
    paddingLeft: attributes.padding.left,
    paddingRight: attributes.padding.right,
  }

  const { children } = useInnerBlocksProps.save([]);

  return (
    <>
      <table {...blockProps}>
        <tbody>
          {attributes.layout && (
            <tr>
              <td className="ng-columns-wrap" style={tdStyle}>
                <table width="100%" cellPadding="0" cellSpacing="0">
                  <tbody>
                    <tr>
                      {children}
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </>
  );

}