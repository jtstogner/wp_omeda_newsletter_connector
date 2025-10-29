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
  registerBlockType('newsletterglue/social-icons', {
    apiVersion: 2,
    title: __('Social icons', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {

    },
    description: __('Add social icons to your newsletter.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'share', 'social'],
    attributes: attributes,
    edit: Edit,
    save,
  });
}