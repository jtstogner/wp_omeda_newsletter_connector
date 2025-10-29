import { Button, Popover, TextControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import apiFetch from '@wordpress/api-fetch';

import { Icon, dragHandle, trash } from '@wordpress/icons';

export function SortablePost(props) {
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
  } = useSortable({ id: props.id });

  const { id, attrs, setAttributes } = props;

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
  };

  const [control, setControl] = useState(false);

  const handle = <div {...listeners} {...attributes} className="ng-sortable-handle"><Icon icon={dragHandle} /></div>;

  const embeds = attrs.embeds;

  var item = embeds[id] ? embeds[id] : 0;

  function getItemTitle(item) {
    var entries = attrs.embeds;
    var key = item.key;
    var title = item.title;

    if (entries[key]['custom']) {
      if (entries[key]['custom']['title']) {
        return entries[key]['custom']['title'];
      }
    }

    return title;
  }

  const [fetching, setFetching] = useState(false);
  const [entry, setEntry] = useState(item.id);
  const [error, setError] = useState('');

  const [popoverAnchor, setPopoverAnchor] = useState();
  const [isVisible, setIsVisible] = useState(false);
  const toggleVisible = () => {
    setControl(false);
    setIsVisible((state) => !state);
  };

  function fetchItem(id = null) {

    if (!entry || !id) {
      return false;
    }

    setError('');
    setFetching(true);

    const data = {
      url: entry
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/fetch_url',
      method: 'post',
      data: data,
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      }
    }).then(response => {

      setFetching(false);

      if (response.error) {
        setError(response.error);
      } else {

        const key = id;

        var itemArray = {
          key: key,
          id: response.item.ID,
          post_id: response.item.post_id ? response.item.post_id : 0,
          title: response.item.title,
          content: response.item.post_content,
          image: response.item.image_url,
          author: response.item.author ? response.item.author : '',
          categories: response.item.categories ? response.item.categories : '',
          tags: response.item.tags ? response.item.tags : '',
          favicon: response.item.favicon ? response.item.favicon : '',
          remote: response.item.is_remote ? 'yes' : 'no',
          domain: response.item.domain ? response.item.domain : '',
          enabled: 1,
          hidden: 0,
        };

        setAttributes({
          embeds: {
            ...attrs.embeds,
            [key]: itemArray
          },
        });

      }

    });

  }

  function deleteItem(key) {
    setAttributes({
      embeds: {
        ...attrs.embeds,
        [key]: {
          ...attrs.embeds[key],
          enabled: 0,
        }
      },
    });
  }

  if (!item.enabled) {
    return;
  }

  return (
    <div ref={setNodeRef} style={style} className="ng-sortable-area">

      {id && item.enabled && (
        <>
          <div className={`ng-sortable-item${control ? ' opened' : ''}`} ref={setPopoverAnchor}>
            {handle}
            <div className="ng-sortable-label ng-sortable-label-limit is-underlined" onClick={() => setControl(!control)}>{getItemTitle(item)}</div>
            <Button
              className="ng-sortable-button"
              icon={trash}
              iconSize={20}
              label={__('Delete post')}
              showTooltip
              onClick={toggleVisible}
            />
            {isVisible && (
              <Popover
                anchor={popoverAnchor}
                placement="overlay"
              >
                <div style={{ display: 'flex', justifyContent: 'space-between', height: '100%', alignItems: 'center', padding: '0 10px' }}>
                  <span style={{ color: '#999' }}>Confirm delete?</span>
                  <div style={{ display: 'flex', columnGap: '6px' }}>
                    <Button
                      className="ng-sortable-button"
                      label={__('Yes')}
                      variant="secondary"
                      isSmall
                      isDestructive
                      onClick={() => deleteItem(id)}
                    >Yes</Button>
                    <Button
                      className="ng-sortable-button"
                      label={__('No')}
                      variant="tertiary"
                      isSmall
                      onClick={toggleVisible}
                    >No</Button>
                  </div>
                </div>
              </Popover>
            )}
          </div>
          <div className={`ng-sortable-control${control ? ' opened' : ''}`}>

            <TextControl
              value={entry}
              onChange={(val) => setEntry(val)}
              placeholder={__('Search or type URL')}
              disabled={fetching}
            />

            <Button
              variant={'secondary'}
              disabled={!entry || fetching}
              isBusy={fetching}
              isSmall
              onClick={() => { fetchItem(id) }}
            >Update</Button>

            {error && <div className="ngl-error" style={{ paddingBottom: '10px' }}>{error}</div>}

          </div>
        </>
      )}

    </div>
  );
}