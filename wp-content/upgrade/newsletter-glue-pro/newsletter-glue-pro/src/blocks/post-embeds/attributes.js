import { theme } from "../../defaults/theme.js";

let attrs = {
  stacked_on_mobile: {
    type: "boolean",
    default: true,
  },
  font: {
    type: "object",
    default: theme.font,
  },
  font_title: {
    type: "object",
    default: theme.font,
  },
  font_text: {
    type: "object",
    default: theme.font,
  },
  font_label: {
    type: "object",
    default: theme.font,
  },
  font_author: {
    type: "object",
    default: theme.font,
  },
  font_button: {
    type: "object",
    default: theme.font,
  },
  containerWidth: {
    type: "string",
  },
  show_divider: {
    type: "boolean",
    default: false,
  },
  divider_size: {
    type: "string",
    default: "1",
  },
  divider_bg: {
    type: "string",
  },
  itemBase: {
    type: "string",
  },
  div1: {
    type: "string",
  },
  div2: {
    type: "string",
  },
  contentstyle: {
    type: "string",
    default: "multi",
  },
  filter: {
    type: "string",
    default: "include",
  },
  image_position: {
    type: "string",
    default: "left",
  },
  table_ratio: {
    type: "string",
    default: "30_70",
  },
  filter_authors: {
    type: "array",
  },
  filter_cpts: {
    type: "array",
  },
  filter_categories: {
    type: "array",
  },
  filter_tags: {
    type: "array",
  },
  hidden_posts: {
    type: "array",
    default: [],
  },
  sortby: {
    type: "object",
  },
  dates: {
    type: "object",
  },
  week_starts: {
    type: "object",
  },
  month_starts: {
    type: "object",
  },
  two_weeks_starts: {
    type: "object",
  },
  starts_time: {
    type: "object",
  },
  border_style: {
    type: "object",
  },
  posts_num: {
    type: "number",
    default: 4,
  },
  columns_num: {
    type: "string",
    default: "one",
  },
  postlength: {
    type: "string",
    default: "excerpt",
  },
  words_num: {
    type: "number",
    default: 30,
  },
  image_radius: {
    type: "number",
    default: 0,
  },
  border_radius: {
    type: "number",
    default: 0,
  },
  border_size: {
    type: "number",
    default: 0,
  },
  update_posts: {
    type: "string",
  },
  show_label: {
    type: "boolean",
    default: true,
  },
  show_author: {
    type: "boolean",
    default: false,
  },
  show_heading: {
    type: "boolean",
    default: true,
  },
  show_image: {
    type: "boolean",
    default: true,
  },
  show_excerpt: {
    type: "boolean",
    default: true,
  },
  show_cta: {
    type: "boolean",
    default: true,
  },
  label_type: {
    type: "string",
    default: "domain",
  },
  cta_type: {
    type: "string",
    default: "link",
  },
  cta_link: {
    type: "string",
    default: "Read more",
  },
  posts: {
    type: "array",
  },
  hash: {
    type: "string",
  },
  custom_data: {
    type: "array",
    default: [],
  },
  embeds: {
    type: "object",
    default: [],
  },
  embeds_order: {
    type: "array",
    default: [],
  },
  fontsize_title: {
    type: "string",
    default: theme.posts.fontsize_title,
  },
  fontsize_text: {
    type: "string",
    default: theme.posts.fontsize_text,
  },
  fontsize_label: {
    type: "string",
    default: theme.posts.fontsize_label,
  },
  fontsize_author: {
    type: "string",
    default: theme.posts.fontsize_author,
  },
  fontsize_button: {
    type: "string",
    default: theme.posts.fontsize_button,
  },
  background_color: {
    type: "string",
  },
  border_color: {
    type: "string",
  },
  text_color: {
    type: "string",
  },
  title_color: {
    type: "string",
  },
  label_color: {
    type: "string",
  },
  author_color: {
    type: "string",
  },
  link: {
    type: "string",
    default: theme.colors.primary,
  },
  button: {
    type: "string",
    default: theme.colors.btn_bg,
  },
  button_text: {
    type: "string",
    default: theme.colors.btn_colour,
  },
  padding: {
    type: "object",
    default: theme.posts.padding,
  },
  margin: {
    type: "object",
    default: theme.posts.margin,
  },
  show_in_web: {
    type: "boolean",
    default: true,
  },
  show_in_email: {
    type: "boolean",
    default: true,
  },
  taxonomies: {
    type: "array",
    default: [],
  },
  order: {
    type: "array",
    default: [1, 2, 3, 4, 5],
  },
  viewport: {
    type: "string",
    default: "Desktop",
  },
};

const tax_types = nglue_backend.tax_types;

tax_types.forEach((taxonomy) => {
  attrs[`${taxonomy}_terms`] = {
    type: "array",
    default: [],
  };
});

if (theme.mobile.posts) {
  attrs[`mobile_padding`] = {
    type: "object",
    default: theme.mobile.posts.padding,
  };
  attrs[`mobile_margin`] = {
    type: "object",
    default: theme.mobile.posts.margin,
  };
  attrs[`mobile_fontsize_title`] = {
    type: "string",
    default: theme.mobile.posts.fontsize_title,
  };
  attrs[`mobile_fontsize_text`] = {
    type: "string",
    default: theme.mobile.posts.fontsize_text,
  };
  attrs[`mobile_fontsize_label`] = {
    type: "string",
    default: theme.mobile.posts.fontsize_label,
  };
  attrs[`mobile_fontsize_author`] = {
    type: "string",
    default: theme.mobile.posts.fontsize_author,
  };
  attrs[`mobile_fontsize_button`] = {
    type: "string",
    default: theme.mobile.posts.fontsize_button,
  };
}

export const attributes = attrs;

export default { attributes };
