import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { attributes } from './attributes.js';
import Edit from './edit.js';
import './editor.scss';
import { icon } from './icon.js';
import save from './save.js';

let loadBlock = true;
if (!nglue_backend.is_allowed_post_type) {
  loadBlock = false;
}

if (loadBlock) {
  registerBlockType('newsletterglue/ad-inserter', {
    apiVersion: 2,
    title: __('Ad Inserter', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {},
    description: __('Insert advertisement images in your email.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'image', 'ad', 'advertisement'],
    attributes: attributes,
    edit: Edit,
    save,
    __experimentalLabel(attributes, { context }) {
      if (context === 'accessibility') {
        const { alt, url } = attributes;

        if (!url) {
          return __('Empty');
        }

        if (!alt) {
          return '';
        }

        return alt;
      }
    },
    getEditWrapperProps(attributes) {
      return {
        'data-align': attributes.align,
      };
    },
  });
}
