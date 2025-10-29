import { Button } from '@wordpress/components';
import { useState } from '@wordpress/element';
import React from 'react';

import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

import { chevronDown, chevronUp, dragHandle, Icon, seen, unseen } from '@wordpress/icons';

import { metafields } from './metafields';

export function SortableItem(props) {
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

  const handle = <div {...listeners} {...attributes} className="ng-sortable-handle"><Icon icon={dragHandle} /></div>;

  var opened = {};
  Object.keys(metafields).forEach((id) => {
    if (metafields[id]['hasSettings']) {
      opened[metafields[id]['name']] = false;
    }
  });

  const [openedArray, setOpenedArray] = useState(opened);

  var Control = metafields[id]['control'] ? metafields[id]['control'] : null;

  return (
    <div ref={setNodeRef} style={style} className="ng-sortable-area">

      <div className={`ng-sortable-item${openedArray[metafields[id]['name']] ? ' opened' : ''}${attrs[`show_${metafields[id]['name']}`] ? '' : ' hidden'}`}>

        {handle}

        <div
          className={`ng-sortable-label${metafields[id]['hasSettings'] ? ' is-underlined' : ''}`}
          onClick={() => {
            if (metafields[id]['hasSettings']) {
              setOpenedArray({
                ...openedArray,
                [metafields[id]['name']]: !openedArray[metafields[id]['name']],
              });
            }
          }}
        >
          {metafields[id]['title']}
        </div>

        {metafields[id]['hasSettings'] && attrs[`show_${metafields[id]['name']}`] && (
          <Button
            className="ng-sortable-button"
            icon={openedArray[metafields[id]['name']] && attrs[`show_${metafields[id]['name']}`] ? chevronUp : chevronDown}
            iconSize={22}
            label={openedArray[metafields[id]['name']] && attrs[`show_${metafields[id]['name']}`] ? 'Close settings' : 'Open settings'}
            showTooltip
            onClick={() => {
              setOpenedArray({
                ...openedArray,
                [metafields[id]['name']]: !openedArray[metafields[id]['name']],
              });
            }}
          />
        )}

        <Button
          className="ng-sortable-button"
          icon={attrs[`show_${metafields[id]['name']}`] ? seen : unseen}
          iconSize={20}
          label={attrs[`show_${metafields[id]['name']}`] ? `Hide ${metafields[id]['title']}` : `Show ${metafields[id]['title']}`}
          showTooltip
          onClick={() => {
            setAttributes({ [`show_${metafields[id]['name']}`]: !attrs[`show_${metafields[id]['name']}`] });
          }}
        />

      </div>

      {metafields[id]['control'] && attrs[`show_${metafields[id]['name']}`] && (
        <div className={`ng-sortable-control${openedArray[metafields[id]['name']] && attrs[`show_${metafields[id]['name']}`] ? ' opened' : ''}`}>
          <Control {...props} />
        </div>
      )}

    </div>
  );
}