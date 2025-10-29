import { theme } from "../../defaults/theme.js";

let attrs = {
  content: {
    type: "string",
    source: "html",
    selector: "p",
    default: "",
    role: "content",
  },
  citation: {
    type: "string",
    source: "html",
    selector: "span",
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
  cite_color: {
    type: "string",
  },
  border: {
    type: "string",
    default: theme.quote.border,
  },
  padding: {
    type: "object",
    default: theme.quote.padding,
  },
  font: {
    type: "object",
    default: theme.font,
  },
  fontsize: {
    type: "string",
    default: theme.quote.fontsize,
  },
  fontsizeCite: {
    type: "string",
    default: theme.block.fontsize,
  },
  lineheight: {
    type: "number",
    default: theme.quote.lineheight,
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
};

if (theme.mobile.quote) {
  attrs[`mobile_quote_size`] = {
    type: "string",
    default: theme.mobile.quote.fontsize,
  };
  attrs[`mobile_quote_citesize`] = {
    type: "string",
    default: theme.mobile.quote.citesize,
  };
  attrs[`mobile_quote_lineheight`] = {
    type: "number",
    default: theme.mobile.quote.lineheight,
  };
  attrs[`mobile_quote_padding`] = {
    type: "object",
    default: theme.mobile.quote.padding,
  };
}

export const attributes = attrs;

export default { attributes };
