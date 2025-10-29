import { theme } from '../../defaults/theme.js';

let attrs = {
  layout: {
    type: 'string',
  },
  background: {
    type: 'string',
  },
  stacked_on_mobile: {
    type: 'boolean',
    default: true,
  },
  padding: {
    type: 'object',
    default: theme.columns.padding,
  },
  show_in_web: {
    type: 'boolean',
    default: true,
  },
  show_in_email: {
    type: 'boolean',
    default: true,
  },
  viewport: {
    type: 'string',
    default: 'Desktop',
  }
};

if (theme.mobile.columns) {
  attrs[`mobile_padding`] = {
    type: 'object',
    default: theme.mobile.columns.padding
  }
}

export const attributes = attrs;

export default { attributes }