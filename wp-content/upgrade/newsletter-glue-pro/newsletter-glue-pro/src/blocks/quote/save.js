import { RichText, useBlockProps } from '@wordpress/block-editor';
import React from 'react';

import { theme } from '../../defaults/theme.js';

export default function save({ attributes }) {

  const color = attributes.color ? attributes.color : theme.color;

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

  const blockProps = useBlockProps.save(attrs);

  const tdStyle = {
    fontSize: attributes.fontsize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: attributes.padding.top,
    paddingBottom: attributes.padding.bottom,
    paddingLeft: attributes.padding.left,
    paddingRight: attributes.padding.right,
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
    fontSize: attributes.fontsize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    color: color,
  }

  const citeStyle = {
    fontSize: attributes.fontsizeCite,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    color: attributes.cite_color ? attributes.cite_color : color,
  }

  return (
    <>
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
                              <RichText.Content tagName="p" value={attributes.content} />
                            </td>
                          </tr>
                          {attributes.citation &&
                            <>
                              <tr><td height="10" style={{ height: '10px' }}></td></tr>
                              <tr>
                                <td className="ng-block-td ng-block-cite" style={citeStyle}>
                                  <RichText.Content tagName="span" value={attributes.citation} />
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