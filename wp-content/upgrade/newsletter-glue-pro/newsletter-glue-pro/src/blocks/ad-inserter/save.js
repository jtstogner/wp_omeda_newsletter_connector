import React from 'react';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
  __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
  useBlockProps,
} from '@wordpress/block-editor';

export default function save({ attributes }) {
  const {
    url,
    alt,
    align,
    width,
    height,
    title,
    adLink,
    show_in_web,
    show_in_email,
    adZoneId,
    adZoneName,
    clientId
  } = attributes;

  const borderProps = getBorderClassesAndStyles(attributes);

  const classes = classnames({
    'ng-block': true,
    [`align${align}`]: align,
    'is-resized': width || height,
    'has-custom-border': !!borderProps.className,
    'ng-ad-inserter': true,
    'ng-hide-web': !show_in_web,
    'ng-hide-email': !show_in_email,
  });

  const blockProps = useBlockProps.save({
    className: classes,
    'data-ad-zone-id': adZoneId || '',
    'data-ad-zone-name': adZoneName || '',
    'data-block-client-id': clientId || '',
  });

  // Use the provided URL or fallback to the placeholder
  const imageUrl = url || nglue_backend.ad_inserter_placeholder_image;

  const ImageComponent = () => (
    <img
      src={imageUrl}
      alt={alt || ''}
      title={title || ''}
      width={width}
      height={height}
      className={borderProps.className}
      style={{
        ...borderProps.style,
        width: width ? `${width}px` : undefined,
        height: height ? `${height}px` : undefined,
      }}
    />
  );

  return (
    <div {...blockProps}>
      <figure className="wp-block-image">
        {adLink ? (
          <a href={adLink} target="_blank" rel="noopener noreferrer">
            <ImageComponent />
          </a>
        ) : (
          <ImageComponent />
        )}
      </figure>
    </div>
  );
}
