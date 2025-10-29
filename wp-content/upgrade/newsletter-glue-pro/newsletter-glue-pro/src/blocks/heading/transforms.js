import { createBlock, getBlockAttributes } from '@wordpress/blocks';

import { getLevelFromHeadingNodeName } from './shared';

let name = 'core/heading';
let paragraphName = 'core/paragraph';

if (nglue_backend.is_allowed_post_type) {
  if (nglue_backend.core_post_type === 'no') {
    name = 'newsletterglue/heading';
    paragraphName = 'newsletterglue/text';
  }
}

const transforms = {
  from: [
    {
      type: 'block',
      isMultiBlock: true,
      blocks: [paragraphName],
      transform: (attributes) =>
        attributes.map(({ content, anchor, align: textAlign }) =>
          createBlock(name, {
            content,
            anchor,
            textAlign,
          })
        ),
    },
    {
      type: 'raw',
      selector: 'h1,h2,h3,h4,h5,h6',
      schema: ({ phrasingContentSchema, isPaste }) => {
        const schema = {
          children: phrasingContentSchema,
          attributes: isPaste ? [] : ['style', 'id'],
        };
        return {
          h1: schema,
          h2: schema,
          h3: schema,
          h4: schema,
          h5: schema,
          h6: schema,
        };
      },
      transform(node) {
        const attributes = getBlockAttributes(name, node.outerHTML);
        const { textAlign } = node.style || {};

        attributes.level = getLevelFromHeadingNodeName(node.nodeName);

        if (
          textAlign === 'left' ||
          textAlign === 'center' ||
          textAlign === 'right'
        ) {
          attributes.align = textAlign;
        }

        return createBlock(name, attributes);
      },
    },
    ...[1, 2, 3, 4, 5, 6].map((level) => ({
      type: 'prefix',
      prefix: Array(level + 1).join('#'),
      transform(content) {
        return createBlock(name, {
          level,
          content,
        });
      },
    })),
    ...[1, 2, 3, 4, 5, 6].map((level) => ({
      type: 'enter',
      regExp: new RegExp(`^/(h|H)${level}$`),
      transform(content) {
        return createBlock(name, {
          level,
          content,
        });
      },
    })),
  ],
  to: [
    {
      type: 'block',
      isMultiBlock: true,
      blocks: [paragraphName],
      transform: (attributes) =>
        attributes.map(({ content, textAlign: align }) =>
          createBlock(paragraphName, { content, align })
        ),
    },
  ],
};

export default transforms;