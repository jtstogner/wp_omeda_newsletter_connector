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
  registerBlockType('newsletterglue/quote', {
    apiVersion: 2,
    title: __('Quote', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {
      attributes: {
        content: 'In quoting others, we cite ourselves.',
        citation: 'Julio Cortázar',
      },
    },
    description: __('Add quote to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'quote'],
    attributes: attributes,
    edit: Edit,
    save,
    transforms: transforms,
    supports: {
      __experimentalOnEnter: true,
      __experimentalSlashInserter: true,
    }
  });
}