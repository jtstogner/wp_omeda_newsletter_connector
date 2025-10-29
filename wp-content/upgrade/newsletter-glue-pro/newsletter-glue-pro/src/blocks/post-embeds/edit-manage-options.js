import { BaseControl, Button, __experimentalDivider as Divider, PanelBody, TextControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import React from 'react';

import apiFetch from '@wordpress/api-fetch';

import { Icon, plus } from '@wordpress/icons';

import {
  closestCenter,
  DndContext,
  KeyboardSensor,
  PointerSensor,
  useSensor,
  useSensors,
} from '@dnd-kit/core';
import {
  arrayMove,
  SortableContext,
  sortableKeyboardCoordinates,
  verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { SortablePost } from './SortablePost';

export const LpManageOptions = props => {

  const { attributes, setAttributes } = props;

  const [items, setItems] = useState(attributes.embeds_order);
  const sensors = useSensors(
    useSensor(PointerSensor),
    useSensor(KeyboardSensor, {
      coordinateGetter: sortableKeyboardCoordinates,
    })
  );

  function handleDragEnd(event) {
    const { active, over } = event;

    if (active.id !== over.id) {
      setItems((items) => {
        const oldIndex = items.indexOf(active.id);
        const newIndex = items.indexOf(over.id);

        var arr = arrayMove(items, oldIndex, newIndex);
        setAttributes({ embeds_order: arr });

        return arr;
      });
    }
  }

  function searchPost(query) {
    if (!query) {
      return false;
    }

    setError('');

    const data = {
      query: query
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/search_post',
      method: 'post',
      data: data,
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      }
    }).then(response => {

      setSearching(false);
      if (response.items) {
        setResults(response.items);
      }

    });

  }

  function fetchItem(customURL = null) {

    if (!entry && !customURL) {
      return false;
    }

    let final_uri = entry;
    if (customURL) {
      final_uri = customURL;
    }

    setError('');
    setFetching(true);
    setSearching(false);

    const data = {
      url: final_uri
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

        setEntry('');
        setResults([]);

        const key = Object.keys(attributes.embeds).length > 0 ? (Math.max.apply(null, Object.keys(attributes.embeds)) + 1) : 1;

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
            ...attributes.embeds,
            [key]: itemArray
          },
          embeds_order: [...attributes.embeds_order, key]
        });

        setItems([...attributes.embeds_order, key]);
      }

    });

  }

  const [fetching, setFetching] = useState(false);
  const [entry, setEntry] = useState('');
  const [error, setError] = useState('');
  const [searching, setSearching] = useState(false);
  const [results, setResults] = useState([]);

  const inputRef = React.useRef(null);

  useEffect(() => {

  }, []);

  return (

    <PanelBody
      title={__('Add & manage posts', 'newsletter-glue')}
      initialOpen={true}
      className="ngl-panel-body"
    >

      <BaseControl className="ngl-combobox">
        <TextControl
          value={entry}
          onChange={(val) => {
            setEntry(val);
            if (val && val.length >= 3) {
              setSearching(true);
              searchPost(val);
            } else {
              setSearching(false);
            }
          }}
          placeholder={__('Search or type URL')}
          disabled={fetching}
        />
        <Button
          variant={'primary'}
          disabled={!entry || fetching}
          isBusy={fetching}
          onClick={() => fetchItem()}
          ref={inputRef}
        >Add</Button>
      </BaseControl>

      {error && <div className="ngl-error" style={{ paddingBottom: '10px' }}>{error}</div>}

      {results.length > 0 && (
        <>
          <div className={`ngl-search-dd${fetching ? ' is-disabled' : ''}`}>
            {results.map((item, i) => {
              return <Button key={i} disabled={fetching || searching} onClick={() => {
                setEntry(item.url);
                fetchItem(item.url);
              }}><div>{item.title}</div><span><Icon icon={plus} /></span></Button>;
            })}
          </div>
          <Divider />
        </>
      )}

      <DndContext
        sensors={sensors}
        collisionDetection={closestCenter}
        onDragEnd={handleDragEnd}
      >
        <SortableContext
          items={items}
          strategy={verticalListSortingStrategy}
        >
          {items.map(id => <SortablePost key={id} id={id} attrs={attributes} setAttributes={setAttributes} setItems={setItems} />)}
        </SortableContext>
      </DndContext>

    </PanelBody>

  );
}