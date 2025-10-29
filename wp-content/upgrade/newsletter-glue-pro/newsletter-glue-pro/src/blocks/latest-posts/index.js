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
  registerBlockType('newsletterglue/latest-posts', {
    apiVersion: 2,
    title: __('Latest posts', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {
      attributes: {

      },
    },
    description: __('Add your latest posts to your newsletters.', 'newsletter-glue'),
    keywords: ['posts', 'email', 'newsletter'],
    attributes: attributes,
    edit: Edit,
    save,
  });
}