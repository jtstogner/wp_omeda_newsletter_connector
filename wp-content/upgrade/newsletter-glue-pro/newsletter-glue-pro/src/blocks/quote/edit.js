import React from 'react';

import { AlignmentControl, BlockControls, InspectorControls, RichText, store as blockEditorStore, useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { theme } from '../../defaults/theme.js';
import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

export default function Edit({ attributes, setAttributes, className, isSelected, onReplace, onRemove, clientId }) {

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
    bgcolor: attributes.background,
    style: {
      color: color,
      backgroundColor: attributes.background,
    }
  };

  const blockProps = useBlockProps(attrs);

  const tdStyle = {
    fontSize: isMobile ? attributes.mobile_quote_size : attributes.fontsize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: isMobile ? attributes.mobile_quote_lineheight : attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: isMobile ? attributes.mobile_quote_padding.top : attributes.padding.top,
    paddingBottom: isMobile ? attributes.mobile_quote_padding.bottom : attributes.padding.bottom,
    paddingLeft: isMobile ? attributes.mobile_quote_padding.left : attributes.padding.left,
    paddingRight: isMobile ? attributes.mobile_quote_padding.right : attributes.padding.right,
    textAlign: attributes.align,
    color: color,
  }

  const quoteStyle = {
    width: '20px',
    borderLeftWidth: '3px',
    borderLeftStyle: 'solid',
    borderLeftColor: attributes.border,
  }

  const mainQuoteStyle = {
    fontSize: isMobile ? attributes.mobile_quote_size : attributes.fontsize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: isMobile ? attributes.mobile_quote_lineheight : attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    color: color,
  }

  const citeStyle = {
    fontSize: isMobile ? attributes.mobile_quote_citesize : attributes.fontsizeCite,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: isMobile ? attributes.mobile_quote_lineheight : attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    color: attributes.cite_color ? attributes.cite_color : color,
  }

  const hasSelection = useSelect((select) => {
    const { isBlockSelected } = select(blockEditorStore);
    return isBlockSelected(clientId);
  }, []);

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
        <tbody>
          <tr>
            <td className="ng-block-td" align={attributes.align} style={tdStyle}>
              <table width="100%" cellPadding={0} cellSpacing={0}>
                <tbody>
                  <tr>
                    {attributes.border && <td width="20" style={quoteStyle}></td>}
                    <td>
                      <table width="100%" cellPadding={0} cellSpacing={0}>
                        <tbody>
                          <tr>
                            <td className="ng-block-td" style={mainQuoteStyle}>
                              <RichText
                                tagName="p"
                                value={attributes.content}
                                onChange={(content) => setAttributes({ content })}
                                placeholder={attributes.placeholder || __('Type / to choose a block', 'newsletter-glue')}
                                data-custom-placeholder={attributes.placeholder ? true : undefined}
                                onReplace={onReplace}
                                onRemove={onRemove}
                                data-empty={!attributes.content || (attributes.content == '<p></p>') ? true : false}
                              />
                            </td>
                          </tr>
                          {(!RichText.isEmpty(attributes.citation) || hasSelection) &&
                            <>
                              <tr><td height="10" style={{ height: '10px' }}></td></tr>
                              <tr>
                                <td className="ng-block-td ng-block-cite" style={citeStyle}>
                                  <RichText
                                    tagName="span"
                                    multiline={false}
                                    value={attributes.citation}
                                    onChange={(citation) => setAttributes({ citation })}
                                    placeholder={__('Add citation', 'newsletter-glue')}
                                  />
                                </td>
                              </tr>
                            </>
                          }
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );

}