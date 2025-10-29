import React from 'react';

import { AlignmentControl, BlockControls, InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { dispatch, select, useSelect } from '@wordpress/data';
import { useEffect, useRef } from '@wordpress/element';
import { Icon, seen } from '@wordpress/icons';

import { theme } from '../../defaults/theme.js';
import { allowedBlocks } from './allowed-blocks.js';
import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

export default function Edit({ attributes, setAttributes, className, isSelected, clientId }) {

  const color = attributes.color ? attributes.color : theme.color;

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
    style: {
      color: color,
    },
  };

  const blockProps = useBlockProps(attrs);

  var tdStyle = {
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
    backgroundColor: attributes.background,
    borderRadius: attributes.radius ? attributes.radius : '0px',
  }

  if (attributes.borderSize) {
    tdStyle['borderWidth'] = attributes.borderSize;
    tdStyle['borderStyle'] = 'solid',
      tdStyle['borderColor'] = attributes.border ? attributes.border : attributes.background;
  }

  const blockTemplate = [
    ['newsletterglue/text', {}]
  ];

  const margin = attributes.margin;

  let colspan = 1;
  if (margin.left) {
    colspan = colspan + 1;
  }

  if (margin.right) {
    colspan = colspan + 1;
  }

  const mounteda = useRef();
  const mountedb = useRef();
  const mountedc = useRef();

  useEffect(() => {
    if (!mounteda.current) {
      mounteda.current = true;
    } else {

      const { getBlock } = wp.data.select('core/block-editor');
      const { updateBlockAttributes } = dispatch('core/block-editor');
      const innerBlocks = select("core/block-editor").getBlockOrder(clientId);

      innerBlocks.forEach((id) => {
        var block = getBlock(id);
        if (['newsletterglue/text', 'newsletterglue/quote', 'newsletterglue/list'].includes(block.name)) {
          updateBlockAttributes(id, { font: attributes.font });
        }
        if (block.name === 'newsletterglue/heading') {
          updateBlockAttributes(id, { h1_font: attributes.font, h2_font: attributes.font, h3_font: attributes.font, h4_font: attributes.font, h5_font: attributes.font, h6_font: attributes.font });
        }
        if (block.name === 'newsletterglue/buttons') {
          var buttons = select("core/block-editor").getBlockOrder(id);
          buttons.forEach((buttonId) => {
            updateBlockAttributes(buttonId, { font: attributes.font });
          });
        }
      });

    }
  }, [attributes.font]);

  useEffect(() => {
    if (!mountedb.current) {
      mountedb.current = true;
    } else {

      const { getBlock } = wp.data.select('core/block-editor');
      const { updateBlockAttributes } = dispatch('core/block-editor');
      const innerBlocks = select("core/block-editor").getBlockOrder(clientId);

      innerBlocks.forEach((id) => {
        var block = getBlock(id);
        if (['newsletterglue/text', 'newsletterglue/quote', 'newsletterglue/list'].includes(block.name)) {
          updateBlockAttributes(id, { color: attributes.color });
        }
        if (block.name === 'newsletterglue/heading') {
          updateBlockAttributes(id, { h1_colour: attributes.color, h2_colour: attributes.color, h3_colour: attributes.color, h4_colour: attributes.color, h5_colour: attributes.color, h6_colour: attributes.color });
        }
      });

    }
  }, [attributes.color]);

  let app = newsletterglue_meta?.app;
  const showconditions = newsletterglue_block_show_hide_content.showconditions;

  /* Show warning */
  var showWarning = false;
  if (showconditions) {
    showWarning = true;
    if (attributes.show_in_web && attributes.show_in_email) {
      if (attributes[`${app}_conditions`]?.length === 0) {
        showWarning = false;
      }
    }
  }

  useEffect(() => {
    if (!mountedc.current) {
      mountedc.current = true;
    } else {

      const { getBlock } = wp.data.select('core/block-editor');
      const { updateBlockAttributes } = dispatch('core/block-editor');
      const innerBlocks = select("core/block-editor").getBlockOrder(clientId);

      innerBlocks.forEach((id) => {
        var block = getBlock(id);
        if (['newsletterglue/text', 'newsletterglue/quote', 'newsletterglue/list'].includes(block.name)) {
          updateBlockAttributes(id, { link: attributes.link });
        }
        if (['newsletterglue/quote'].includes(block.name)) {
          updateBlockAttributes(id, { border: attributes.link });
        }
        if (block.name === 'newsletterglue/buttons') {
          var buttons = select("core/block-editor").getBlockOrder(id);
          buttons.forEach((buttonId) => {
            updateBlockAttributes(buttonId, { background: attributes.link });
          });
        }
      });

    }
  }, [attributes.link]);

  return (
    <>
      <BlockControls group="block">
        <AlignmentControl
          value={attributes.align}
          onChange={(nextAlign) => {
            setAttributes({ align: nextAlign === undefined ? 'none' : nextAlign });
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
        {showWarning && (
          <div style={{
            position: 'absolute',
            right: 0,
            top: 0,
            width: '30px',
            height: '30px',
            backgroundColor: '#eee',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
          }}>
            <Icon icon={seen} />
          </div>
        )}
        <tbody>
          {margin && margin.top && (
            <tr><td className="ng-block-vs ng-block-vs-1" style={{ height: margin.top }} height={parseInt(margin.top, 10)} colSpan={colspan}></td></tr>
          )}
          <tr>
            {margin && margin.left && (
              <td className="ng-block-hs ng-block-hs-1" style={{ width: margin.left }} height={parseInt(margin.left, 10)}></td>
            )}
            <td className="ng-block-td" align={attributes.align} style={tdStyle}>
              <InnerBlocks template={blockTemplate} allowedBlocks={allowedBlocks} orientation="vertical" />
            </td>
            {margin && margin.right && (
              <td className="ng-block-hs ng-block-hs-2" style={{ width: margin.right }} height={parseInt(margin.right, 10)}></td>
            )}
          </tr>
          {margin && margin.bottom && (
            <tr><td className="ng-block-vs ng-block-vs-2" style={{ height: margin.bottom }} height={parseInt(margin.bottom, 10)} colSpan={colspan}></td></tr>
          )}
        </tbody>
      </table>
    </>
  );

}