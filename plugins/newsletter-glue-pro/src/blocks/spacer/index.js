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
  registerBlockType('newsletterglue/spacer', {
    apiVersion: 2,
    title: __('Spacer', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    description: __('Add spacing to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'spacer'],
    attributes: attributes,
    edit: Edit,
    save,
  });
}