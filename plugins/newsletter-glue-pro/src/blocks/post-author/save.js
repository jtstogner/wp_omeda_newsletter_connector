import React from 'react';

import { RichText, useBlockProps } from '@wordpress/block-editor';

import { theme } from '../../defaults/theme.js';
import { getSocialColor } from './utils.js';

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

                      <RichText.Content
                        tagName="div"
                        className="ng-div"
                        value={attributes.author_name}
                        style={{ fontWeight: 'bold', marginBottom: '2px' }}
                      />

                      <RichText.Content
                        tagName="div"
                        className="ng-div"
                        value={attributes.author_bio}
                        style={{ marginBottom: '8px' }}
                      />

                      {attributes.show_button && (
                        <div className="ng-div ng-block-button">
                          <a
                            href={attributes.social_user}
                            rel="nofollow"
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
                            <RichText.Content
                              tagName="span"
                              value={attributes.button_text}
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