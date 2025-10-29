import React from 'react';

import { useBlockProps } from '@wordpress/block-editor';
import { LpSave } from './save-output.js';

export default function save({ attributes }) {

  var attrs = {
    className: `is-${attributes.contentstyle} columns-${attributes.columns_num} images-${attributes.image_position} table-ratio-${attributes.table_ratio}`
  };

  const blockProps = useBlockProps.save(attrs);

  return (
    <LpSave attributes={attributes} blockProps={blockProps} />
  );

}