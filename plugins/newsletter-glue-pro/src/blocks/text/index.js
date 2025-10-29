import { registerBlockType } from "@wordpress/blocks";
import { __ } from "@wordpress/i18n";
import { attributes } from "./attributes";
import Edit from "./edit";
import "./editor.scss";
import { icon } from "./icon.js";
import save from "./save";
import transforms from "./transforms";

let loadBlock = true;
if (!nglue_backend.is_allowed_post_type) {
  loadBlock = false;
}

if (loadBlock) {
  registerBlockType("newsletterglue/text", {
    apiVersion: 2,
    title: __("Text", "newsletter-glue"),
    category: "newsletterglue-blocks",
    icon: icon,
    example: {
      attributes: {
        content: __(
          "In a village of La Mancha, the name of which I have no desire to call to mind, there lived not long since one of those gentlemen that keep a lance in the lance-rack, an old buckler, a lean hack, and a greyhound for coursing."
        ),
      },
    },
    description: __("Add text to your email.", "newsletter-glue"),
    keywords: ["newsletter", "email", "text"],
    attributes: attributes,
    edit: Edit,
    save,
    transforms: transforms,
    supports: {
      __experimentalSelector: "p",
      __unstablePasteTextInline: true,
      __experimentalSlashInserter: true,
      splitting: true,
    },
    usesContext: ["container/color"],
    merge(attributes, attributesToMerge) {
      return {
        content: (attributes.content || "") + (attributesToMerge.content || ""),
      };
    },
  });
}
