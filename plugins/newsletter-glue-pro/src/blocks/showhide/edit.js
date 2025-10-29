import React from 'react';

import { BlockControls, InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';

import { Icon, seen } from '@wordpress/icons';

export default function Edit({ attributes }) {

  var attrs = {
    width: '100%',
    className: `ng-block`,
    style: {

    },
  };

  const blockProps = useBlockProps(attrs);

  let app = newsletterglue_meta?.app;
  const showconditions = newsletterglue_block_show_hide_content.showconditions;

  /* Show warning */
  var showWarning = false;
  if (showconditions) {
    showWarning = true;
    if (attributes.show_in_web && attributes.show_in_email) {
      if (attributes[`${app}_conditions`]?.length === 0) {
        showWarning = false;
      }
    }
  }

  return (
    <>
      <BlockControls group="block">

      </BlockControls>

      <InspectorControls>

      </InspectorControls>

      <div {...blockProps}>
        {showWarning && (
          <div style={{
            position: 'absolute',
            right: 0,
            top: 0,
            width: '30px',
            height: '30px',
            backgroundColor: '#eee',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
          }}>
            <Icon icon={seen} />
          </div>
        )}
        <InnerBlocks />
      </div>
    </>
  );

}