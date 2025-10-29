import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import React from 'react';

export default function save() {

  var attrs = {
    width: '100%',
    className: `ng-block`,
    style: {

    }
  };

  const blockProps = useBlockProps.save(attrs);

  return (
    <>
      <div {...blockProps}>
        <InnerBlocks.Content />
      </div>
    </>
  );

}