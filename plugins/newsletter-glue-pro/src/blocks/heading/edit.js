import React from 'react';

import { AlignmentControl, BlockControls, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { theme } from '../../defaults/theme.js';
import { Controls } from './controls.js';
import HeadingLevelDropdown from './heading-level-dropdown';
import { MobileControls } from './mobile-controls.js';

export default function Edit({ attributes, setAttributes, className, isSelected, mergeBlocks, onReplace, clientId }) {

  const font_key = attributes[`h${attributes.level}_font`].key;

  const color = attributes[`h${attributes.level}_colour`] ? attributes[`h${attributes.level}_colour`] : theme.headings[`h${attributes.level}`].color;

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block ng-block-h${attributes.level}`,
    bgcolor: attributes.background,
    style: {
      color: color,
      backgroundColor: attributes.background,
    }
  };

  const blockProps = useBlockProps(attrs);

  const tdStyle = {
    fontSize: isMobile && attributes[`mobile_h${attributes.level}_size`] ? attributes[`mobile_h${attributes.level}_size`] : attributes[`h${attributes.level}_size`],
    fontFamily: nglue_backend.font_names[font_key],
    lineHeight: isMobile && attributes[`mobile_h${attributes.level}_lineheight`] ? attributes[`mobile_h${attributes.level}_lineheight`] : attributes[`h${attributes.level}_lineheight`],
    fontWeight: attributes.fontweight.key,
    paddingTop: isMobile && attributes[`mobile_h${attributes.level}_padding`] ? attributes[`mobile_h${attributes.level}_padding`].top : attributes[`h${attributes.level}_padding`].top,
    paddingBottom: isMobile && attributes[`mobile_h${attributes.level}_padding`] ? attributes[`mobile_h${attributes.level}_padding`].bottom : attributes[`h${attributes.level}_padding`].bottom,
    paddingLeft: isMobile && attributes[`mobile_h${attributes.level}_padding`] ? attributes[`mobile_h${attributes.level}_padding`].left : attributes[`h${attributes.level}_padding`].left,
    paddingRight: isMobile && attributes[`mobile_h${attributes.level}_padding`] ? attributes[`mobile_h${attributes.level}_padding`].right : attributes[`h${attributes.level}_padding`].right,
    textAlign: attributes.textAlign,
    color: color,
  }

  const elStyle = {
    fontSize: isMobile && attributes[`mobile_h${attributes.level}_size`] ? attributes[`mobile_h${attributes.level}_size`] : attributes[`h${attributes.level}_size`],
    fontFamily: nglue_backend.font_names[font_key],
    lineHeight: isMobile && attributes[`mobile_h${attributes.level}_lineheight`] ? attributes[`mobile_h${attributes.level}_lineheight`] : attributes[`h${attributes.level}_lineheight`],
    fontWeight: attributes.fontweight.key,
    textAlign: attributes.textAlign,
    color: color,
  }

  const tagName = 'h' + attributes.level;

  return (
    <>
      <BlockControls group="block">
        <HeadingLevelDropdown
          selectedLevel={attributes.level}
          onChange={(newLevel) =>
            setAttributes({ level: newLevel })
          }
        />
        <AlignmentControl
          value={attributes.textAlign}
          onChange={(nextAlign) => {
            setAttributes({ textAlign: nextAlign === undefined ? 'none' : nextAlign });
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
            <td className="ng-block-td" align={attributes.textAlign} style={tdStyle}>
              <RichText
                identifier="content"
                tagName={tagName}
                style={elStyle}
                value={attributes.content}
                onChange={(content) => setAttributes({ content })}
                placeholder={attributes.placeholder || __('Enter heading...', 'newsletter-glue')}
                onReplace={onReplace}
                onRemove={() => onReplace([])}
                onMerge={mergeBlocks}
                data-empty={!attributes.content ? true : false}
                onSplit={(value, isOriginal) => {
                  let block;

                  if (isOriginal || value) {
                    block = createBlock('newsletterglue/heading', {
                      ...attributes,
                      content: value,
                    });
                  } else {
                    block = createBlock(
                      // eslint-disable-next-line 
                      'newsletterglue/text' ?? 'newsletterglue/heading'
                    );
                  }

                  if (isOriginal) {
                    block.clientId = clientId;
                  }

                  return block;
                }}
              />
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );

}