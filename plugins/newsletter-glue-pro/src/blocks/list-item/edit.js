import React from 'react';

/**
 * WordPress dependencies
 */
import {
  BlockControls,
  RichText,
  useBlockProps,
  useInnerBlocksProps,
} from '@wordpress/block-editor';
import { ToolbarButton } from '@wordpress/components';
import { useMergeRefs } from '@wordpress/compose';
import { select } from '@wordpress/data';
import { __, isRTL } from '@wordpress/i18n';
import {
  formatIndent,
  formatIndentRTL,
  formatOutdent,
  formatOutdentRTL,
} from '@wordpress/icons';

/**
 * Internal dependencies
 */
import {
  useCopy,
  useEnter,
  useIndentListItem,
  useMerge,
  useOutdentListItem,
  useSpace,
  useSplit,
} from './hooks';

import { convertToListItems } from './utils';

export function IndentUI({ clientId }) {
  const [canIndent, indentListItem] = useIndentListItem(clientId);
  const [canOutdent, outdentListItem] = useOutdentListItem(clientId);

  return (
    <>
      <ToolbarButton
        icon={isRTL() ? formatOutdentRTL : formatOutdent}
        title={__('Outdent')}
        describedBy={__('Outdent list item')}
        disabled={!canOutdent}
        onClick={() => outdentListItem()}
      />
      <ToolbarButton
        icon={isRTL() ? formatIndentRTL : formatIndent}
        title={__('Indent')}
        describedBy={__('Indent list item')}
        isDisabled={!canIndent}
        onClick={() => indentListItem()}
      />
    </>
  );
}

export default function ListItemEdit({
  attributes,
  setAttributes,
  onReplace,
  clientId,
  mergeBlocks
}) {

  let isMobile;
  if (select('core/editor')) {
    isMobile = select('core/editor').getDeviceType() === 'Mobile';
  } else if (select('core/edit-site')) {
    isMobile = select('core/edit-site').getDeviceType() === 'Mobile';
  } else {
    isMobile = false;
  }

  const { placeholder, content, spacing, mobile_list_spacing } = attributes;
  const blockProps = useBlockProps({ className: `ng-block`, ref: useCopy(clientId) });
  const innerBlocksProps = useInnerBlocksProps(blockProps, {
    allowedBlocks: ['newsletterglue/list'],
    renderAppender: false,
    __unstableDisableDropZone: true,
  });
  const useEnterRef = useEnter({ content, clientId });
  const useSpaceRef = useSpace(clientId);
  const onSplit = useSplit(clientId);
  const onMerge = useMerge(clientId, mergeBlocks);

  const style = {
    paddingBottom: isMobile ? mobile_list_spacing : spacing,
  }

  return (
    <>
      <li {...innerBlocksProps} style={style}>
        <RichText
          ref={useMergeRefs([useEnterRef, useSpaceRef])}
          identifier="content"
          tagName="div"
          onChange={(nextContent) =>
            setAttributes({ content: nextContent })
          }
          value={content}
          aria-label={__('List text')}
          placeholder={placeholder || __('List')}
          onSplit={onSplit}
          onMerge={onMerge}
          onReplace={
            onReplace
              ? (blocks, ...args) => {
                onReplace(
                  convertToListItems(blocks),
                  ...args
                );
              }
              : undefined
          }
        />
        {innerBlocksProps.children}
      </li>
      <BlockControls group="block">
        <IndentUI clientId={clientId} />
      </BlockControls>
    </>
  );
}