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
  registerBlockType('newsletterglue/post-author', {
    apiVersion: 2,
    title: __('Author byline', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {

    },
    description: __('Add author byline to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'author'],
    attributes: attributes,
    edit: Edit,
    save,
  });
}