/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

const transforms = {
  from: [
    {
      type: 'block',
      blocks: ['newsletterglue/code'],
      transform: ({ content }) => {
        return createBlock('newsletterglue/html', {
          content,
        });
      },
    },
  ],
};

export default transforms;
