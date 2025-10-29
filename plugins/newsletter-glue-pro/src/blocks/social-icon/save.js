import { useBlockProps } from '@wordpress/block-editor';
import React from 'react';

export default function save({ attributes }) {

  var attrs = {
    className: `ng-block ng-social-${attributes.service}`,
  };

  let getLeftMargin = '0px';
  let getRightMargin = '0px';
  if (attributes.align == 'center') {
    getLeftMargin = attributes.gap;
    getRightMargin = attributes.gap;
  } else if (attributes.align === 'right') {
    getLeftMargin = attributes.gap;
    getRightMargin = '0px';
  } else {
    getLeftMargin = '0px';
    getRightMargin = attributes.gap;
  }

  return (
    <>
      <span
        {...useBlockProps.save(attrs)}
        style={{ display: 'inline-flex', marginRight: getRightMargin, marginLeft: getLeftMargin }}
      >
        <a href={attributes.url}
          rel={'noopener noreferrer'}
          target={attributes && attributes.new_window ? '_blank' : '_self'}>
          <img
            src={`${nglue_backend.share_uri}/${attributes.icon_shape}/${attributes.icon_color}/${attributes.service}.png`}
            width={parseInt(attributes.icon_size)}
            height={parseInt(attributes.icon_size)}
            style={{ width: attributes.icon_size, height: attributes.icon_size }}
            className={'ngl-inline-image'}
          />
        </a>
      </span>
    </>
  );

}