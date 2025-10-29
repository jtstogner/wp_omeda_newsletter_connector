import { theme } from "../../defaults/theme.js";

let attrs = {
  align: {
    type: "string",
  },
  url: {
    type: "string",
    source: "attribute",
    selector: "img",
    attribute: "src",
    role: "content",
  },
  alt: {
    type: "string",
    source: "attribute",
    selector: "img",
    attribute: "alt",
    default: "",
    role: "content",
  },
  caption: {
    type: "string",
    source: "html",
    selector: "span",
    role: "content",
  },
  title: {
    type: "string",
    source: "attribute",
    selector: "img",
    attribute: "title",
    role: "content",
  },
  href: {
    type: "string",
    source: "attribute",
    selector: "td > a",
    attribute: "href",
    role: "content",
  },
  rel: {
    type: "string",
    source: "attribute",
    selector: "td > a",
    attribute: "rel",
  },
  linkClass: {
    type: "string",
    source: "attribute",
    selector: "td > a",
    attribute: "class",
  },
  id: {
    type: "number",
    role: "content",
  },
  threshold: {
    type: "number",
    default: 600,
  },
  width: {
    type: "number",
  },
  height: {
    type: "number",
  },
  sizeSlug: {
    type: "string",
  },
  linkDestination: {
    type: "string",
  },
  linkTarget: {
    type: "string",
    source: "attribute",
    selector: "td > a",
    attribute: "target",
  },
  font: {
    type: "object",
    default: theme.font,
  },
  fontsize: {
    type: "string",
    default: theme.image.fontsize,
  },
  fontweight: {
    type: "object",
    default: theme.image.fontweight,
  },
  background: {
    type: "string",
  },
  color: {
    type: "string",
  },
  padding: {
    type: "object",
    default: theme.image.padding,
  },
  border: {
    type: "string",
  },
  borderSize: {
    type: "string",
  },
  radius: {
    type: "string",
  },
  mobile_keep_size: {
    type: "boolean",
    default: false,
  },
  show_in_web: {
    type: "boolean",
    default: true,
  },
  show_in_email: {
    type: "boolean",
    default: true,
  },
  viewport: {
    type: "string",
    default: "Desktop",
  },
};

if (theme.mobile.image) {
  attrs[`mobile_size`] = {
    type: "string",
    default: theme.mobile.image.fontsize,
  };
  attrs[`mobile_padding`] = {
    type: "object",
    default: theme.mobile.image.padding,
  };
  attrs[`mobile_width`] = {
    type: "number",
  };
  attrs[`mobile_height`] = {
    type: "number",
  };
}

export const attributes = attrs;

export default { attributes };
