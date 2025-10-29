import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { attributes } from './attributes';
import Edit from './edit';
import './editor.scss';
import { embedContentIcon } from './icons';
import save from './save';
import transforms from './transforms';

let loadBlock = true;
if (!nglue_backend.is_allowed_post_type) {
  loadBlock = false;
}

if (loadBlock) {
  registerBlockType('newsletterglue/embed', {
    apiVersion: 2,
    title: __('Embed', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: embedContentIcon,
    example: {
      attributes: {

      },
    },
    description: __('Add social embeds to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'embed'],
    attributes: attributes,
    edit: Edit,
    save,
    transforms: transforms,
  });
}