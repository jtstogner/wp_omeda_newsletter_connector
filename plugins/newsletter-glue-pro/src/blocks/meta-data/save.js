import React from 'react';

import { RichText, useBlockProps } from '@wordpress/block-editor';
import * as wpDate from "@wordpress/date";

import { theme } from '../../defaults/theme.js';
import { metafields } from './metafields';

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

  var order = attributes.order;

  var divider = attributes.divider_style && attributes.divider_style == 'dot' ? '•' : '|';

  var read_more_tag = attributes.read_online_link === 'blog' ? '{{ blog_post }}' : '{{ webversion }}';

  const post = wp.data.select("core/editor").getCurrentPost();
  const postDate = wpDate.format(attributes.date_format, post.date);

  var len = 0;

  return (
    <>
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
                          <RichText.Content
                            tagName="span"
                            value={attributes.author_name}
                          />
                        </>
                      )}

                      {item.name === 'title' && (
                        <RichText.Content
                          tagName="span"
                          value={attributes.title}
                        />
                      )}

                      {item.name === 'issue' && (
                        <RichText.Content
                          tagName="span"
                          value={attributes.issue}
                        />
                      )}

                      {item.name === 'date' && <span className="ngl-metadata-date-ajax">{postDate}</span>}

                      {item.name === 'location' && (
                        <>
                          <RichText.Content
                            tagName="span"
                            value={attributes.location}
                          />
                        </>
                      )}

                      {item.name === 'url' && (
                        <>
                          <RichText.Content
                            tagName="a"
                            href={read_more_tag}
                            value={attributes.url}
                          />
                        </>
                      )}

                      {item.name === 'readtime' && (
                        <>
                          <RichText.Content
                            tagName="span"
                            value={attributes.readtime}
                          />{' '}
                          <span className="ngl-metadata-readtime-ajax">{attributes.readingtime}</span>
                        </>
                      )}

                      {item.name === 'meta' && (
                        <RichText.Content
                          tagName="span"
                          value={attributes.meta}
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