import React from 'react';

import { AlignmentControl, BlockControls, InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { theme } from '../../defaults/theme.js';
import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

export default function Edit({ attributes, setAttributes, className, isSelected, clientId }) {

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  var color = attributes.color ? attributes.color : theme.color;

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
    bgcolor: attributes.background,
    style: {
      color: color,
      backgroundColor: attributes.background,
    },
  };

  const blockProps = useBlockProps(attrs);

  const tdStyle = {
    fontSize: isMobile ? attributes.mobile_size : attributes.fontsize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: isMobile ? attributes.mobile_lineheight : attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: isMobile ? attributes.mobile_padding.top : attributes.padding.top,
    paddingBottom: isMobile ? attributes.mobile_padding.bottom : attributes.padding.bottom,
    paddingLeft: isMobile ? attributes.mobile_padding.left : attributes.padding.left,
    paddingRight: isMobile ? attributes.mobile_padding.right : attributes.padding.right,
    textAlign: attributes.align,
    color: color,
  }

  const ALLOWED_BLOCKS = [
    'newsletterglue/social-icon',
  ];

  return (
    <>
      <BlockControls group="block">
        <AlignmentControl
          value={attributes.align}
          onChange={(nextAlign) => {
            setAttributes({ align: nextAlign === undefined ? 'none' : nextAlign });
            var children = wp.data.select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks;
            children.forEach(function (child) {
              wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { align: nextAlign });
            });
          }}
        />
      </BlockControls>

      <InspectorControls>
        {deviceType !== 'Mobile' &&
          <Controls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
        {deviceType === 'Mobile' &&
          <MobileControls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
      </InspectorControls>

      <table {...blockProps}>
        <tbody>
          <tr>
            <td className={`ng-block-td ng-block-td-${attributes.align}`} align={attributes.align} style={tdStyle}>
              {attributes.add_description && (
                <RichText
                  identifier="content"
                  tagName="div"
                  className="ngl-share-description ng-div"
                  style={{ color: color, marginBottom: '8px' }}
                  value={attributes.description}
                  onChange={(newValue) => setAttributes({ description: newValue })}
                  placeholder={__('Follow me on')}
                  __unstableEmbedURLOnPaste
                  __unstableAllowPrefixTransformations
                />
              )}
              <div className="ngl-share-wrap ng-div" style={{ lineHeight: 1, fontSize: '1px' }}>
                <InnerBlocks
                  allowedBlocks={ALLOWED_BLOCKS}
                  orientation={'horizontal'}
                  templateLock={false}
                  __experimentalAppenderTagName={'div'}
                  renderAppender={() => (
                    <InnerBlocks.ButtonBlockAppender
                      rootClientId={clientId}
                      className={'ngl-social-appender'}
                    />
                  )}
                />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );

}