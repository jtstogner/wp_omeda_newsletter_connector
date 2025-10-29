import React from 'react';

/**
 * WordPress dependencies
 */
import {
  BlockControls,
  InspectorControls,
  JustifyToolbar,
  useBlockProps,
  useInnerBlocksProps
} from '@wordpress/block-editor';

import { useSelect } from '@wordpress/data';

import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

/**
 * Internal dependencies
 */
const buttonBlockName = 'newsletterglue/button';

const ALLOWED_BLOCKS = [buttonBlockName];

const DEFAULT_BLOCK = {
  name: buttonBlockName,
  attributesToCopy: [
    'backgroundColor',
    'border',
    'className',
    'fontFamily',
    'fontSize',
    'gradient',
    'style',
    'textColor',
    'width',
  ],
};

function ButtonsEdit({ attributes, setAttributes, clientId, className, isSelected }) {

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  let justify;
  justify = attributes.justify ? attributes.justify : 'left';

  if (isMobile) {
    if (attributes.mobile_justify) {
      justify = attributes.mobile_justify;
    }
  }

  const controls = (
    <>
      <BlockControls group="block">
        <JustifyToolbar
          value={isMobile && attributes.mobile_justify ? attributes.mobile_justify : attributes.justify}
          onChange={(nextJustify) => {
            var key = isMobile ? 'mobile_justify' : 'justify';
            setAttributes({ [key]: nextJustify });
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
    </>
  );

  const blockGap = attributes.spacing ? attributes.spacing : '20px';

  const blockProps = useBlockProps({
    style: {
      gap: blockGap,
      justifyContent: justify,
    },
  });

  const innerBlocksProps = useInnerBlocksProps(blockProps, {
    allowedBlocks: ALLOWED_BLOCKS,
    defaultBlock: DEFAULT_BLOCK,
    directInsert: true,
    template: [
      [
        buttonBlockName,
        { className: '' },
      ],
    ],
    templateInsertUpdatesSelection: true,
    orientation: 'horizontal',
  });

  const tdStyle = {
    paddingTop: isMobile ? attributes.mobile_padding.top : attributes.padding.top,
    paddingBottom: isMobile ? attributes.mobile_padding.bottom : attributes.padding.bottom,
    paddingLeft: isMobile ? attributes.mobile_padding.left : attributes.padding.left,
    paddingRight: isMobile ? attributes.mobile_padding.right : attributes.padding.right,
  }

  return (
    <>
      <table width="100%" cellPadding="0" cellSpacing="0" className="ng-block">
        <tbody>
          <tr>
            <td style={tdStyle}>
              <div {...innerBlocksProps} />
            </td>
          </tr>
        </tbody>
      </table>
      {controls}
    </>
  );
}

export default ButtonsEdit;