import React from 'react';

/**
 * WordPress dependencies
 */
import {
  BlockControls,
  PlainText,
  useBlockProps,
} from '@wordpress/block-editor';
import { Disabled, ToolbarButton, ToolbarGroup } from '@wordpress/components';
import { useContext, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Preview from './preview';

export default function HTMLEdit({ attributes, setAttributes, isSelected }) {
  const [isPreview, setIsPreview] = useState();
  const isDisabled = useContext(Disabled.Context);

  function switchToPreview() {
    setIsPreview(true);
  }

  function switchToHTML() {
    setIsPreview(false);
  }

  return (
    <div {...useBlockProps({ className: 'block-library-html__edit' })}>
      <BlockControls>
        <ToolbarGroup>
          <ToolbarButton
            className="components-tab-button"
            isPressed={!isPreview}
            onClick={switchToHTML}
          >
            HTML
          </ToolbarButton>
          <ToolbarButton
            className="components-tab-button"
            isPressed={isPreview}
            onClick={switchToPreview}
          >
            {__('Preview')}
          </ToolbarButton>
        </ToolbarGroup>
      </BlockControls>
      {isPreview || isDisabled ? (
        <Preview
          content={attributes.content}
          isSelected={isSelected}
        />
      ) : (
        <PlainText
          value={attributes.content}
          onChange={(content) => setAttributes({ content })}
          placeholder={__('Write HTML…')}
          aria-label={__('HTML')}
        />
      )}
    </div>
  );
}
