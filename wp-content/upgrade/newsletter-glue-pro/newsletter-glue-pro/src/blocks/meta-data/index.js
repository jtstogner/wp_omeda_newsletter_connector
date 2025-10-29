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
  registerBlockType('newsletterglue/meta-data', {
    apiVersion: 2,
    title: __('Metadata', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {

    },
    description: __('Add meta data to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'meta'],
    attributes: attributes,
    edit: Edit,
    save,
  });
}