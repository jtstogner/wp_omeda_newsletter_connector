let apps = [];

if (nglue_backend.is_allowed_post_type) {
  apps = newsletterglue_block_show_hide_content.apps;
}

let attrs = {
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

if (apps) {
  apps.forEach((esp) => {
    attrs[`${esp}_conditions`] = {
      type: 'array',
      default: []
    }
  });
}

export const attributes = attrs;

export default { attributes }