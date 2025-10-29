import React from 'react';

/**
 * WordPress dependencies
 */
import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
  const attrs = {
    className: `ng-block`,
  }

  const { spacing } = attributes;
  const style = {
    paddingBottom: spacing,
  }

  return (
    <li {...useBlockProps.save(attrs)} style={style}>
      <RichText.Content value={attributes.content} />
      <InnerBlocks.Content />
    </li>
  );
}