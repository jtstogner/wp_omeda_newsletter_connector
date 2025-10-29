import { theme } from '../../defaults/theme.js';

let attrs = {
  background: {
    type: 'string',
  },
  height: {
    type: 'string',
    default: theme.spacer.height,
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

if (theme.mobile.spacer) {
  attrs[`mobile_height`] = {
    type: 'string',
  }
}

export const attributes = attrs;

export default { attributes }