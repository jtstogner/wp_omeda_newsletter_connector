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
  registerBlockType('newsletterglue/table', {
    apiVersion: 2,
    title: __('Table', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {

    },
    description: __('Add a table to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'table'],
    attributes: attributes,
    edit: Edit,
    save,
    transforms: transforms,
  });
}