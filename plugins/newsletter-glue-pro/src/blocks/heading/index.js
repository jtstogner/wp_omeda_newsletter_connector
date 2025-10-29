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
  registerBlockType("newsletterglue/heading", {
    apiVersion: 2,
    title: __("Heading", "newsletter-glue"),
    category: "newsletterglue-blocks",
    icon: icon,
    example: {
      attributes: {
        content: __("Code is Poetry"),
        level: 2,
      },
    },
    description: __("Add headings to your email.", "newsletter-glue"),
    keywords: ["newsletter", "email", "heading"],
    attributes: attributes,
    transforms: transforms,
    supports: {
      __unstablePasteTextInline: true,
      __experimentalSlashInserter: true,
      splitting: true,
    },
    merge(attributes, attributesToMerge) {
      return {
        content: (attributes.content || "") + (attributesToMerge.content || ""),
      };
    },
    edit: Edit,
    save,
  });
}
