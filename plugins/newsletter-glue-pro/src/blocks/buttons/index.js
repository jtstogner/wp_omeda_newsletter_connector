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
  registerBlockType('newsletterglue/buttons', {
    apiVersion: 2,
    title: __('Buttons', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {
      innerBlocks: [
        {
          name: 'newsletterglue/button',
          attributes: { text: __('Find out more') },
        },
        {
          name: 'newsletterglue/button',
          attributes: { text: __('Contact us') },
        },
      ],
    },
    description: __('Add buttons to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'buttons'],
    attributes: attributes,
    edit: Edit,
    save,
    transforms: transforms,
    supports: {
      anchor: true,
      html: false,
      __experimentalExposeControlsToChildren: true,
    }
  });
}