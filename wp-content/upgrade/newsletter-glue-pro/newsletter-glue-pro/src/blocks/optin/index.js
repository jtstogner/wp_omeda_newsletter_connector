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
  registerBlockType('newsletterglue/optin', {
    apiVersion: 2,
    title: __('Subscriber form', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {
      attributes: {

      },
    },
    description: __('New subscribers can sign up to your mailing list with this form.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'subscribe', 'form'],
    attributes: attributes,
    edit: Edit,
    save,
  });
}