import { theme } from "../../defaults/theme.js";

let attrs = {
  textAlign: {
    type: "string",
    default: "center",
  },
  text: {
    type: "string",
    source: "html",
    selector: "a",
    default: "",
    role: "content",
  },
  url: {
    type: "string",
    source: "attribute",
    selector: "a",
    attribute: "href",
    role: "content",
  },
  title: {
    type: "string",
    source: "attribute",
    selector: "a",
    attribute: "title",
    role: "content",
  },
  rel: {
    type: "string",
    source: "attribute",
    selector: "a",
    attribute: "rel",
    role: "content",
  },
  linkTarget: {
    type: "string",
    source: "attribute",
    selector: "a",
    attribute: "target",
    role: "content",
  },
  placeholder: {
    type: "string",
  },
  width: {
    type: "string",
    default: "relative",
  },
  custom_width: {
    type: "string",
  },
  padding: {
    type: "object",
    default: theme.button.padding,
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
  buttonstyle: {
    type: "string",
    default: "filled",
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
    default: theme.button.lineheight,
  },
  fontweight: {
    type: "object",
    default: theme.fontweight,
  },
  borderSize: {
    type: "string",
  },
  radius: {
    type: "string",
    default: theme.button.radius,
  },
  viewport: {
    type: "string",
    default: "Desktop",
  },
};

if (theme.mobile.button) {
  attrs[`mobile_fontsize`] = {
    type: "string",
    default: theme.mobile.button.fontsize,
  };
  attrs[`mobile_lineheight`] = {
    type: "number",
    default: theme.mobile.button.lineheight,
  };
  attrs[`mobile_padding`] = {
    type: "object",
    default: theme.mobile.button.padding,
  };
  attrs[`mobile_width`] = {
    type: "string",
  };
  attrs[`mobile_custom_width`] = {
    type: "string",
  };
}

export const attributes = attrs;

export default { attributes };
