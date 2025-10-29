import React from 'react';

/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import {
  PanelBody,
  __experimentalUnitControl as UnitControl,
  __experimentalParseQuantityAndUnitFromRawValue as parseQuantityAndUnitFromRawValue,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { useInstanceId } from '@wordpress/compose';

import { units } from '../../defaults/units.js';

/**
 * Internal dependencies
 */
import { MIN_SPACER_SIZE } from './constants';

function DimensionInput({ label, onChange, isResizing, value = '' }) {
  const inputId = useInstanceId(UnitControl, 'block-spacer-height-input');

  const handleOnChange = (unprocessedValue) => {
    onChange(unprocessedValue);
  };

  // Force the unit to update to `px` when the Spacer is being resized.
  const [parsedQuantity, parsedUnit] =
    parseQuantityAndUnitFromRawValue(value);
  const computedValue = [
    parsedQuantity,
    isResizing ? 'px' : parsedUnit,
  ].join('');

  return (
    <UnitControl
      label={label}
      id={inputId}
      isResetValueOnUnitChange
      min={MIN_SPACER_SIZE}
      onChange={handleOnChange}
      __unstableInputWidth={'80px'}
      value={computedValue}
      units={units}
    />
  );
}

export default function MobileControls({
  attributes,
  setAttributes,
  orientation,
  height,
  width,
  isResizing,
}) {

  return (
    <InspectorControls>
      <PanelBody title={__('Settings')}>
        {orientation === 'horizontal' && (
          <DimensionInput
            label={__('Width')}
            value={width}
            onChange={(nextWidth) =>
              setAttributes({ width: nextWidth })
            }
            isResizing={isResizing}
          />
        )}
        {orientation !== 'horizontal' && (
          <DimensionInput
            label={__('Height')}
            value={attributes.mobile_height ? attributes.mobile_height : height}
            onChange={(nextHeight) =>
              setAttributes({ mobile_height: nextHeight })
            }
            isResizing={isResizing}
          />
        )}
      </PanelBody>
    </InspectorControls>
  );
}