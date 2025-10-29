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
  button: {
    type: "string",
    default: theme.colors.primary,
  },
  padding: {
    type: "object",
    default: theme.author.padding,
  },
  font: {
    type: "object",
    default: theme.font,
  },
  fontsize: {
    type: "string",
    default: theme.author.fontsize,
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
  divider_style: {
    type: "string",
    default: "line",
  },
  date_format: {
    type: "string",
    default: "F j, Y",
  },
  author_name: {
    type: "string",
  },
  author_bio: {
    type: "string",
  },
  social: {
    type: "string",
    default: "twitter",
  },
  social_user: {
    type: "string",
  },
  show_button: {
    type: "boolean",
    default: true,
  },
  button_text: {
    type: "string",
  },
  border_radius: {
    type: "number",
    default: 5,
  },
  button_style: {
    type: "string",
    default: "solid",
  },
  icon_style: {
    type: "string",
    default: "default",
  },
  profile_pic: {
    type: "string",
  },
  button_icon: {
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

if (theme.mobile.meta) {
  attrs[`mobile_size`] = {
    type: "string",
    default: theme.mobile.author.fontsize,
  };
  attrs[`mobile_lineheight`] = {
    type: "number",
    default: theme.mobile.author.lineheight,
  };
  attrs[`mobile_padding`] = {
    type: "object",
    default: theme.mobile.author.padding,
  };
}

export const attributes = attrs;

export default { attributes };
