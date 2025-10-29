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
    default: theme.meta.padding,
  },
  font: {
    type: "object",
    default: theme.font,
  },
  fontsize: {
    type: "string",
    default: theme.meta.fontsize,
  },
  lineheight: {
    type: "number",
    default: theme.lineheight,
  },
  post_id: {
    type: "number",
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
  order: {
    type: "array",
    default: [1, 2, 3, 4, 5, 6, 7, 8],
  },
  divider_style: {
    type: "string",
    default: "line",
  },
  read_online_link: {
    type: "string",
    default: "blog",
  },
  show_author: {
    type: "boolean",
    default: true,
  },
  show_issue: {
    type: "boolean",
    default: true,
  },
  show_date: {
    type: "boolean",
    default: true,
  },
  show_title: {
    type: "boolean",
    default: false,
  },
  show_location: {
    type: "boolean",
    default: false,
  },
  show_readtime: {
    type: "boolean",
    default: true,
  },
  show_url: {
    type: "boolean",
    default: true,
  },
  show_meta: {
    type: "boolean",
    default: false,
  },
  title: {
    type: "string",
  },
  issue: {
    type: "string",
  },
  meta: {
    type: "string",
  },
  location: {
    type: "string",
  },
  url: {
    type: "string",
  },
  readingtime: {
    type: "string",
  },
  readtime: {
    type: "string",
  },
  date_format: {
    type: "string",
    default: "F j, Y",
  },
  author_name: {
    type: "string",
  },
  profile_pic: {
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
    default: theme.mobile.meta.fontsize,
  };
  attrs[`mobile_lineheight`] = {
    type: "number",
    default: theme.mobile.meta.lineheight,
  };
  attrs[`mobile_padding`] = {
    type: "object",
    default: theme.mobile.meta.padding,
  };
}

export const attributes = attrs;

export default { attributes };
