/**
 * WordPress dependencies
 */
import { createBlock, getBlockAttributes } from "@wordpress/blocks";

/**
 * Internal dependencies
 */
let name = "core/paragraph";

if (nglue_backend.is_allowed_post_type) {
  if (nglue_backend.core_post_type === "no") {
    name = "newsletterglue/text";
  }
}

const transforms = {
  from: [
    {
      type: "raw",
      // Paragraph is a fallback and should be matched last.
      priority: 20,
      selector: "p",
      schema: ({ phrasingContentSchema, isPaste }) => ({
        p: {
          children: phrasingContentSchema,
          attributes: isPaste ? [] : ["style", "id"],
        },
      }),
      transform(node) {
        const attributes = getBlockAttributes(name, node.outerHTML);
        const { textAlign } = node.style || {};

        if (
          textAlign === "left" ||
          textAlign === "center" ||
          textAlign === "right"
        ) {
          attributes.align = textAlign;
        }

        return createBlock(name, attributes);
      },
    },
  ],
};

export default transforms;
