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
  registerBlockType('newsletterglue/list-item', {
    apiVersion: 2,
    title: __('List item', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    description: __('Add a list item to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'list'],
    attributes: attributes,
    edit: Edit,
    save,
    merge(attributes, attributesToMerge) {
      return {
        ...attributes,
        content: attributes.content + attributesToMerge.content,
      };
    },
    supports: {
      className: false,
      __experimentalSelector: 'li'
    },
    parent: ['newsletterglue/list'],
    transforms: transforms
  });
}