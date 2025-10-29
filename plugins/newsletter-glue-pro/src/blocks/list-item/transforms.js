/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

let paragraphBlock = 'core/paragraph';

if (nglue_backend.is_allowed_post_type) {
  if (nglue_backend.core_post_type === 'no') {
    paragraphBlock = 'newsletterglue/text';
  }
}

const transforms = {
  to: [
    {
      type: 'block',
      blocks: [paragraphBlock],
      transform: (attributes) =>
        createBlock(paragraphBlock, attributes),
    },
  ],
};

export default transforms;
