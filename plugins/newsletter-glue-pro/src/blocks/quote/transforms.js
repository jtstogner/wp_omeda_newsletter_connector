import { createBlock } from '@wordpress/blocks';

const transforms = {

  from: [
    {
      type: 'block',
      isMultiBlock: true,
      blocks: ['newsletterglue/text'],
      transform: (attributes) => {
        var attrs = attributes[0];
        return createBlock('newsletterglue/quote', {
          content: attrs.content,
        });
      }
    },
    {
      type: 'block',
      isMultiBlock: true,
      blocks: ['newsletterglue/heading'],
      transform: (attributes) => {
        var attrs = attributes[0];
        return createBlock('newsletterglue/quote', {
          content: attrs.content.replace(/<[^>]+>/g, ''),
        });
      }
    },
  ],

  to: [
    {
      type: 'block',
      isMultiBlock: true,
      blocks: ['newsletterglue/text'],
      transform: (attributes) => {
        var attrs = attributes[0];
        return createBlock('newsletterglue/text', {
          content: attrs.content,
        });
      }
    },
    {
      type: 'block',
      isMultiBlock: true,
      blocks: ['newsletterglue/heading'],
      transform: (attributes) => {
        var attrs = attributes[0];
        return createBlock('newsletterglue/heading', {
          content: attrs.content,
        });
      }
    },
  ],

};

export default transforms;