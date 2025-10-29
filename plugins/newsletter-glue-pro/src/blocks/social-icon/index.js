import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { globe } from "@wordpress/icons";
import React from 'react';
import { attributes } from './attributes';
import Edit from './edit';
import './editor.scss';
import { icon } from './icon.js';
import save from './save';

let loadBlock = true;
if (!nglue_backend.is_allowed_post_type) {
  loadBlock = false;
}

const tiktokIcon = (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 50 50"
    width="100%"
    height="100%"
    fill="inherit"
  >
    <path d="M41,4H9C6.243,4,4,6.243,4,9v32c0,2.757,2.243,5,5,5h32c2.757,0,5-2.243,5-5V9C46,6.243,43.757,4,41,4z M37.006,22.323 c-0.227,0.021-0.457,0.035-0.69,0.035c-2.623,0-4.928-1.349-6.269-3.388c0,5.349,0,11.435,0,11.537c0,4.709-3.818,8.527-8.527,8.527 s-8.527-3.818-8.527-8.527s3.818-8.527,8.527-8.527c0.178,0,0.352,0.016,0.527,0.027v4.202c-0.175-0.021-0.347-0.053-0.527-0.053 c-2.404,0-4.352,1.948-4.352,4.352s1.948,4.352,4.352,4.352s4.527-1.894,4.527-4.298c0-0.095,0.042-19.594,0.042-19.594h4.016 c0.378,3.591,3.277,6.425,6.901,6.685V22.323z" />
  </svg>
);

const xIcon = (
  <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
    <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" />
  </svg>
);

const flipboardIcon = (
  <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
    <path d="M0 32v448h448V32H0zm358.4 179.2h-89.6v89.6h-89.6v89.6H89.6V121.6h268.8v89.6z" />
  </svg>
);

if (loadBlock) {
  registerBlockType('newsletterglue/social-icon', {
    apiVersion: 2,
    title: __('Social icon', 'newsletter-glue'),
    category: 'newsletterglue-blocks',
    icon: icon,
    example: {

    },
    description: __('Add social icon to your newsletter.', 'newsletter-glue'),
    keywords: ['newsletter', 'email', 'share', 'social'],
    attributes: attributes,
    edit: Edit,
    save,
    parent: ['newsletterglue/social-icons'],
    supports: {
      reusable: false,
      html: false,
      customClassName: false,
    },
    variations: [
      {
        isDefault: true,
        name: 'instagram',
        title: 'Instagram',
        description: 'Add a social link to Instagram',
        icon: 'instagram',
        attributes: { service: 'instagram' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'x',
        title: 'X',
        description: 'Add a social link to X',
        icon: xIcon,
        attributes: { service: 'x' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'twitter',
        title: 'Twitter',
        description: 'Add a social link to Twitter',
        icon: 'twitter',
        attributes: { service: 'twitter' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'facebook',
        title: 'Facebook',
        description: 'Add a social link to Facebook',
        icon: 'facebook',
        attributes: { service: 'facebook' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'twitch',
        title: 'Twitch',
        description: 'Add a social link to Twitch',
        icon: 'twitch',
        attributes: { service: 'twitch' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'tiktok',
        title: 'Tiktok',
        description: 'Add a social link to Tiktok',
        icon: tiktokIcon,
        attributes: { service: 'tiktok' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'youtube',
        title: 'YouTube',
        description: 'Add a social link to YouTube',
        icon: 'youtube',
        attributes: { service: 'youtube' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'linkedin',
        title: 'LinkedIn',
        description: 'Add a social link to LinkedIn',
        icon: 'linkedin',
        attributes: { service: 'linkedin' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'pinterest',
        title: 'Pinterest',
        description: 'Add a social link to Pinterest',
        icon: 'pinterest',
        attributes: { service: 'pinterest' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'email',
        title: 'Email',
        description: 'Add a social link to Email',
        icon: 'email',
        attributes: { service: 'email' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'web',
        title: 'Web',
        description: 'Add a social link to Web',
        icon: globe,
        attributes: { service: 'web' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'whatsapp',
        title: 'Whatsapp',
        description: 'Add a social link to Whatsapp',
        icon: 'whatsapp',
        attributes: { service: 'whatsapp' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
      {
        name: 'flipboard',
        title: 'Flipboard',
        description: 'Add a social link to Flipboard',
        icon: flipboardIcon,
        attributes: { service: 'flipboard' },
        isActive: function (blockAttributes, variationAttributes) { return blockAttributes.service === variationAttributes.service }
      },
    ]
  });
}