import { theme } from '../../defaults/theme.js';

let attrs = {
  level: {
    type: 'number',
    default: 2,
  },
  content: {
    type: 'string',
    source: 'html',
    selector: 'h1,h2,h3,h4,h5,h6',
  },
  background: {
    type: 'string',
  },
  link: {
    type: 'string',
    default: theme.colors.primary,
  },
  fontweight: {
    type: 'object',
    default: theme.headings.fontweight,
  },
  textAlign: {
    type: 'string',
    default: 'none',
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

[1, 2, 3, 4, 5, 6].map((level) => {
  attrs[`h${level}_padding`] = {
    type: 'object',
    default: theme.headings[`h${level}`].padding
  }
  attrs[`h${level}_font`] = {
    type: 'object',
    default: theme.headings[`h${level}`].font
  }
  attrs[`h${level}_size`] = {
    type: 'string',
    default: theme.headings[`h${level}`].fontsize
  }
  attrs[`h${level}_lineheight`] = {
    type: 'number',
    default: theme.headings[`h${level}`].lineheight
  }
  attrs[`h${level}_colour`] = {
    type: 'string',
  }
});

[1, 2, 3, 4, 5, 6].map((level) => {
  if (theme.mobile.headings[`h${level}`]) {
    attrs[`mobile_h${level}_size`] = {
      type: 'string',
      default: theme.mobile.headings[`h${level}`].fontsize
    }
    attrs[`mobile_h${level}_lineheight`] = {
      type: 'number',
      default: theme.mobile.headings[`h${level}`].lineheight
    }
    attrs[`mobile_h${level}_padding`] = {
      type: 'object',
      default: theme.mobile.headings[`h${level}`].padding
    }
  }
});

export const attributes = attrs;

export default { attributes }