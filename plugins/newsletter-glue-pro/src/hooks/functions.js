import { select } from '@wordpress/data';

export function isParentBlock(clientId, name) {
  const innerBlock = name;
  const parentBlocks = select('core/block-editor').getBlockParents(clientId);
  const parentAttributes = select('core/block-editor').getBlocksByClientId(parentBlocks);

  var is_under_inner = false;
  var i;
  for (i = 0; i < parentAttributes.length; i++) {
    if (parentAttributes[i].name == innerBlock) {
      is_under_inner = true;
    }
  }

  if (is_under_inner) {
    return false;
  } else {
    return true;
  }
}