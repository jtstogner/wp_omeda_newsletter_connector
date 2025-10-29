/**
 * WordPress dependencies
 */
import { createBlock } from "@wordpress/blocks";
import { create, split, toHTMLString } from "@wordpress/rich-text";

/**
 * Internal dependencies
 */
import { createListBlockFromDOMElement } from "./utils";

let listBlock = "newsletterglue/list";
let listItemBlock = "newsletterglue/list-item";
let paragraphBlock = "newsletterglue/text";
let headingBlock = "newsletterglue/heading";

/*
if (nglue_backend.is_allowed_post_type) {
  if (nglue_backend.core_post_type === 'no') {
    listBlock = 'newsletterglue/list';
    listItemBlock = 'newsletterglue/list-item';
    paragraphBlock = 'newsletterglue/text';
    headingBlock = 'newsletterglue/heading';
  }
}
*/

function getListContentSchema({ phrasingContentSchema }) {
  const listContentSchema = {
    ...phrasingContentSchema,
    ul: {},
    ol: { attributes: ["type", "start", "reversed"] },
  };

  // Recursion is needed.
  // Possible: ul > li > ul.
  // Impossible: ul > ul.
  ["ul", "ol"].forEach((tag) => {
    listContentSchema[tag].children = {
      li: {
        children: listContentSchema,
      },
    };
  });

  return listContentSchema;
}

function getListContentFlat(blocks) {
  return blocks.flatMap(({ name, attributes, innerBlocks = [] }) => {
    if (name === listItemBlock) {
      return [attributes.content, ...getListContentFlat(innerBlocks)];
    }
    return getListContentFlat(innerBlocks);
  });
}

const transforms = {
  from: [
    {
      type: "block",
      isMultiBlock: true,
      blocks: [paragraphBlock, headingBlock],
      transform: (blockAttributes) => {
        let childBlocks = [];
        if (blockAttributes.length > 1) {
          childBlocks = blockAttributes.map(({ content }) => {
            return createBlock(listItemBlock, { content });
          });
        } else if (blockAttributes.length === 1) {
          const value = create({
            html: blockAttributes[0].content,
          });
          childBlocks = split(value, "\n").map((result) => {
            return createBlock(listItemBlock, {
              content: toHTMLString({ value: result }),
            });
          });
        }
        return createBlock(listBlock, {}, childBlocks);
      },
    },
    {
      type: "raw",
      selector: "ol,ul",
      schema: (args) => ({
        ol: getListContentSchema(args).ol,
        ul: getListContentSchema(args).ul,
      }),
      transform: createListBlockFromDOMElement,
    },
    ...["*", "-"].map((prefix) => ({
      type: "prefix",
      prefix,
      transform(content) {
        return createBlock(listBlock, {}, [
          createBlock(listItemBlock, { content }),
        ]);
      },
    })),
    ...["1.", "1)"].map((prefix) => ({
      type: "prefix",
      prefix,
      transform(content) {
        return createBlock(
          listBlock,
          {
            ordered: true,
          },
          [createBlock(listItemBlock, { content })]
        );
      },
    })),
  ],
  to: [
    ...[paragraphBlock, headingBlock].map((block) => ({
      type: "block",
      blocks: [block],
      transform: (_attributes, childBlocks) => {
        return getListContentFlat(childBlocks).map((content) =>
          createBlock(block, {
            content,
          })
        );
      },
    })),
  ],
};

export default transforms;
