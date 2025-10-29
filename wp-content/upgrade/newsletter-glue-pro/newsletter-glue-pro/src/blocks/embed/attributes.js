import { theme } from "../../defaults/theme.js";

let attrs = {
  url: {
    type: "string",
    role: "content",
  },
  content: {
    type: "string",
    role: "content",
  },
  provider: {
    type: "string",
  },
  cannotEmbed: {
    type: "boolean",
    default: false,
  },
  background: {
    type: "string",
  },
  color: {
    type: "string",
  },
  border: {
    type: "string",
  },
  link: {
    type: "string",
    default: theme.colors.primary,
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
  padding: {
    type: "object",
    default: theme.embed.padding,
  },
  margin: {
    type: "object",
    default: theme.embed.margin,
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
};

if (theme.mobile.embed) {
  attrs[`mobile_size`] = {
    type: "string",
    default: theme.mobile.embed.fontsize,
  };
  attrs[`mobile_lineheight`] = {
    type: "number",
    default: theme.mobile.embed.lineheight,
  };
  attrs[`mobile_padding`] = {
    type: "object",
    default: theme.mobile.embed.padding,
  };
  attrs[`mobile_margin`] = {
    type: "object",
    default: theme.mobile.embed.margin,
  };
}

export const attributes = attrs;

export default { attributes };
