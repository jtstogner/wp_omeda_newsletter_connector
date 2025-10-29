/**
 * WordPress dependencies
 */
import { createBlock, rawHandler } from '@wordpress/blocks';

let listBlock = 'core/list';
let listItemBlock = 'core/list-item';

if (nglue_backend.is_allowed_post_type) {
  if (nglue_backend.core_post_type === 'no') {
    listBlock = 'newsletterglue/list';
    listItemBlock = 'newsletterglue/list-item';
  }
}

export function createListBlockFromDOMElement(listElement) {
  const listAttributes = {
    ordered: 'OL' === listElement.tagName,
    anchor: listElement.id === '' ? undefined : listElement.id,
    start: listElement.getAttribute('start')
      ? parseInt(listElement.getAttribute('start'), 10)
      : undefined,
    reversed: listElement.hasAttribute('reversed') ? true : undefined,
    type: listElement.getAttribute('type') ?? undefined,
  };

  const innerBlocks = Array.from(listElement.children).map(
    (listItem) => {
      const children = Array.from(listItem.childNodes).filter(
        (node) =>
          node.nodeType !== node.TEXT_NODE ||
          node.textContent.trim().length !== 0
      );
      children.reverse();
      const [nestedList, ...nodes] = children;

      const hasNestedList =
        nestedList?.tagName === 'UL' || nestedList?.tagName === 'OL';
      if (!hasNestedList) {
        return createBlock(listItemBlock, {
          content: listItem.innerHTML,
        });
      }
      const htmlNodes = nodes.map((node) => {
        if (node.nodeType === node.TEXT_NODE) {
          return node.textContent;
        }
        return node.outerHTML;
      });
      htmlNodes.reverse();
      const childAttributes = {
        content: htmlNodes.join('').trim(),
      };
      const childInnerBlocks = [
        createListBlockFromDOMElement(nestedList),
      ];
      return createBlock(
        listItemBlock,
        childAttributes,
        childInnerBlocks
      );
    }
  );

  return createBlock(listBlock, listAttributes, innerBlocks);
}

export function migrateToListV2(attributes) {
  const { values, start, reversed, ordered, type, ...otherAttributes } =
    attributes;

  const list = document.createElement(ordered ? 'ol' : 'ul');
  list.innerHTML = values;
  if (start) {
    list.setAttribute('start', start);
  }
  if (reversed) {
    list.setAttribute('reversed', true);
  }
  if (type) {
    list.setAttribute('type', type);
  }

  const [listBlock] = rawHandler({ HTML: list.outerHTML });

  return [
    { ...otherAttributes, ...listBlock.attributes },
    listBlock.innerBlocks,
  ];
}
