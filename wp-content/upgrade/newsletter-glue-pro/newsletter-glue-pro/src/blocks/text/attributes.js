import { theme } from "../../defaults/theme.js";

let attrs = {
  content: {
    type: "string",
    source: "html",
    selector: "p",
    default: "",
    role: "content",
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
    default: theme.block.padding,
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
  viewport: {
    type: "string",
    default: "Desktop",
  },
  inherited: {
    type: "boolean",
    default: false,
  },
};

if (theme.mobile.paragraph) {
  attrs[`mobile_p_size`] = {
    type: "string",
    default: theme.mobile.paragraph.fontsize,
  };
  attrs[`mobile_p_lineheight`] = {
    type: "number",
    default: theme.mobile.paragraph.lineheight,
  };
  attrs[`mobile_p_padding`] = {
    type: "object",
    default: theme.mobile.paragraph.padding,
  };
}

export const attributes = attrs;

export default { attributes };
