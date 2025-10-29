import { theme } from "../../defaults/theme.js";

let attrs = {
  values: {
    type: "string",
    source: "html",
    selector: "ol,ul",
    multiline: "li",
    default: "",
    __unstableMultilineWrapperTags: ["ol", "ul"],
    role: "content",
  },
  ordered: {
    type: "boolean",
    default: false,
    role: "content",
  },
  type: {
    type: "string",
  },
  start: {
    type: "number",
  },
  reversed: {
    type: "boolean",
  },
  background: {
    type: "string",
  },
  color: {
    type: "string",
  },
  link: {
    type: "string",
    default: theme.colors.primary,
  },
  padding: {
    type: "object",
  },
  font: {
    type: "object",
    default: theme.font,
  },
  fontsize: {
    type: "string",
    default: theme.block.fontsize,
  },
  lineheight: {
    type: "number",
    default: theme.lineheight,
  },
  spacing: {
    type: "string",
    default: theme.list.spacing,
  },
  fontweight: {
    type: "object",
    default: theme.fontweight,
  },
  align: {
    type: "string",
    default: "none",
  },
  placeholder: {
    type: "string",
  },
  show_in_web: {
    type: "boolean",
    default: true,
  },
  show_in_email: {
    type: "boolean",
    default: true,
  },
  isParent: {
    type: "boolean",
    default: true,
  },
  viewport: {
    type: "string",
    default: "Desktop",
  },
};

if (theme.mobile.list) {
  attrs[`mobile_list_size`] = {
    type: "string",
    default: theme.mobile.list.fontsize,
  };
  attrs[`mobile_list_lineheight`] = {
    type: "number",
    default: theme.mobile.list.lineheight,
  };
  attrs[`mobile_list_padding`] = {
    type: "object",
  };
  attrs[`mobile_list_spacing`] = {
    type: "string",
    default: theme.mobile.list.spacing,
  };
}

export const attributes = attrs;

export default { attributes };
