/**
 * WordPress dependencies
 */
import { createBlock, switchToBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
let listBlock = 'core/list';
let listItemBlock = 'core/list-item';
let paragraphBlock = 'core/paragraph';

if (nglue_backend.is_allowed_post_type) {
  if (nglue_backend.core_post_type === 'no') {
    listBlock = 'newsletterglue/list';
    listItemBlock = 'newsletterglue/list-item';
    paragraphBlock = 'newsletterglue/text';
  }
}

export function createListItem(listItemAttributes, listAttributes, children) {
  return createBlock(
    listItemBlock,
    listItemAttributes,
    !children?.length
      ? []
      : [createBlock(listBlock, listAttributes, children)]
  );
}

function convertBlockToList(block) {
  const list = switchToBlockType(block, listBlock);
  if (list) return list;
  const paragraph = switchToBlockType(block, paragraphBlock);
  if (paragraph) return switchToBlockType(paragraph, listBlock);
  return null;
}

export function convertToListItems(blocks) {
  const listItems = [];

  for (let block of blocks) {
    if (block.name === listItemBlock) {
      listItems.push(block);
    } else if (block.name === listBlock) {
      listItems.push(...block.innerBlocks);
    } else if ((block = convertBlockToList(block))) {
      for (const { innerBlocks } of block) {
        listItems.push(...innerBlocks);
      }
    }
  }

  return listItems;
}