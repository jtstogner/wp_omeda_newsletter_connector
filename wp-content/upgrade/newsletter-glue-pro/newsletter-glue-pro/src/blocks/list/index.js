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
  registerBlockType("newsletterglue/list", {
    apiVersion: 2,
    title: __("List", "newsletter-glue"),
    category: "newsletterglue-blocks",
    icon: icon,
    example: {
      innerBlocks: [
        {
          name: "newsletterglue/list-item",
          attributes: { content: __("Alice.") },
        },
        {
          name: "newsletterglue/list-item",
          attributes: { content: __("The White Rabbit.") },
        },
        {
          name: "newsletterglue/list-item",
          attributes: { content: __("The Cheshire Cat.") },
        },
        {
          name: "newsletterglue/list-item",
          attributes: { content: __("The Mad Hatter.") },
        },
        {
          name: "newsletterglue/list-item",
          attributes: { content: __("The Queen of Hearts.") },
        },
      ],
    },
    description: __("Add a list to your email.", "newsletter-glue"),
    keywords: ["newsletter", "email", "list"],
    attributes: attributes,
    edit: Edit,
    save,
    transforms: transforms,
    supports: {
      __unstablePasteTextInline: true,
      __experimentalSelector: "ol,ul",
      __experimentalSlashInserter: true,
      splitting: true,
    },
  });
}
