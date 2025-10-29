let attrs = {
  content: {
    type: 'string',
    source: 'raw',
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

export const attributes = attrs;

export default { attributes }