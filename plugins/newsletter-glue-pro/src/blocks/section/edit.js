import React from 'react';

import classnames from 'classnames';

import { BlockControls, BlockVerticalAlignmentToolbar, InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { dispatch, select, useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

import { allowedBlocks } from './allowed-blocks.js';

export default function Edit({ attributes, setAttributes, className, isSelected, clientId }) {

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const classes = classnames({
    'ng-block': true,
    'ng-should-remove': !attributes.width
  });

  var attrs = {
    width: attributes.width ? attributes.width : 'auto',
    className: classes,
    valign: attributes.verticalAlign,
    style: {
      width: attributes.width ? attributes.width + 'px' : 'auto',
      paddingTop: attributes.padding.top,
      paddingBottom: attributes.padding.bottom,
      paddingLeft: attributes.padding.left,
      paddingRight: attributes.padding.right,
      display: !attributes.width ? 'none' : 'table-cell',
      verticalAlign: attributes.verticalAlign,
      backgroundColor: attributes.background,
    }
  };

  const blockTemplate = [
    ['newsletterglue/text', {}]
  ];

  const blockProps = useBlockProps(attrs);

  const innerBlocksProps = useInnerBlocksProps(blockProps, {
    allowedBlocks: allowedBlocks,
    template: blockTemplate,
    templateLock: false,
  });

  useEffect(() => {
    let newWidth = attributes.originalWidth;

    if (attributes.padding.left) {
      newWidth = newWidth - parseInt(attributes.padding.left);
    }

    if (attributes.padding.right) {
      newWidth = newWidth - parseInt(attributes.padding.right);
    }

    setAttributes({ width: newWidth });

  }, [attributes.padding]);

  useEffect(() => {
    var block = select('core/block-editor').getBlocksByClientId(clientId)[0];
    if (block && block.innerBlocks) {
      const children = select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks;
      children.forEach(function (child) {
        dispatch('core/block-editor').updateBlockAttributes(child.clientId, { threshold: attributes.width });
      });
    }
  }, [attributes.width]);

  return (
    <>
      <BlockControls group="block">
        <BlockVerticalAlignmentToolbar
          value={attributes.verticalAlign}
          onChange={(nextAlign) => {
            if (nextAlign == 'center') {
              nextAlign = 'middle';
            }
            setAttributes({ verticalAlign: nextAlign === undefined ? 'top' : nextAlign });
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

      <td {...innerBlocksProps} />
    </>
  );

}