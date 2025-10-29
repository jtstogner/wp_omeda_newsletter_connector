import React from 'react';

/**
 * WordPress dependencies
 */
import {
  BlockControls,
  InspectorControls,
  store as blockEditorStore,
  useBlockProps,
  useInnerBlocksProps,
} from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { ToolbarButton } from '@wordpress/components';
import { dispatch, select, useDispatch, useSelect } from '@wordpress/data';
import { Platform, useCallback, useEffect, useLayoutEffect } from '@wordpress/element';
import { __, isRTL } from '@wordpress/i18n';
import {
  formatListBullets,
  formatListBulletsRTL,
  formatListNumbered,
  formatListNumberedRTL,
  formatOutdent,
  formatOutdentRTL,
} from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { theme } from '../../defaults/theme.js';
import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';
import OrderedListSettings from './ordered-list-settings';

import { isParentBlock } from '../../hooks/functions.js';

import TagName from './tag-name';

const TEMPLATE = [['newsletterglue/list-item']];
const NATIVE_MARGIN_SPACING = 8;

function useOutdentList(clientId) {
  const { canOutdent } = useSelect(
    (innerSelect) => {
      const { getBlockRootClientId, getBlock } =
        innerSelect(blockEditorStore);
      const parentId = getBlockRootClientId(clientId);
      return {
        canOutdent:
          !!parentId &&
          getBlock(parentId).name === 'newsletterglue/list-item',
      };
    },
    [clientId]
  );
  const { replaceBlocks, selectionChange } = useDispatch(blockEditorStore);
  const { getBlockRootClientId, getBlockAttributes, getBlock } =
    useSelect(blockEditorStore);

  return [
    canOutdent,
    useCallback(() => {
      const parentBlockId = getBlockRootClientId(clientId);
      const parentBlockAttributes = getBlockAttributes(parentBlockId);
      // Create a new parent block without the inner blocks.
      const newParentBlock = createBlock(
        'newsletterglue/list-item',
        parentBlockAttributes
      );
      const { innerBlocks } = getBlock(clientId);
      // Replace the parent block with a new parent block without inner blocks,
      // and make the inner blocks siblings of the parent.
      replaceBlocks(
        [parentBlockId],
        [newParentBlock, ...innerBlocks]
      );
      // Select the last child of the list being outdent.
      selectionChange(innerBlocks[innerBlocks.length - 1].clientId);
    }, [clientId]),
  ];
}

function IndentUI({ clientId }) {
  const [canOutdent, outdentList] = useOutdentList(clientId);
  return (
    <>
      <ToolbarButton
        icon={isRTL() ? formatOutdentRTL : formatOutdent}
        title={__('Outdent')}
        describedBy={__('Outdent list item')}
        disabled={!canOutdent}
        onClick={outdentList}
      />
    </>
  );
}

export default function Edit({ attributes, setAttributes, clientId, style, className, isSelected }) {

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  const color = attributes.color ? attributes.color : theme.color;

  const blockProps = useBlockProps({
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
    bgcolor: attributes.background,
    style: {
      color: color,
      backgroundColor: attributes.background,
    },
    ...(Platform.isNative && { style }),
  });

  const innerBlocksProps = useInnerBlocksProps(blockProps, {
    allowedBlocks: ['newsletterglue/list-item'],
    template: TEMPLATE,
    templateLock: false,
    templateInsertUpdatesSelection: true,
    ...(Platform.isNative && {
      marginVertical: NATIVE_MARGIN_SPACING,
      marginHorizontal: NATIVE_MARGIN_SPACING,
      useCompactList: true,
    }),
  });

  const { ordered, type, reversed, start } = attributes;

  useLayoutEffect(() => {
    if (!attributes.padding || !attributes.isParent) {
      if (isParentBlock(clientId, 'newsletterglue/list')) {
        setAttributes({ isParent: true, padding: theme.list.main_padding, mobile_list_padding: theme.mobile.list.main_padding });
      } else {
        setAttributes({ isParent: false, padding: theme.list.padding, mobile_list_padding: theme.mobile.list.padding });
      }
    }
  }, []);

  useEffect(() => {
    if (select('core/block-editor').getBlocksByClientId(clientId)[0]) {
      var children = select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks;
      children.forEach(function (child) {
        dispatch('core/block-editor').updateBlockAttributes(child.clientId, { spacing: attributes.spacing, mobile_list_spacing: attributes.mobile_list_spacing });
      });
    }

  }, [attributes.spacing, attributes.mobile_list_spacing]);

  const controls = (
    <>
      <BlockControls group="block">
        <ToolbarButton
          icon={isRTL() ? formatListBulletsRTL : formatListBullets}
          title={__('Unordered')}
          describedBy={__('Convert to unordered list')}
          isActive={ordered === false}
          onClick={() => {
            setAttributes({ ordered: false });
          }}
        />
        <ToolbarButton
          icon={isRTL() ? formatListNumberedRTL : formatListNumbered}
          title={__('Ordered')}
          describedBy={__('Convert to ordered list')}
          isActive={ordered === true}
          onClick={() => {
            setAttributes({ ordered: true });
          }}
        />
        <IndentUI clientId={clientId} />
      </BlockControls>
      <InspectorControls>
        {deviceType !== 'Mobile' &&
          <Controls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
        {deviceType === 'Mobile' &&
          <MobileControls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
      </InspectorControls>
    </>
  );

  let tdStyle;
  if (attributes.padding) {
    tdStyle = {
      fontSize: isMobile ? attributes.mobile_list_size : attributes.fontsize,
      fontFamily: nglue_backend.font_names[attributes.font.key],
      lineHeight: isMobile ? attributes.mobile_list_lineheight : attributes.lineheight,
      fontWeight: attributes.fontweight.key,
      paddingTop: isMobile ? attributes.mobile_list_padding.top : attributes.padding.top,
      paddingBottom: isMobile ? attributes.mobile_list_padding.bottom : attributes.padding.bottom,
      paddingLeft: isMobile ? attributes.mobile_list_padding.left : attributes.padding.left,
      paddingRight: isMobile ? attributes.mobile_list_padding.right : attributes.padding.right,
      color: color,
    }
  } else {
    tdStyle = {
      fontSize: isMobile ? attributes.mobile_list_size : attributes.fontsize,
      fontFamily: nglue_backend.font_names[attributes.font.key],
      lineHeight: isMobile ? attributes.mobile_list_lineheight : attributes.lineheight,
      fontWeight: attributes.fontweight.key,
      color: color,
    }
  }

  return (
    <>
      <table {...blockProps}>
        <tbody>
          <tr>
            <td className="ng-block-td" style={tdStyle}>
              <TagName
                ordered={ordered}
                reversed={reversed}
                start={start}
                type={type}
                {...innerBlocksProps}
              />
            </td>
          </tr>
        </tbody>
      </table>
      {controls}
      {ordered && (
        <OrderedListSettings
          setAttributes={setAttributes}
          ordered={ordered}
          reversed={reversed}
          start={start}
        />
      )}
    </>
  );
}