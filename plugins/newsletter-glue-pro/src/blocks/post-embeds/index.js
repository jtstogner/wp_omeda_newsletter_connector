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
  registerBlockType('newsletterglue/post-embeds', {
    apiVersion: 2,
    title: __('Post embeds', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {
      attributes: {

      },
    },
    description: __('Add embedded posts and URLs to your newsletters.', 'newsletter-glue'),
    keywords: ['embed', 'email', 'newsletter'],
    attributes: attributes,
    edit: Edit,
    save,
  });
}