import React from 'react';

import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { Fragment } from '@wordpress/element';

import { LpBorderOptions } from './edit-border-options.js';
import { LpDisplayOptions } from './edit-display-options.js';
import { LpFilterOptions } from './edit-filter-options.js';
import { LpLayoutOptions } from './edit-layout-options.js';
import { LpEdit } from './edit-output.js';

import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

export default function Edit({ attributes, setAttributes, className, isSelected, clientId }) {

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  var hasImages = attributes.show_image ? 'images' : 'no-images';

  var attrs = {
    className: `is-${attributes.contentstyle} has-${hasImages} columns-${attributes.columns_num} images-${attributes.image_position} table-ratio-${attributes.table_ratio}`
  };

  const blockProps = useBlockProps(attrs);

  return (

    <Fragment>

      <InspectorControls>

        {deviceType !== 'Mobile' && <>
          <div className={`ngl-gb-outlined`}>
            <LpLayoutOptions attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} />
            <LpFilterOptions attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} />
          </div>

          <LpDisplayOptions attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} />
          <LpBorderOptions attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} />
        </>}

        {deviceType !== 'Mobile' &&
          <Controls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
        {deviceType === 'Mobile' &&
          <MobileControls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
      </InspectorControls>

      <LpEdit attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} blockProps={blockProps} />

    </Fragment>

  );

}