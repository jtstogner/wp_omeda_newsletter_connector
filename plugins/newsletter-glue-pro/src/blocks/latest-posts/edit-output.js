import React from 'react';

import apiFetch from '@wordpress/api-fetch';
import { MediaUpload, RichText } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from 'react';

import { Icon, rotateLeft, seen, unseen } from '@wordpress/icons';

import { theme } from '../../defaults/theme.js';
import { calculateColumnWidth } from './calculateColumnWidth';

export const LpEdit = props => {

  const { attributes, setAttributes, blockProps } = props;

  const [loading, setLoading] = useState(true);

  const changeIcon = <svg stroke="currentColor" fill="none" strokeWidth="2" viewBox="0 0 24 24" strokeLinecap="round" strokeLinejoin="round" height="20" width="20" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>;

  const [focused, setFocused] = useState(0);
  const [activeId, setActiveId] = useState(0);

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  var color = attributes.text_color ? attributes.text_color : theme.color;

  useEffect(() => {

    const data = attributes;

    if (attributes.posts) {
      setLoading(false);
    } else {

      apiFetch({
        path: 'newsletterglue/' + nglue_backend.api_version + '/get_posts',
        method: 'post',
        data: data,
        headers: {
          'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
        }
      }).then(response => {

        setLoading(false);

        if (response.posts.length) {
          if (!attributes.posts) {
            setAttributes({ posts: response.posts, hash: response.hash });
          }
        } else {
          setAttributes({ posts: null, hash: null });
        }

      });

    }

  }, []);

  useEffect(() => {
    if (!attributes.div1 || !attributes.div2 || !attributes.containerWidth || !attributes.itemBase) {
      calculateColumnWidth(attributes, setAttributes);
    }
  }, []);

  useEffect(() => {
    calculateColumnWidth(attributes, setAttributes);
  }, [attributes.image_position, attributes.table_ratio, attributes.columns_num, attributes.padding, attributes.margin]);

  useEffect(() => {

    let data;

    if (loading || !attributes.update_posts) {

      data = "";

    } else {

      data = attributes;
      
      // Debug log for data being sent to API
      console.log('Latest Posts - Sending data to API:', data);
      console.log('Latest Posts - Offset value:', data.offset);

      // Create a modified copy of the data to send to the API
      const apiData = {...data};
      
      // If we have an offset, adjust posts_num to fetch enough posts
      if (apiData.offset && apiData.offset > 0) {
        // Store original posts_num for later use
        apiData.original_posts_num = apiData.posts_num;
        
        // If posts_num is 0 (fetch all), we need a different approach
        if (apiData.posts_num === 0) {
          console.log('Latest Posts - posts_num is 0 (fetch all posts) with offset:', apiData.offset);
          // We'll fetch all posts and then apply the offset in the response handler
        } else {
          // Increase posts_num by the offset to fetch enough posts
          apiData.posts_num = apiData.posts_num + parseInt(apiData.offset);
          console.log('Latest Posts - Adjusted posts_num from', data.posts_num, 'to', apiData.posts_num);
        }
      }

      apiFetch({
        path: 'newsletterglue/' + nglue_backend.api_version + '/get_posts',
        method: 'post',
        data: apiData,
        headers: {
          'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
        }
      }).then(response => {
        
        // Debug log for API response
        console.log('Latest Posts - API response:', response);

        if (response.posts.length) {
          let postsToStore = response.posts;
          
          // If we have an offset, remove the first 'offset' number of posts
          if (data.offset && data.offset > 0) {
            const offset = parseInt(data.offset);
            
            if (postsToStore.length > offset) {
              console.log('Latest Posts - Removing first', offset, 'posts from', postsToStore.length, 'posts');
              
              // Skip the first 'offset' posts
              if (data.posts_num === 0) {
                // For 'fetch all' case, just skip the offset posts
                postsToStore = postsToStore.slice(offset);
                console.log('Latest Posts - After offset with posts_num=0, keeping', postsToStore.length, 'posts');
              } else {
                // For normal case, skip offset posts and take only the number of posts requested
                postsToStore = postsToStore.slice(offset, offset + data.posts_num);
                console.log('Latest Posts - After offset, keeping', postsToStore.length, 'posts');
              }
            } else {
              console.log('Latest Posts - Not enough posts to apply offset');
            }
          }
          
          // Store the posts (already with offset applied)
          setAttributes({ posts: postsToStore, hash: response.hash, update_posts: null });
        } else {
          setAttributes({ posts: null });
        }

      }).catch(error => {
        // Debug log for API errors
        console.error('Latest Posts - API error:', error);
      });

    }

  }, [attributes.update_posts]);

  function checkForData(id, type) {
    let data = attributes.custom_data;
    for (var i = 0; i < data.length; i++) {
      if (data[i][type + '_' + id] && data[i][type + '_' + id] != null) {
        return data[i][type + '_' + id];
        break; // eslint-disable-line
      }
    }
    return false;
  }

  function getExcerpt(item, excerpt) {
    var result = excerpt;
    if (attributes.words_num > 0 && checkForData(item.id, 'excerpt')) {
      result = result.split(" ").splice(0, attributes.words_num).join(" ");
      result = result + '...';
    }
    return result;
  }

  const styles = {};

  styles['fontFamily'] = nglue_backend.font_names[attributes.font.key];

  if (attributes.background_color) {
    styles['backgroundColor'] = attributes.background_color;
  }

  styles['color'] = color;

  if (attributes.padding) {
    styles['paddingLeft'] = isMobile ? attributes.mobile_padding.left : attributes.padding.left;
    styles['paddingRight'] = isMobile ? attributes.mobile_padding.right : attributes.padding.right;
    styles['paddingTop'] = isMobile ? attributes.mobile_padding.top : attributes.padding.top;
    styles['paddingBottom'] = isMobile ? attributes.mobile_padding.bottom : attributes.padding.bottom;
  }

  if (attributes.margin) {
    styles['marginLeft'] = isMobile ? attributes.mobile_margin.left : attributes.margin.left;
    styles['marginRight'] = isMobile ? attributes.mobile_margin.right : attributes.margin.right;
    styles['marginTop'] = isMobile ? attributes.mobile_margin.top : attributes.margin.top;
    styles['marginBottom'] = isMobile ? attributes.mobile_margin.bottom : attributes.margin.bottom;
  }

  if (attributes.border_radius) {
    styles['borderRadius'] = attributes.border_radius + 'px';
  }

  if (attributes.border_size) {
    styles['borderWidth'] = attributes.border_size + 'px';
    styles['borderStyle'] = attributes.border_style ? attributes.border_style.value : 'solid';
    styles['borderColor'] = attributes.border_color ? attributes.border_color : 'transparent';
  }

  const titleStyles = {};
  titleStyles['fontFamily'] = nglue_backend.font_names[attributes.font_title.key];
  titleStyles['fontSize'] = isMobile ? attributes.mobile_fontsize_title : attributes.fontsize_title;
  if (attributes.title_color) {
    titleStyles['color'] = attributes.title_color;
  } else {
    titleStyles['color'] = theme.colors.h3;
  }

  const labelStyles = {};
  labelStyles['fontFamily'] = nglue_backend.font_names[attributes.font_label.key];
  labelStyles['fontSize'] = isMobile ? attributes.mobile_fontsize_label : attributes.fontsize_label;
  if (attributes.label_color) {
    labelStyles['color'] = attributes.label_color;
  }

  const authorStyles = {};
  authorStyles['fontFamily'] = nglue_backend.font_names[attributes.font_author.key];
  authorStyles['fontSize'] = isMobile ? attributes.mobile_fontsize_author : attributes.fontsize_author;
  if (attributes.author_color) {
    authorStyles['color'] = attributes.author_color;
  }

  const linkStyles = {};
  linkStyles['fontFamily'] = nglue_backend.font_names[attributes.font_button.key];
  linkStyles['fontSize'] = isMobile ? attributes.mobile_fontsize_text : attributes.fontsize_text;
  if (attributes.link) {
    if (attributes.cta_type == 'link') {
      linkStyles['color'] = attributes.link;
    }
    if (attributes.cta_type == 'button') {
      linkStyles['backgroundColor'] = attributes.button;
      linkStyles['color'] = attributes.button_text;
    }
  }

  let wrapperStyle = {};
  if (attributes.margin) {
    if (attributes.margin.top || attributes.margin.bottom) {
      wrapperStyle.padding = '1px 0';
    }
  }

  const divider_bg = attributes.divider_bg ? attributes.divider_bg : '#eeeeee';

  return (

    <div {...blockProps} style={wrapperStyle}>

      <div className="ngl-lp-items ngl-lp-items-blockeditor" style={styles}>

        {loading && <div className="ngl-lp-loading">Loading posts...</div>}

        {!loading && !attributes.posts && <div className="ngl-lp-noresults">No posts were found.</div>}

        {!loading && attributes.posts &&
          attributes.posts.map(function (item, i) {

            var label_text = '';
            if (attributes.label_type == 'domain') {
              label_text = item.domain;
            } else if (attributes.label_type == 'category') {
              label_text = item.categories;
            } else if (attributes.label_type == 'tag') {
              label_text = item.tags;
            } else if (attributes.label_type == 'author') {
              label_text = item.author;
            }

            var postIsHidden = attributes.hidden_posts && attributes.hidden_posts.includes(item.id);

            var postExcerpt = checkForData(item.id, 'excerpt') ? checkForData(item.id, 'excerpt') : item.post_content;

            return (
              <div className={`ngl-lp-item-wrap ${deviceType !== 'Desktop' ? 'on-mobile' : ''}`} key={i} style={{ flexBasis: attributes.itemBase }}>

                {(checkForData(item.id, 'author') || checkForData(item.id, 'image') || checkForData(item.id, 'label') || checkForData(item.id, 'title') || checkForData(item.id, 'excerpt') || checkForData(item.id, 'ctalink')) && (
                  <div className="ng-reset-item">
                    <Button
                      variant="secondary"
                      icon={rotateLeft}
                      iconSize={20}
                      onClick={() => {
                        let data = attributes.custom_data;
                        data.forEach((entry, b) => {
                          if (entry['author_' + item.id]) {
                            data[b]['author_' + item.id] = null;
                          }
                          if (entry['image_' + item.id]) {
                            data[b]['image_' + item.id] = null;
                          }
                          if (entry['label_' + item.id]) {
                            data[b]['label_' + item.id] = null;
                          }
                          if (entry['title_' + item.id]) {
                            data[b]['title_' + item.id] = null;
                          }
                          if (entry['excerpt_' + item.id]) {
                            data[b]['excerpt_' + item.id] = null;
                          }
                          if (entry['ctalink_' + item.id]) {
                            data[b]['ctalink_' + item.id] = null;
                          }
                        });
                        setAttributes({ custom_data: data, date: new Date });
                      }}
                    >{__('Reset')}</Button>
                  </div>
                )}

                <div className={`ngl-lp-item ${deviceType != 'Desktop' && attributes.stacked_on_mobile ? 'is-stacked' : 'is-not-stacked'} ${postIsHidden ? "ngl-lp-item-hidden" : "ngl-lp-item-visible"}`}>
                  {attributes.show_image &&
                    <MediaUpload
                      multiple={false}
                      render={({ open }) => (
                        <div className={`ngl-lp-image-edit${checkForData(item.id, 'image') ? ' ngl-lp-image-edit-hasImage' : ''}`} style={{ flexBasis: attributes.div1, padding: '2px 0' }}>

                          {postIsHidden && <div className="ngl-lp-data-hidden"></div>}
                          <div className="ngl-lp-image">
                            <img src={checkForData(item.id, 'image') ? checkForData(item.id, 'image') : item.featured_image} alt={item.post_title} style={{ borderRadius: attributes.image_radius + 'px' }} onClick={open} />

                            <Button
                              variant="link"
                              className="ngl-reset-mods ngl-reset-mods-edit"
                              icon={changeIcon}
                              iconSize={20}
                              onClick={open}
                            />

                          </div>
                        </div>
                      )}
                      onSelect={(media) => {

                        let data = attributes.custom_data;
                        data.forEach((entry, b) => {
                          if (entry['image_' + item.id]) {
                            data[b]['image_' + item.id] = null;
                          }
                        });

                        setAttributes({ custom_data: [...data, { ['image_' + item.id]: media.url }], date: new Date });

                      }}
                    />

                  }

                  <div className="ngl-lp-data" style={{ flexBasis: attributes.div2 }}>

                    {postIsHidden && <div className="ngl-lp-data-hidden"></div>}

                    {attributes.order.map((index) => {
                      if (index == 1 && attributes.show_label) {
                        return <div key={index} className="ngl-lp-labels-edit" style={{ padding: '2px 0' }}>
                          <RichText
                            tagName="div"
                            className="ngl-lp-labels"
                            style={labelStyles}
                            value={checkForData(item.id, 'label') ? checkForData(item.id, 'label') : label_text}
                            placeholder={__('Add label...', 'newsletter-glue')}
                            allowedFormats={['core/bold', 'core/link', 'core/italic']}
                            onChange={(value) => {

                              let data = attributes.custom_data;
                              data.forEach((entry, b) => {
                                if (entry['label_' + item.id]) {
                                  data[b]['label_' + item.id] = null;
                                }
                              });

                              setAttributes({ custom_data: [...data, { ['label_' + item.id]: value }], date: new Date });

                            }}
                          />
                        </div>
                      }

                      if (index == 2 && attributes.show_author) {
                        return <div key={index} className="ngl-lp-labels-edit" style={{ padding: '2px 0' }}>
                          <RichText
                            tagName="div"
                            className="ngl-lp-labels"
                            style={authorStyles}
                            value={checkForData(item.id, 'author') ? checkForData(item.id, 'author') : item.author}
                            placeholder={__('Add author...', 'newsletter-glue')}
                            allowedFormats={['core/bold', 'core/link', 'core/italic']}
                            onChange={(value) => {

                              let data = attributes.custom_data;
                              data.forEach((entry, b) => {
                                if (entry['author_' + item.id]) {
                                  data[b]['author_' + item.id] = null;
                                }
                              });

                              setAttributes({ custom_data: [...data, { ['author_' + item.id]: value }], date: new Date });

                            }}
                          />
                        </div>
                      }

                      if (index == 3 && attributes.show_heading) {
                        return <div key={index} className="ngl-lp-title" style={{ padding: '2px 0' }}>
                          <RichText
                            tagName="h3"
                            value={checkForData(item.id, 'title') ? checkForData(item.id, 'title') : item.post_title}
                            placeholder={__('Add a heading', 'newsletter-glue')}
                            style={titleStyles}
                            allowedFormats={['core/bold', 'core/link', 'core/italic']}
                            onChange={(value) => {

                              let data = attributes.custom_data;
                              data.forEach((entry, b) => {
                                if (entry['title_' + item.id]) {
                                  data[b]['title_' + item.id] = null;
                                }
                              });

                              setAttributes({ custom_data: [...data, { ['title_' + item.id]: value }], date: new Date });

                            }}
                          />
                        </div>
                      }

                      if (index == 4 && attributes.show_excerpt) {
                        return <div key={index} className="ngl-lp-content-edit" style={{ padding: '2px 0' }}>
                          <RichText
                            tagName="div"
                            className="ngl-lp-content"
                            style={{
                              fontFamily: nglue_backend.font_names[attributes.font_text.key],
                              fontSize: isMobile ? attributes.mobile_fontsize_text : attributes.fontsize_text,
                              color: color,
                            }}
                            value={focused === item.id ? postExcerpt : getExcerpt(item, postExcerpt)}
                            onMouseEnter={() => {
                              setActiveId(item.id);
                            }}
                            onMouseLeave={() => {

                            }}
                            onFocus={() => {
                              if (item.id == activeId) {
                                setFocused(item.id);
                              }
                            }}
                            onBlur={() => {
                              return false;
                            }}
                            placeholder={__('Add text...', 'newsletter-glue')}
                            allowedFormats={['core/bold', 'core/link', 'core/italic', 'core/image']}
                            onChange={(value) => {

                              let data = attributes.custom_data;
                              data.forEach((entry, b) => {
                                if (entry['excerpt_' + item.id]) {
                                  data[b]['excerpt_' + item.id] = null;
                                }
                              });

                              setAttributes({ custom_data: [...data, { ['excerpt_' + item.id]: value }], date: new Date });

                            }}
                          />
                        </div>
                      }

                      if (index == 5 && attributes.show_cta) {
                        return <div key={index} className={`ngl-lp-cta ngl-lp-cta-${attributes.cta_type}`} style={{ padding: '2px 0' }}>
                          <RichText
                            className={`ngl-lp-cta-link${attributes.cta_type == 'button' ? ' wp-block-button__link' : ''}`}
                            multiline={false}
                            tagName="a"
                            style={linkStyles}
                            value={checkForData(item.id, 'ctalink') ? checkForData(item.id, 'ctalink') : attributes.cta_link}
                            placeholder={__('Add link text...', 'newsletter-glue')}
                            allowedFormats={['core/bold', 'core/italic']}
                            onChange={(value) => {

                              let data = attributes.custom_data;
                              data.forEach((entry, b) => {
                                if (entry['ctalink_' + item.id]) {
                                  data[b]['ctalink_' + item.id] = null;
                                }
                              });

                              setAttributes({ custom_data: [...data, { ['ctalink_' + item.id]: value }], date: new Date });

                            }}
                          />
                        </div>
                      }

                    })}

                  </div>

                  {postIsHidden &&
                    <span className="ngl-lp-item-hide-wrap">
                      <a
                        href="#"
                        className="ngl-lp-item-hide"
                        data-post-id={item.id}
                        onClick={() => {
                          let array = attributes.hidden_posts;
                          const index = array.indexOf(item.id);
                          if (index > -1) { // only splice array when item is found
                            array.splice(index, 1); // 2nd parameter means remove one item only
                            setAttributes({ hidden_posts: array, date: new Date });
                          }
                        }}
                      >
                        <span className="ngl-lp-hide-icon"><Icon icon={seen} /></span>
                        Show this post
                      </a>
                    </span>
                  }

                  {!postIsHidden &&
                    <span className="ngl-lp-item-hide-wrap">
                      <a
                        href="#"
                        className="ngl-lp-item-hide"
                        data-post-id={item.id}
                        onClick={() => {
                          let theHiddenPosts = attributes.hidden_posts.slice();
                          theHiddenPosts.push(item.id);
                          setAttributes({ hidden_posts: theHiddenPosts, date: new Date });
                        }}
                      >
                        <span className="ngl-lp-hide-icon"><Icon icon={unseen} /></span>
                        Hide this post
                      </a>
                    </span>
                  }

                </div>
                {attributes.show_divider && (
                  <div style={{ backgroundColor: divider_bg, height: parseInt(attributes.divider_size) + 'px', margin: "10px 0" }} />
                )}
              </div>)

          })
        }

      </div>

    </div>

  );
}