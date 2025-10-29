import { theme } from '../../defaults/theme.js';

let attrs = {
  list_id: {
    type: 'string',
  },
  extra_list_id: {
    type: 'string',
  },
  checkbox_text: {
    type: 'string',
  },
  double_optin: {
    type: 'boolean',
    default: false,
  },
  button_text: {
    type: 'string',
    default: 'Subscribe',
  },
  add_heading: {
    type: 'boolean',
    default: false,
  },
  add_description: {
    type: 'boolean',
    default: false,
  },
  add_name: {
    type: 'boolean',
    default: false,
  },
  add_text: {
    type: 'boolean',
    default: false,
  },
  add_checkbox: {
    type: 'boolean',
    default: false,
  },
  name_required: {
    type: 'boolean',
    default: false,
  },
  cb_required: {
    type: 'boolean',
    default: false,
  },
  form_style: {
    type: 'string',
    default: 'portrait',
  },
  form_radius: {
    type: 'number',
    default: 12,
  },
  spacing_size: {
    type: 'number',
    default: 20,
  },
  name_placeholder: {
    type: 'string',
  },
  email_placeholder: {
    type: 'string',
  },
  message_text: {
    type: 'string',
    default: 'Thanks for subscribing.',
  },
  order: {
    type: 'array',
    default: [1, 2, 3, 4, 5],
  },
  background: {
    type: 'string',
  },
  color: {
    type: 'string',
  },
  link: {
    type: 'string',
    default: theme.colors.primary,
  },
  padding: {
    type: 'object',
    default: theme.optin.padding,
  },
  margin: {
    type: 'object',
    default: theme.optin.margin,
  },
  form_header: {
    type: 'string',
  },
  form_description: {
    type: 'string',
  },
  button_fill: {
    type: 'string',
  },
  button_outline: {
    type: 'string',
  },
  button_text_color: {
    type: 'string',
  },
  heading_color: {
    type: 'string',
  },
  description_color: {
    type: 'string',
  },
  label_color: {
    type: 'string',
  },
  input_color: {
    type: 'string',
  },
  text_color: {
    type: 'string',
  },
  checkbox_color: {
    type: 'string',
  },
  success_color: {
    type: 'string',
  },
  name_label: {
    type: 'string',
    default: 'Name',
  },
  email_label: {
    type: 'string',
    default: 'Email',
  },
  form_text: {
    'type': 'string',
  },
  font: {
    type: 'object',
    default: theme.font,
  },
  fontsize: {
    type: 'string',
    default: theme.block.fontsize,
  },
  lineheight: {
    type: 'number',
    default: theme.lineheight,
  },
  fontweight: {
    type: 'object',
    default: theme.fontweight,
  },
  align: {
    type: 'string',
    default: 'none',
  },
  placeholder: {
    type: 'string',
  },
  font_heading: {
    type: 'object',
    default: theme.font,
  },
  font_desc: {
    type: 'object',
    default: theme.font,
  },
  font_input: {
    type: 'object',
    default: theme.font,
  },
  font_label: {
    type: 'object',
    default: theme.font,
  },
  font_text: {
    type: 'object',
    default: theme.font,
  },
  font_checkbox: {
    type: 'object',
    default: theme.font,
  },
  font_button: {
    type: 'object',
    default: theme.font,
  },
  font_success: {
    type: 'object',
    default: theme.font,
  },
  fontsize_heading: {
    type: 'string',
    default: theme.optin.fontsize_heading,
  },
  fontsize_desc: {
    type: 'string',
    default: theme.optin.fontsize_desc,
  },
  fontsize_label: {
    type: 'string',
    default: theme.optin.fontsize_label,
  },
  fontsize_input: {
    type: 'string',
    default: theme.optin.fontsize_input,
  },
  fontsize_text: {
    type: 'string',
    default: theme.optin.fontsize_text,
  },
  fontsize_checkbox: {
    type: 'string',
    default: theme.optin.fontsize_checkbox,
  },
  fontsize_button: {
    type: 'string',
    default: theme.optin.fontsize_button,
  },
  fontsize_success: {
    type: 'string',
    default: theme.optin.fontsize_success,
  },
  show_in_web: {
    type: 'boolean',
    default: true,
  },
  show_in_email: {
    type: 'boolean',
    default: false,
  },
  viewport: {
    type: 'string',
    default: 'Desktop',
  },
};

if (theme.mobile.optin) {
  attrs[`mobile_fontsize_heading`] = {
    type: 'string',
    default: theme.mobile.optin.fontsize_heading
  }
  attrs[`mobile_fontsize_desc`] = {
    type: 'string',
    default: theme.mobile.optin.fontsize_desc
  }
  attrs[`mobile_fontsize_input`] = {
    type: 'string',
    default: theme.mobile.optin.fontsize_input
  }
  attrs[`mobile_fontsize_label`] = {
    type: 'string',
    default: theme.mobile.optin.fontsize_label
  }
  attrs[`mobile_fontsize_text`] = {
    type: 'string',
    default: theme.mobile.optin.fontsize_text
  }
  attrs[`mobile_fontsize_checkbox`] = {
    type: 'string',
    default: theme.mobile.optin.fontsize_checkbox
  }
  attrs[`mobile_fontsize_button`] = {
    type: 'string',
    default: theme.mobile.optin.fontsize_button
  }
  attrs[`mobile_fontsize_success`] = {
    type: 'string',
    default: theme.mobile.optin.fontsize_success
  }
  attrs[`mobile_padding`] = {
    type: 'object',
    default: theme.mobile.optin.padding
  }
  attrs[`mobile_margin`] = {
    type: 'object',
    default: theme.mobile.optin.margin
  }
}

export const attributes = attrs;

export default { attributes }