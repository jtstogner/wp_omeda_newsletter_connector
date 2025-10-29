import { Button } from '@wordpress/components';
import { useState } from '@wordpress/element';
import React from 'react';

import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

import { chevronDown, chevronUp, seen, unseen } from '@wordpress/icons';

import { metafields } from './metafields';

export function SortableItem(props) {
  const {
    setNodeRef,
    transform,
    transition,
  } = useSortable({ id: props.id });

  const { id, attrs, setAttributes } = props;

  if (id == 5) {
    return null;
  }

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
  };

  var opened = {};
  Object.keys(metafields).forEach((id) => {
    if (metafields[id]['hasSettings']) {
      opened[metafields[id]['name']] = false;
    }
  });

  const [openedArray, setOpenedArray] = useState(opened);

  var Control = metafields[id] && metafields[id]['control'] ? metafields[id]['control'] : null;

  return (
    <div ref={setNodeRef} style={style} className="ng-sortable-area">

      <div className={`ng-sortable-item disabled${openedArray[metafields[id]['name']] ? ' opened' : ''}${attrs[`add_${metafields[id]['name']}`] ? '' : ' hidden'}`}>

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

        {metafields[id]['hasSettings'] && attrs[`add_${metafields[id]['name']}`] && (
          <Button
            className="ng-sortable-button"
            icon={openedArray[metafields[id]['name']] && attrs[`add_${metafields[id]['name']}`] ? chevronUp : chevronDown}
            iconSize={22}
            label={openedArray[metafields[id]['name']] && attrs[`add_${metafields[id]['name']}`] ? 'Close settings' : 'Open settings'}
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
          icon={attrs[`add_${metafields[id]['name']}`] ? seen : unseen}
          iconSize={20}
          label={attrs[`add_${metafields[id]['name']}`] ? `Hide ${metafields[id]['title']}` : `Show ${metafields[id]['title']}`}
          showTooltip
          onClick={() => {
            setAttributes({ [`add_${metafields[id]['name']}`]: !attrs[`add_${metafields[id]['name']}`] });
          }}
        />

      </div>

      {metafields[id]['control'] && attrs[`add_${metafields[id]['name']}`] && (
        <div className={`ng-sortable-control${openedArray[metafields[id]['name']] && attrs[`add_${metafields[id]['name']}`] ? ' opened' : ''}`}>
          <Control {...props} />
        </div>
      )}

    </div>
  );
}