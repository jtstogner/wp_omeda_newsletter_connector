import React from 'react';

import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { select } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
  BaseControl,
  PanelBody,
  TextControl,
  ToggleControl,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

export default function Edit({ attributes, setAttributes, clientId }) {

  const { url_share, icon_size, new_window, share_type, service, icon_color, icon_shape, gap, align, text } = attributes;

  useEffect(() => {
    const parentBlocks = select('core/block-editor').getBlockParents(clientId);
    var last = Object.keys(parentBlocks).pop();
    const parentAttributes = select('core/block-editor').getBlocksByClientId(parentBlocks)[last];

    if (parentAttributes && parentAttributes.attributes) {
      if (icon_color !== parentAttributes.attributes.icon_color) {
        setAttributes({ icon_color: parentAttributes.attributes.icon_color });
      }

      if (icon_shape !== parentAttributes.attributes.icon_shape) {
        setAttributes({ icon_shape: parentAttributes.attributes.icon_shape });
      }

      if (icon_size !== parentAttributes.attributes.icon_size) {
        setAttributes({ icon_size: parentAttributes.attributes.icon_size });
      }

      if (gap !== parentAttributes.attributes.gap) {
        setAttributes({ gap: parentAttributes.attributes.gap });
      }

      if (align !== parentAttributes.attributes.align) {
        setAttributes({ align: parentAttributes.attributes.align });
      }
    }
  }, []);

  useEffect(() => {

    if (share_type === 'text') {
      if (attributes.service === 'x') {
        let updatedURL = 'https://x.com/intent/tweet?text=' + encodeURIComponent(attributes.text);
        setAttributes({ url: updatedURL });
      }
      if (attributes.service === 'twitter') {
        let updatedURL = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(attributes.text);
        setAttributes({ url: updatedURL });
      }
      if (attributes.service === 'facebook') {
        let updatedURL = 'https://facebook.com/sharer/sharer.php?u=' + encodeURIComponent(attributes.text);
        setAttributes({ url: updatedURL });
      }
      if (attributes.service === 'linkedin') {
        let updatedURL = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(attributes.text);
        setAttributes({ url: updatedURL });
      }
    }

  }, [attributes.text]);

  useEffect(() => {
    if (share_type === 'link') {
      let updatedURL = attributes.url_share;
      setAttributes({ url: updatedURL });
    }
  }, [attributes.url_share]);

  useEffect(() => {
    if (share_type === 'link') {
      let updatedURL = attributes.url_share;
      setAttributes({ url: updatedURL });
    } else {
      if (attributes.service === 'x') {
        let updatedURL = 'https://x.com/intent/tweet?text=' + encodeURIComponent(attributes.text);
        setAttributes({ url: updatedURL });
      }
      if (attributes.service === 'twitter') {
        let updatedURL = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(attributes.text);
        setAttributes({ url: updatedURL });
      }
      if (attributes.service === 'facebook') {
        let updatedURL = 'https://facebook.com/sharer/sharer.php?u=' + encodeURIComponent(attributes.text);
        setAttributes({ url: updatedURL });
      }
      if (attributes.service === 'linkedin') {
        let updatedURL = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(attributes.text);
        setAttributes({ url: updatedURL });
      }
    }
  }, [attributes.share_type]);

  var attrs = {
    className: `ng-block ng-social-${attributes.service}`,
  };

  const blockProps = useBlockProps(attrs);

  const innerBlocksProps = useInnerBlocksProps(blockProps, {
    allowedBlocks: ['newsletterglue/social-icons'],
    renderAppender: false,
    __unstableDisableDropZone: true,
  });

  let getLeftMargin = '0px';
  let getRightMargin = '0px';
  if (align == 'center') {
    getLeftMargin = gap;
    getRightMargin = gap;
  } else if (align === 'right') {
    getLeftMargin = gap;
    getRightMargin = '0px';
  } else {
    getLeftMargin = '0px';
    getRightMargin = gap;
  }

  const tags = ['{{link_to_post}}'];

  const unsharableServices = ['instagram', 'twitch', 'tiktok', 'email', 'web', 'pinterest', 'whatsapp', 'flipboard', 'youtube'];

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Social icon options')}>
          {!unsharableServices.includes(attributes.service) && (
            <BaseControl>
              <ToggleGroupControl
                label={__('Share type', 'newsletter-glue')}
                value={share_type}
                onChange={(newValue) => setAttributes({ share_type: newValue })}
                isBlock
              >
                <ToggleGroupControlOption
                  value="link"
                  label={__('Social platform')}
                />
                <ToggleGroupControlOption
                  value="text"
                  label={__('Post')}
                />
              </ToggleGroupControl>
            </BaseControl>
          )}
          {share_type === 'link' ? (
            <BaseControl>
              <TextControl
                label={__('URL', 'newsletter-glue')}
                onChange={(val) => setAttributes({ url_share: val })}
                value={url_share}
                style={{ lineHeight: 1.2 }}
              />
              <div style={{ color: '#777' }}>
                Example:<br />
                https://facebook.com/NewsletterGlue
              </div>
            </BaseControl>
          ) : (
            <BaseControl>
              <TextControl
                label={__('Text / URL', 'newsletter-glue')}
                onChange={(val) => setAttributes({ text: val })}
                value={text ? text : '{{link_to_post}}'}
                style={{ lineHeight: 1.2 }}
              />
              <div className="ngl-tag-insert-help">
                Insert: {' '}
                {tags.map((tag, i) => {
                  return <a key={`tag-${i}`} href="#" onClick={(e) => {
                    e.preventDefault();
                    let currentText = text ? text : '';
                    setAttributes({ text: currentText + ' ' + tag });
                  }}>{tag}</a>
                })}
              </div>
            </BaseControl>
          )}
          <BaseControl>
            <ToggleControl
              label={__('Open link in new tab', 'newsletter-glue')}
              onChange={(val) => setAttributes({ new_window: val })}
              checked={new_window}
            />
          </BaseControl>
        </PanelBody>
      </InspectorControls>

      <span
        {...innerBlocksProps}
        style={{ display: 'inline-flex', marginRight: getRightMargin, marginLeft: getLeftMargin }}
      >
        <img
          src={`${nglue_backend.share_uri}/${icon_shape}/${icon_color}/${service}.png`}
          width={parseInt(icon_size)}
          height={parseInt(icon_size)}
          style={{ width: icon_size, height: icon_size }}
          className={'ngl-inline-image'}
        />
      </span>
    </>
  );

}