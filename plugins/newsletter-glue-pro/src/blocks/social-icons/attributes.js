import { theme } from "../../defaults/theme.js";

let attrs = {
  add_description: {
    type: "boolean",
    default: true,
  },
  description: {
    type: "string",
    default: "Follow me on",
  },
  icon_shape: {
    type: "string",
    default: "round",
  },
  icon_color: {
    type: "string",
    default: "black",
  },
  icon_size: {
    type: "string",
    default: "24px",
  },
  mobile_icon_size: {
    type: "string",
    default: "",
  },
  gap: {
    type: "string",
    default: "5px",
  },
  mobile_gap: {
    type: "string",
    default: "",
  },
  new_window: {
    type: "boolean",
    default: true,
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
    default: theme.share.padding,
  },
  font: {
    type: "object",
    default: theme.font,
  },
  fontsize: {
    type: "string",
    default: theme.share.fontsize,
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

if (theme.mobile.share) {
  attrs[`mobile_size`] = {
    type: "string",
    default: theme.mobile.share.fontsize,
  };
  attrs[`mobile_lineheight`] = {
    type: "number",
    default: theme.mobile.share.lineheight,
  };
  attrs[`mobile_padding`] = {
    type: "object",
    default: theme.mobile.share.padding,
  };
}

export const attributes = attrs;

export default { attributes };
