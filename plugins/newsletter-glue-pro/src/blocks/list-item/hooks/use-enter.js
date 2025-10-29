/**
 * WordPress dependencies
 */
import { store as blockEditorStore } from '@wordpress/block-editor';
import {
  cloneBlock,
  createBlock,
} from '@wordpress/blocks';
import { useRefEffect } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useRef } from '@wordpress/element';
import { ENTER } from '@wordpress/keycodes';

/**
 * Internal dependencies
 */
import useOutdentListItem from './use-outdent-list-item';

let paragraphBlock = 'core/paragraph';

if (nglue_backend.is_allowed_post_type) {
  if (nglue_backend.core_post_type === 'no') {
    paragraphBlock = 'newsletterglue/text';
  }
}

export default function useEnter(props) {
  const { replaceBlocks, selectionChange } = useDispatch(blockEditorStore);
  const { getBlock, getBlockRootClientId, getBlockIndex } =
    useSelect(blockEditorStore);
  const propsRef = useRef(props);
  propsRef.current = props;
  const [canOutdent, outdentListItem] = useOutdentListItem(
    propsRef.current.clientId
  );
  return useRefEffect(
    (element) => {
      function onKeyDown(event) {
        if (event.defaultPrevented || event.keyCode !== ENTER) {
          return;
        }
        const { content, clientId } = propsRef.current;
        if (content.length) {
          return;
        }
        event.preventDefault();
        if (canOutdent) {
          outdentListItem();
          return;
        }
        // Here we are in top level list so we need to split.
        const topParentListBlock = getBlock(
          getBlockRootClientId(clientId)
        );
        const blockIndex = getBlockIndex(clientId);
        const head = cloneBlock({
          ...topParentListBlock,
          innerBlocks: topParentListBlock.innerBlocks.slice(
            0,
            blockIndex
          ),
        });
        const middle = createBlock(paragraphBlock);
        // Last list item might contain a `list` block innerBlock
        // In that case append remaining innerBlocks blocks.
        const after = [
          ...(topParentListBlock.innerBlocks[blockIndex]
            .innerBlocks[0]?.innerBlocks || []),
          ...topParentListBlock.innerBlocks.slice(blockIndex + 1),
        ];
        const tail = after.length
          ? [
            cloneBlock({
              ...topParentListBlock,
              innerBlocks: after,
            }),
          ]
          : [];
        replaceBlocks(
          topParentListBlock.clientId,
          [head, middle, ...tail],
          1
        );
        // We manually change the selection here because we are replacing
        // a different block than the selected one.
        selectionChange(middle.clientId);
      }

      element.addEventListener('keydown', onKeyDown);
      return () => {
        element.removeEventListener('keydown', onKeyDown);
      };
    },
    [canOutdent]
  );
}
