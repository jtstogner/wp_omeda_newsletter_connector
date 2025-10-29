import React from 'react';

import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { theme } from '../../defaults/theme.js';
import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';
import { getSocialColor } from './utils.js';

export default function Edit({ attributes, setAttributes, className, isSelected, clientId }) {

  useEffect(() => {
    if (!attributes.author_name) {
      setAttributes({ author_name: newsletterglue_meta.author_name })
    }

    if (!attributes.profile_pic) {
      setAttributes({ profile_pic: newsletterglue_meta.profile_pic })
    }

    if (!attributes.button_text) {
      setAttributes({ button_text: __('Follow') })
    }

  }, []);

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
    color: color,
  }

  var tableAttrs = {
    width: 'auto',
    cellPadding: 0,
    cellSpacing: 0
  };

  let avatarWidth = 50;
  let iconWidth = 16;

  var platform = attributes.social ? attributes.social : 'twitter';
  var outline = attributes.button_style === 'solid' ? '' : '-fill';

  var linkColor = attributes.button_style === 'solid' ? '#fff' : attributes.button;
  var linkBackground = attributes.button_style === 'solid' ? getSocialColor(attributes.social, attributes.button) : 'transparent';
  var linkBorder = '2px solid ' + getSocialColor(attributes.social, attributes.button);

  let iconURL = `${nglue_backend.images_uri}/social/${platform}${outline}.png`;
  if (attributes.icon_style === 'custom' && attributes.button_icon) {
    iconURL = attributes.button_icon;
  }
  if (attributes.icon_style === 'custom' && !attributes.button_icon) {
    iconURL = '';
  }
  if (attributes.icon_style === 'default' && attributes.social === 'custom') {
    iconURL = '';
  }
  if (attributes.icon_style === 'no_icon') {
    iconURL = '';
  }

  return (
    <>
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
            <td className="ng-block-td" style={tdStyle}>
              <table {...tableAttrs}>
                <tbody>
                  <tr>
                    <td style={{ width: avatarWidth + 'px', padding: '0 12px 0 0', verticalAlign: 'top' }}>
                      <img
                        src={attributes.profile_pic}
                        width={avatarWidth}
                        height={avatarWidth}
                        className="ng-image"
                        style={{ width: avatarWidth + 'px', height: avatarWidth + 'px', display: 'inline-block', margin: 0, verticalAlign: 'top', borderRadius: avatarWidth + 'px' }}
                      />
                    </td>
                    <td style={{ verticalAlign: 'top' }}>

                      <RichText
                        tagName="div"
                        className="ng-div"
                        placeholder={newsletterglue_meta.author_name}
                        value={attributes.author_name}
                        onChange={(author_name) => setAttributes({ author_name: author_name })}
                        withoutInteractiveFormatting
                        multiline={false}
                        style={{ fontWeight: 'bold', marginBottom: '2px' }}
                      />

                      <RichText
                        tagName="div"
                        className="ng-div"
                        placeholder={newsletterglue_meta.author_bio ? newsletterglue_meta.author_bio : __('Enter biography or something else...', 'newsletter-glue')}
                        value={attributes.author_bio}
                        onChange={(author_bio) => setAttributes({ author_bio: author_bio })}
                        allowedFormats={['core/bold', 'core/link', 'core/italic', 'core/strikethrough', 'core/text-color']}
                        multiline={false}
                        style={{ marginBottom: '8px' }}
                      />

                      {attributes.show_button && (
                        <div className="ng-div ng-block-button">
                          <a
                            href={attributes.social_user}
                            rel="nofollow"
                            onClick={(event) => event.preventDefault()}
                            style={{ padding: '2px 12px', display: 'inline-block', borderRadius: attributes.border_radius, color: linkColor, border: linkBorder, backgroundColor: linkBackground }}
                          >
                            {iconURL && (
                              <img
                                src={iconURL}
                                width={iconWidth}
                                height={iconWidth}
                                className="ng-image"
                                style={{ width: iconWidth + 'px', height: iconWidth + 'px', display: 'inline-block', margin: '0 2px 0 0', borderRadius: iconWidth + 'px', verticalAlign: 'sub' }}
                              />
                            )}
                            <RichText
                              tagName="span"
                              placeholder={__('Enter button text...')}
                              value={attributes.button_text}
                              onChange={(button_text) => setAttributes({ button_text: button_text })}
                              withoutInteractiveFormatting
                              multiline={false}
                            />
                          </a>
                        </div>
                      )}

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