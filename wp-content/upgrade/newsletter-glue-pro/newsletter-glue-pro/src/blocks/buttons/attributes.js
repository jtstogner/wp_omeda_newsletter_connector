import { theme } from '../../defaults/theme.js';

let attrs = {
  spacing: {
    type: 'string',
    default: theme.buttons.spacing,
  },
  padding: {
    type: 'object',
    default: theme.buttons.padding,
  },
  justify: {
    type: 'string',
    default: 'left',
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

if (theme.mobile.buttons) {
  attrs[`mobile_padding`] = {
    type: 'object',
    default: theme.mobile.buttons.padding
  }
  attrs[`mobile_spacing`] = {
    type: 'string',
    default: theme.mobile.buttons.spacing
  }
  attrs[`mobile_justify`] = {
    type: 'string'
  }
}

export const attributes = attrs;

export default { attributes }