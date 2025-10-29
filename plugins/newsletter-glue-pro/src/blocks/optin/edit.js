import { AlignmentControl, BlockControls, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import React from 'react';

import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

import { theme } from '../../defaults/theme.js';
import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

export default function Edit({ attributes, setAttributes, className, isSelected, clientId }) {

  const [showMsg, setShowMsg] = useState(false);

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
    className: classnames(className, {
      'ng-block': true,
      'is-landscape': attributes.form_style === 'landscape',
      'is-portrait': attributes.form_style === 'portrait',
    }),
    style: {
      color: color,
    },
  };

  const blockProps = useBlockProps(attrs);

  let fontSize = isMobile ? attributes.mobile_p_size : attributes.fontsize;
  if (typeof fontSize === 'string' && !fontSize.includes('px')) {
    fontSize = fontSize + 'px';
  }

  const tdStyle = {
    fontSize: fontSize,
    fontFamily: nglue_backend.font_names[attributes.font.key],
    lineHeight: isMobile ? attributes.mobile_p_lineheight : attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: isMobile ? attributes.mobile_padding.top : attributes.padding.top,
    paddingBottom: isMobile ? attributes.mobile_padding.bottom : attributes.padding.bottom,
    paddingLeft: isMobile ? attributes.mobile_padding.left : attributes.padding.left,
    paddingRight: isMobile ? attributes.mobile_padding.right : attributes.padding.right,
    textAlign: attributes.align,
    color: color,
    backgroundColor: attributes.background,
  }

  const margin = isMobile ? attributes.mobile_margin : attributes.margin;

  const layout = attributes.form_style;

  var buttonFill = attributes.button_fill ? attributes.button_fill : theme.colors.btn_bg;

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
          <Controls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} showMsg={showMsg} setShowMsg={setShowMsg} />
        }
        {deviceType === 'Mobile' &&
          <MobileControls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
      </InspectorControls>

      <table {...blockProps}>
        <tbody>
          {margin.top && (<tr><td height={parseInt(margin.top)}></td></tr>)}
          <tr>
            {margin.left && <td className="ng-block-spacer" style={{ width: margin.left }} width={parseInt(margin.left)}></td>}
            <td className="ng-block-td" style={tdStyle}>

              <div
                className="ng-block-form ngl-form"
                style={{ display: 'flex', gap: attributes.spacing_size, flexDirection: 'column', position: 'relative', margin: 0 }}
              >

                <div className="ngl-form-errors" style={{
                  display: 'none',
                  background: '#ffcece',
                  color: '#672d2d',
                  padding: '11px 20px',
                  textAlign: 'left',
                  border: '1px solid #ec9d9d',
                  lineHeight: '22px',
                  borderRadius: attributes.form_radius ? attributes.form_radius : 0,
                  fontFamily: nglue_backend.font_names[attributes.font_input.key],
                  fontSize: isMobile ? attributes.mobile_fontsize_input : attributes.fontsize_input,
                }}>{' '}</div>

                {showMsg && (
                  <div className="ng-form-overlay"
                    style={{
                      position: 'absolute',
                      top: 0,
                      left: 0,
                      width: '100%',
                      height: '100%',
                      background: '#ffffff',
                      zIndex: 9999,
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      flexDirection: 'column',
                    }}>
                    <div className="ng-form-overlay-icon" style={{ color: '#169620' }}>
                      <svg
                        style={{ width: '32px', height: '32px' }}
                        stroke="currentColor"
                        fill="currentColor"
                        strokeWidth="0"
                        viewBox="0 0 1024 1024"
                        height="32"
                        width="32"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm193.5 301.7l-210.6 292a31.8 31.8 0 0 1-51.7 0L318.5 484.9c-3.8-5.3 0-12.7 6.5-12.7h46.9c10.2 0 19.9 4.9 25.9 13.3l71.2 98.8 157.2-218c6-8.3 15.6-13.3 25.9-13.3H699c6.5 0 10.3 7.4 6.5 12.7z"></path></svg>
                    </div>
                    <div className="ng-form-overlay-text" style={{
                      fontFamily: nglue_backend.font_names[attributes.font_success.key],
                      fontSize: isMobile ? attributes.mobile_fontsize_success : attributes.fontsize_success,
                      color: attributes.success_color ? attributes.success_color : color,
                    }} >{attributes.message_text}</div>
                  </div>
                )}

                {attributes.add_heading && (
                  <RichText
                    tagName="div"
                    value={attributes.form_header}
                    className="ng-form-header"
                    placeholder={__('Enter heading...', 'newsletter-glue')}
                    style={{
                      fontFamily: nglue_backend.font_names[attributes.font_heading.key],
                      fontSize: isMobile ? attributes.mobile_fontsize_heading : attributes.fontsize_heading,
                      color: attributes.heading_color ? attributes.heading_color : theme.headings['h3'].color,
                      lineHeight: 1.1,
                    }}
                    allowedFormats={['core/bold', 'core/link', 'core/italic']}
                    onChange={(value) => setAttributes({ form_header: value })}
                  />
                )}

                {attributes.add_description && (
                  <RichText
                    tagName="div"
                    value={attributes.form_description}
                    className="ng-form-description"
                    placeholder={__('Enter description...', 'newsletter-glue')}
                    style={{
                      fontFamily: nglue_backend.font_names[attributes.font_desc.key],
                      fontSize: isMobile ? attributes.mobile_fontsize_desc : attributes.fontsize_desc,
                      color: attributes.description_color ? attributes.description_color : color,
                      lineHeight: 1.5,
                    }}
                    allowedFormats={['core/bold', 'core/link', 'core/italic']}
                    onChange={(value) => setAttributes({ form_description: value })}
                  />
                )}

                <div
                  className="ngl-form-wrap"
                  style={{
                    display: 'flex',
                    gap: attributes.spacing_size,
                    flexDirection: layout === 'landscape' ? 'row' : 'column',
                    alignItems: layout === 'landscape' ? 'flex-end' : 'initial',
                  }}
                >

                  {attributes.add_name && (
                    <div className="ngl-form-field">
                      <RichText
                        tagName="div"
                        value={attributes.name_label}
                        className={`ngl-form-label${attributes.name_required ? ' ngl-form-label-req' : ''}`}
                        format='string'
                        placeholder={__('Name', 'newsletter-glue')}
                        style={{
                          fontFamily: nglue_backend.font_names[attributes.font_label.key],
                          fontSize: isMobile ? attributes.mobile_fontsize_label : attributes.fontsize_label,
                          color: attributes.label_color ? attributes.label_color : color,
                          lineHeight: 1.6,
                          marginBottom: '4px',
                          display: 'block',
                        }}
                        onChange={(value) => setAttributes({ name_label: value })}
                      />
                      <RichText
                        tagName="div"
                        value={attributes.name_placeholder}
                        className="ngl-form-input"
                        format='string'
                        fieldid={'name'}
                        style={{
                          fontFamily: nglue_backend.font_names[attributes.font_input.key],
                          fontSize: isMobile ? attributes.mobile_fontsize_input : attributes.fontsize_input,
                          color: attributes.input_color ? attributes.input_color : color,
                          borderRadius: attributes.form_radius ? attributes.form_radius : 0,
                          border: '1px solid #aaa',
                          padding: '8px 15px',
                          width: '100%',
                          boxSizing: 'border-box',
                          lineHeight: 1.6,
                        }}
                        onChange={(value) => setAttributes({ name_placeholder: value })}
                      />
                    </div>
                  )}

                  <div className="ngl-form-field">
                    <RichText
                      tagName="div"
                      value={attributes.email_label}
                      className="ngl-form-label"
                      format='string'
                      placeholder={__('Email', 'newsletter-glue')}
                      style={{
                        fontFamily: nglue_backend.font_names[attributes.font_label.key],
                        fontSize: isMobile ? attributes.mobile_fontsize_label : attributes.fontsize_label,
                        color: attributes.label_color ? attributes.label_color : color,
                        lineHeight: 1.6,
                        marginBottom: '4px',
                        display: 'block',
                      }}
                      onChange={(value) => setAttributes({ email_label: value })}
                    />
                    <RichText
                      tagName="div"
                      value={attributes.email_placeholder}
                      className="ngl-form-input"
                      format='string'
                      fieldid={'email'}
                      style={{
                        fontFamily: nglue_backend.font_names[attributes.font_input.key],
                        fontSize: isMobile ? attributes.mobile_fontsize_input : attributes.fontsize_input,
                        color: attributes.input_color ? attributes.input_color : color,
                        borderRadius: attributes.form_radius ? attributes.form_radius : 0,
                        border: '1px solid #aaa',
                        padding: '8px 15px',
                        width: '100%',
                        boxSizing: 'border-box',
                        lineHeight: 1.6,
                      }}
                      onChange={(value) => setAttributes({ email_placeholder: value })}
                    />
                  </div>

                  {attributes.add_checkbox && layout != 'landscape' && (
                    <div className="ngl-form-checkbox" style={{ display: 'flex', alignItems: 'flex-start', gap: '8px' }}>
                      <input
                        type="checkbox"
                        name="ngl_extra_list"
                        id="ngl_extra_list"
                        className="ng-form-extra-list"
                        style={{
                          top: '0px',
                          position: 'relative',
                          margin: '0px',
                        }}
                      />
                      <RichText
                        tagName="div"
                        className="ng-form-checkbox-text"
                        value={attributes.checkbox_text}
                        placeholder={'Enter text for checkbox...'}
                        format='string'
                        style={{
                          fontFamily: nglue_backend.font_names[attributes.font_checkbox.key],
                          fontSize: isMobile ? attributes.mobile_fontsize_checkbox : attributes.fontsize_checkbox,
                          color: attributes.checkbox_color ? attributes.checkbox_color : color,
                          lineHeight: 1.1,
                        }}
                        onChange={(value) => setAttributes({ checkbox_text: value })}
                      />
                    </div>
                  )}

                  <div className="ng-form-div">
                    <RichText
                      tagName="div"
                      className="ng-form-button"
                      value={attributes.button_text}
                      placeholder={'Subscribe'}
                      format='string'
                      style={{
                        fontFamily: nglue_backend.font_names[attributes.font_button.key],
                        fontSize: isMobile ? attributes.mobile_fontsize_button : attributes.fontsize_button,
                        cursor: 'pointer',
                        borderRadius: attributes.form_radius ? attributes.form_radius : 0,
                        color: attributes.button_text_color ? attributes.button_text_color : theme.colors.btn_colour,
                        backgroundColor: buttonFill,
                        borderWidth: '1px',
                        borderStyle: 'solid',
                        borderColor: attributes.button_outline ? attributes.button_outline : buttonFill,
                        textAlign: 'center',
                        padding: '8px 20px',
                        lineHeight: 1.6,
                        width: '100%',
                        boxSizing: 'border-box',
                      }}
                      onChange={(value) => setAttributes({ button_text: value })}
                    />
                  </div>

                </div>

                {layout === 'landscape' && attributes.add_checkbox && (
                  <div className="ngl-form-checkbox" style={{ display: 'flex', alignItems: 'flex-start', gap: '8px' }}>
                    <input
                      type="checkbox"
                      name="ngl_extra_list"
                      id="ngl_extra_list"
                      className="ng-form-extra-list"
                      style={{
                        top: '0px',
                        position: 'relative',
                        margin: '0px',
                      }}
                    />
                    <RichText
                      tagName="div"
                      className="ng-form-checkbox-text"
                      value={attributes.checkbox_text}
                      placeholder={'Enter text for checkbox...'}
                      format='string'
                      style={{
                        fontFamily: nglue_backend.font_names[attributes.font_checkbox.key],
                        fontSize: isMobile ? attributes.mobile_fontsize_checkbox : attributes.fontsize_checkbox,
                        color: attributes.checkbox_color ? attributes.checkbox_color : color,
                        lineHeight: 1.1,
                      }}
                      onChange={(value) => setAttributes({ checkbox_text: value })}
                    />
                  </div>
                )}

                {attributes.add_text && (
                  <RichText
                    tagName="div"
                    className="ng-form-text"
                    value={attributes.form_text}
                    placeholder={'Enter text...'}
                    format='string'
                    style={{
                      fontFamily: nglue_backend.font_names[attributes.font_text.key],
                      fontSize: isMobile ? attributes.mobile_fontsize_text : attributes.fontsize_text,
                      color: attributes.text_color ? attributes.text_color : color,
                      textAlign: layout === 'landscape' ? 'left' : 'center',
                      lineHeight: 1.6,
                    }}
                    onChange={(value) => setAttributes({ form_text: value })}
                  />
                )}

              </div>

            </td>
            {margin.right && <td className="ng-block-spacer" style={{ width: margin.right }} width={parseInt(margin.right)}></td>}
          </tr>
          {margin.bottom && (<tr><td height={parseInt(margin.bottom)}></td></tr>)}
        </tbody>
      </table>
    </>
  );

}