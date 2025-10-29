import React from 'react';

import { AlignmentControl, BlockControls, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import * as wpDate from "@wordpress/date";
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { theme } from '../../defaults/theme.js';
import { Controls } from './controls.js';
import { metafields } from './metafields';
import { MobileControls } from './mobile-controls.js';

export default function Edit({ attributes, setAttributes, className, isSelected, clientId }) {

  const post = wp.data.select("core/editor").getCurrentPost();
  const postDate = wpDate.format(attributes.date_format, post.date);

  useEffect(() => {
    if (!attributes.author_name) {
      setAttributes({ author_name: newsletterglue_meta.author_name })
    }

    if (!attributes.profile_pic) {
      setAttributes({ profile_pic: newsletterglue_meta.profile_pic })
    }

    if (!attributes.title && post) {
      setAttributes({ title: post.title })
    }

    if (!attributes.url) {
      setAttributes({ url: __('Read online') })
    }

    if (!attributes.readtime) {
      setAttributes({ readtime: __('Reading time:') })
    }

    setAttributes({ readingtime: newsletterglue_meta.readtime });
    setAttributes({ post_id: post.id })
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
    textAlign: attributes.align,
    color: color,
  }

  var order = attributes.order;

  var divider = attributes.divider_style && attributes.divider_style == 'dot' ? '•' : '|';

  var read_more_tag = attributes.read_online_link === 'blog' ? '{{ blog_post }}' : '{{ webversion }}';

  var len = 0;

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

      <table {...blockProps} data-date-format={attributes.date_format}>
        <tbody>
          <tr>
            <td className="ng-block-td" align={attributes.align} style={tdStyle}>
              {order.map(function (index, i) {
                var item = metafields[index];
                if (attributes['show_' + item.name]) {
                  len++;
                  return (
                    <span key={i} className={`ng-block-${item.name}`} style={{ display: 'inline-block' }}>

                      {len > 1 && <span className="ng-sep">&nbsp;&nbsp;&nbsp;{divider}&nbsp;&nbsp;&nbsp;</span>}

                      {item.name === 'author' && (
                        <>
                          <img src={attributes.profile_pic} width="32" height="32" className="ng-image" style={{ width: '32px', height: '32px', display: 'inline-block', margin: '0 6px 0 0', verticalAlign: 'middle', borderRadius: '32px' }} />
                          <RichText
                            tagName="span"
                            placeholder={newsletterglue_meta.author_name}
                            value={attributes.author_name}
                            onChange={(author_name) => setAttributes({ author_name: author_name })}
                            withoutInteractiveFormatting
                            multiline={false}
                          />
                        </>
                      )}

                      {item.name === 'title' && (
                        <RichText
                          tagName="span"
                          aria-label={__('Enter title...')}
                          placeholder={__('Enter title...')}
                          value={attributes.title}
                          onChange={(title) => setAttributes({ title: title })}
                          withoutInteractiveFormatting
                          multiline={false}
                        />
                      )}

                      {item.name === 'issue' && (
                        <RichText
                          tagName="span"
                          aria-label={__('Issue #')}
                          placeholder={__('Issue #')}
                          value={attributes.issue}
                          onChange={(issue) => setAttributes({ issue: issue })}
                          withoutInteractiveFormatting
                          multiline={false}
                        />
                      )}

                      {item.name === 'date' && <span className="ngl-metadata-date-ajax">{postDate}</span>}

                      {item.name === 'location' && (
                        <>
                          <RichText
                            tagName="span"
                            aria-label={__('Location')}
                            placeholder={__('Location')}
                            value={attributes.location}
                            onChange={(location) => setAttributes({ location: location })}
                            withoutInteractiveFormatting
                            multiline={false}
                          />
                        </>
                      )}

                      {item.name === 'url' && (
                        <>
                          <RichText
                            tagName="a"
                            href={read_more_tag}
                            placeholder={__('Read online')}
                            value={attributes.url}
                            onChange={(url) => setAttributes({ url: url })}
                            withoutInteractiveFormatting
                            multiline={false}
                          />
                        </>
                      )}

                      {item.name === 'readtime' && (
                        <>
                          <RichText
                            tagName="span"
                            placeholder={__('Reading time:')}
                            value={attributes.readtime}
                            onChange={(readtime) => setAttributes({ readtime: readtime })}
                            withoutInteractiveFormatting
                            multiline={false}
                          />{' '}
                          <span className="ngl-metadata-readtime-ajax">{attributes.readingtime}</span>
                        </>
                      )}

                      {item.name === 'meta' && (
                        <RichText
                          tagName="span"
                          aria-label={__('Enter data')}
                          placeholder={__('Enter data...')}
                          value={attributes.meta}
                          onChange={(meta) => setAttributes({ meta: meta })}
                          withoutInteractiveFormatting
                          multiline={false}
                        />
                      )}

                    </span>
                  );
                }
              })}
            </td>
          </tr>
        </tbody>
      </table>
    </>
  );

}