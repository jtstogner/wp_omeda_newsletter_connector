/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
let name = 'core/embed';

if (nglue_backend.is_allowed_post_type) {
  if (nglue_backend.core_post_type === 'no') {
    name = 'newsletterglue/embed';
  }
}

const transforms = {
  from: [
    {
      type: 'raw',
      isMatch: (node) =>
        node.nodeName === 'P' &&
        /^\s*(https?:\/\/\S+)\s*$/i.test(node.textContent) &&
        node.textContent?.match(/https/gi)?.length === 1,
      transform: (node) => {
        return createBlock(name, {
          url: node.textContent.trim(),
        });
      },
    },
  ],
  to: [
    {
      type: 'block',
      blocks: ['newsletterglue/text'],
      isMatch: ({ url }) => !!url,
      transform: ({ url, caption }) => {
        let value = `<a href="${url}">${url}</a>`;
        if (caption?.trim()) {
          value += `<br />${caption}`;
        }
        return createBlock('newsletterglue/text', {
          content: value,
        });
      },
    },
  ],
};

export default transforms;