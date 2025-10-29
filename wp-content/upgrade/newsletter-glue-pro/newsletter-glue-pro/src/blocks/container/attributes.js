import { theme } from "../../defaults/theme.js";

let apps = [];

if (nglue_backend.is_allowed_post_type) {
  apps = newsletterglue_block_show_hide_content.apps;
}

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
    default: theme.container.padding,
  },
  margin: {
    type: "object",
    default: theme.container.margin,
  },
  font: {
    type: "object",
    default: theme.font,
  },
  fontsize: {
    type: "string",
    default: theme.container.fontsize,
  },
  lineheight: {
    type: "number",
    default: theme.container.lineheight,
  },
  fontweight: {
    type: "object",
    default: theme.container.fontweight,
  },
  align: {
    type: "string",
    default: "none",
  },
  placeholder: {
    type: "string",
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

if (theme.mobile.container) {
  attrs[`mobile_size`] = {
    type: "string",
    default: theme.mobile.container.fontsize,
  };
  attrs[`mobile_lineheight`] = {
    type: "number",
    default: theme.mobile.container.lineheight,
  };
  attrs[`mobile_padding`] = {
    type: "object",
    default: theme.mobile.container.padding,
  };
  attrs[`mobile_margin`] = {
    type: "object",
    default: theme.mobile.container.margin,
  };
}

if (apps) {
  apps.forEach((esp) => {
    attrs[`${esp}_conditions`] = {
      type: "array",
      default: [],
    };
  });
}

export const attributes = attrs;

export default { attributes };
