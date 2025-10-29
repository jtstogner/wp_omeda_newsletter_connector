import { theme } from '../../defaults/theme.js';

let attrs = {
  background: {
    type: 'string',
  },
  width: {
    type: 'number',
  },
  originalWidth: {
    type: 'number',
  },
  verticalAlign: {
    type: 'string',
    default: 'top',
  },
  padding: {
    type: 'object',
    default: theme.column.padding,
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

if (theme.mobile.column) {
  attrs[`mobile_padding`] = {
    type: 'object',
    default: theme.mobile.column.padding
  }
}

export const attributes = attrs;

export default { attributes }