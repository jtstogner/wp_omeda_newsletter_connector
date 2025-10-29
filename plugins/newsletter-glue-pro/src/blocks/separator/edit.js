import React from 'react';

import { AlignmentControl, BlockControls, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';

import { theme } from '../../defaults/theme.js';
import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

export default function Edit({ attributes, setAttributes, className, isSelected, clientId }) {

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  var color = attributes.color ? attributes.color : theme.color;

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
    bgcolor: attributes.background,
    style: {
      color: color,
      backgroundColor: attributes.background,
    },
  };

  const blockProps = useBlockProps(attrs);

  const tdStyle = {
    paddingTop: isMobile ? attributes.mobile_padding.top : attributes.padding.top,
    paddingBottom: isMobile ? attributes.mobile_padding.bottom : attributes.padding.bottom,
    paddingLeft: isMobile ? attributes.mobile_padding.left : attributes.padding.left,
    paddingRight: isMobile ? attributes.mobile_padding.right : attributes.padding.right,
    color: color,
  }

  let hrWidth = isMobile ? attributes.mobile_width : attributes.width;
  let hrHeight = isMobile ? attributes.mobile_height : attributes.height;

  const hrStyle = {
    backgroundColor: 'transparent',
    color: 'transparent',
    margin: 0,
    border: 0,
    borderTop: `${hrHeight} solid ${color}`,
    width: hrWidth,
    height: 0,
  };

  return (
    <>
      <BlockControls group="block">
        <AlignmentControl
          value={isMobile ? attributes.mobile_align : attributes.align}
          onChange={(nextAlign) => {
            if (isMobile) {
              setAttributes({ mobile_align: nextAlign === undefined ? 'none' : nextAlign });
            } else {
              setAttributes({ align: nextAlign === undefined ? 'none' : nextAlign });
            }
          }}
        />
      </BlockControls>

      <InspectorControls>
        {deviceType !== 'Mobile' &&
          <Controls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
        {deviceType === 'Mobile' &&
          <MobileControls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
      </InspectorControls>

      <table {...blockProps}>
        <tbody>
          <tr>
            <td className="ng-block-td" align={isMobile ? attributes.mobile_align : attributes.align} style={tdStyle}>
              <hr style={hrStyle} />
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );

}