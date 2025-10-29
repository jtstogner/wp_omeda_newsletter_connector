import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { attributes } from './attributes';
import Edit from './edit';
import './editor.scss';
import { icon } from './icon.js';
import save from './save';
import transforms from './transforms';

let loadBlock = true;
if (!nglue_backend.is_allowed_post_type) {
  loadBlock = false;
}

if (loadBlock) {
  registerBlockType('newsletterglue/html', {
    apiVersion: 2,
    title: __('Custom HTML', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {
      attributes: {
        content:
          '<marquee>' +
          __('Welcome to the wonderful world of blocks…') +
          '</marquee>',
      },
    },
    description: __('Add custom HTML to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'html'],
    attributes: attributes,
    edit: Edit,
    save,
    transforms: transforms,
    supports: {
      customClassName: false,
      className: false,
      html: false,
    },
  });
}