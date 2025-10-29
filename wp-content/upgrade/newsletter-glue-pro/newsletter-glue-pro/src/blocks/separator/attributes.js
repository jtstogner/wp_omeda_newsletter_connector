import { theme } from '../../defaults/theme.js';

let attrs = {
  background: {
    type: 'string',
  },
  color: {
    type: 'string',
  },
  padding: {
    type: 'object',
    default: theme.separator.padding,
  },
  align: {
    type: 'string',
    default: 'center',
  },
  mobile_align: {
    type: 'string',
    default: 'center',
  },
  width: {
    type: 'string',
    default: theme.separator.width,
  },
  height: {
    type: 'string',
    default: theme.separator.height,
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
  },
};

if (theme.mobile.separator) {
  attrs[`mobile_padding`] = {
    type: 'object',
    default: theme.mobile.separator.padding
  }
  attrs[`mobile_width`] = {
    type: 'string',
    default: theme.mobile.separator.width
  }
  attrs[`mobile_height`] = {
    type: 'string',
    default: theme.mobile.separator.height
  }
}

export const attributes = attrs;

export default { attributes }