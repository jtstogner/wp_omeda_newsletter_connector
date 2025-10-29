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
  registerBlockType('newsletterglue/showhide', {
    apiVersion: 2,
    title: __('Show/hide content', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    description: __('Hide selected content from your blog/newsletter.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'group'],
    attributes: attributes,
    edit: Edit,
    save,
  });
}