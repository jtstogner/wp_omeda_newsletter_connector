import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { attributes } from './attributes';
import Edit from './edit';
import './editor.scss';
import { icon } from './icon.js';
import save from './save';

let loadBlock = true;
if (!nglue_backend.is_allowed_post_type) {
  loadBlock = false;
}

if (loadBlock) {
  registerBlockType('newsletterglue/button', {
    apiVersion: 2,
    title: __('Button', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {
      attributes: {
        text: __('Call to Action'),
      },
    },
    description: __('Add a button to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'button'],
    attributes: attributes,
    edit: Edit,
    save,
    merge: (a, { text = '' }) => ({
      ...a,
      text: (a.text || '') + text,
    }),
    supports: {
      reusable: false,
      align: false,
      __experimentalSelector: '.wp-block-button .wp-block-button__link'
    },
    parent: ['newsletterglue/buttons'],
  });
}