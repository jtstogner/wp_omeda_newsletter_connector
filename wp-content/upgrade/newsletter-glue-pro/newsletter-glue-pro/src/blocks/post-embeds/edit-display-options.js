import { Button, __experimentalDivider as Divider, PanelBody, PanelRow, __experimentalToggleGroupControl as ToggleGroupControl, __experimentalToggleGroupControlOption as ToggleGroupControlOption, __experimentalUnitControl as UnitControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { chevronDown, chevronUp, seen, unseen } from '@wordpress/icons';

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
import { SortableItem } from './SortableItem';

import { units } from '../../defaults/units.js';

export const LpDisplayOptions = props => {

  const { attributes, setAttributes } = props;

  const { show_image, show_divider } = attributes;

  const [DividerOpen, setDividerOpen] = useState(false);

  const [items, setItems] = useState(attributes.order);
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
        setAttributes({ order: arr });

        return arr;
      });
    }
  }

  return (

    <PanelBody
      title={__('Display options', 'newsletter-glue')}
      initialOpen={true}
      className="ngl-panel-body"
    >

      <div className="ng-sortable-area">
        <div className={`ng-sortable-item disabled${attributes.show_image ? '' : ' hidden'}`}>
          <div className="ng-sortable-label">Featured image</div>
          <Button
            className="ng-sortable-button"
            icon={attributes.show_image ? seen : unseen}
            iconSize={20}
            label={show_image ? 'Hide image' : 'Show image'}
            showTooltip
            onClick={() => {
              setAttributes({ show_image: !attributes.show_image });
            }}
          />
        </div>
      </div>

      <DndContext
        sensors={sensors}
        collisionDetection={closestCenter}
        onDragEnd={handleDragEnd}
      >
        <SortableContext
          items={items}
          strategy={verticalListSortingStrategy}
        >
          {items.map(id => <SortableItem key={id} id={id} attrs={attributes} setAttributes={setAttributes} />)}
        </SortableContext>
      </DndContext>

      <div className="ng-sortable-area">
        <div className={`ng-sortable-item${DividerOpen ? ' opened' : ''} disabled${attributes.show_divider ? '' : ' hidden'}`}>
          <div className="ng-sortable-label is-underlined" onClick={() => setDividerOpen(!DividerOpen)}>Separator</div>
          {show_divider && (
            <Button
              className="ng-sortable-button"
              icon={DividerOpen && show_divider ? chevronUp : chevronDown}
              iconSize={22}
              label={DividerOpen && show_divider ? 'Close settings' : 'Open settings'}
              showTooltip
              onClick={() => {
                setDividerOpen(!DividerOpen);
              }}
            />
          )}
          <Button
            className="ng-sortable-button"
            icon={attributes.show_divider ? seen : unseen}
            iconSize={20}
            label={show_divider ? 'Hide divider' : 'Show divider'}
            showTooltip
            onClick={() => {
              setAttributes({ show_divider: !attributes.show_divider });
            }}
          />
        </div>
        {show_divider && (
          <div className={`ng-sortable-control${DividerOpen && show_divider ? ' opened' : ''}`}>
            <div style={{ height: "10px" }} />
            <UnitControl
              label={"Thickness"}
              value={attributes.divider_size}
              onChange={(newValue) => {
                setAttributes({ divider_size: newValue });
              }}
              units={units}
              style={{ maxWidth: '100px' }}
            />
          </div>
        )}
      </div>

      <Divider />

      {attributes.contentstyle == 'multi' && <>
        <PanelRow>
          <ToggleGroupControl
            label={__('Number of columns', 'newsletter-glue')}
            value={attributes.columns_num}
            onChange={(newValue) => setAttributes({ columns_num: newValue })}
            isBlock
          >
            <ToggleGroupControlOption
              value="one"
              label={__('1 column')}
            />
            <ToggleGroupControlOption
              value="two"
              label={__('2 columns')}
            />
          </ToggleGroupControl>
        </PanelRow>
      </>
      }

      {(attributes.columns_num == 'one' || attributes.contentstyle == 'single') && <>

        {attributes.show_image &&
          <PanelRow>
            <ToggleGroupControl
              label={__('Table width ratio', 'newsletter-glue')}
              value={attributes.table_ratio}
              onChange={(newValue) => setAttributes({ table_ratio: newValue })}
              isBlock
            >
              <ToggleGroupControlOption
                value="full"
                label={__('Full')}
              />
              <ToggleGroupControlOption
                value="50_50"
                label={__('50:50')}
              />
              <ToggleGroupControlOption
                value="30_70"
                label={__('30:70')}
              />
              <ToggleGroupControlOption
                value="70_30"
                label={__('70:30')}
              />
            </ToggleGroupControl>
          </PanelRow>
        }

        {attributes.show_image && attributes.table_ratio !== 'full' &&
          <PanelRow>
            <ToggleGroupControl
              label={__('Image placement', 'newsletter-glue')}
              value={attributes.image_position}
              onChange={(newValue) => setAttributes({ image_position: newValue })}
              isBlock
            >
              <ToggleGroupControlOption
                value="left"
                label={__('Left')}
              />
              <ToggleGroupControlOption
                value="right"
                label={__('Right')}
              />
            </ToggleGroupControl>
          </PanelRow>
        }

      </>
      }

    </PanelBody>

  );
}