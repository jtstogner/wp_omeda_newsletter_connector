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
  registerBlockType('newsletterglue/image', {
    apiVersion: 2,
    title: __('Image', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {

    },
    description: __('Add images to your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'image'],
    attributes: attributes,
    edit: Edit,
    save,
    __experimentalLabel(attributes, { context }) {
      if (context === 'accessibility') {
        const { caption, alt, url } = attributes;

        if (!url) {
          return __('Empty');
        }

        if (!alt) {
          return caption || '';
        }

        // This is intended to be read by a screen reader.
        // A period simply means a pause, no need to translate it.
        return alt + (caption ? '. ' + caption : '');
      }
    },
    getEditWrapperProps(attributes) {
      return {
        'data-align': attributes.align,
      };
    },
    transforms: transforms,
  });
}