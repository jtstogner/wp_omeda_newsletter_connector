import React from 'react';

import { __ } from '@wordpress/i18n';

import {
  PanelBody,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
  __experimentalToolsPanel as ToolsPanel,
} from '@wordpress/components';

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

import { useState } from '@wordpress/element';

import { ColorSettingsPane } from '../../components/colors.js';
import { SettingsPane } from '../../components/settings.js';

import { fonts } from '../../defaults/fonts.js';
import { theme } from '../../defaults/theme.js';
import { fontweights } from '../../defaults/weights.js';

export const Controls = props => {

  const { attributes, setAttributes } = props;

  const resetColors = () => {
    setAttributes({
      background: undefined,
      color: undefined,
      link: theme.colors.primary,
    });
  };

  const resetTypography = () => {
    setAttributes({
      fontweight: theme.fontweight,
      lineheight: theme.lineheight,
      font: theme.font,
      fontsize: theme.meta.fontsize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: theme.meta.padding,
    });
  };

  const colors = [
    { value: 'color', label: 'Text' },
    { value: 'background', label: 'Background' },
    { value: 'link', label: 'Link', default: theme.colors.primary, required: true }
  ];

  const typographySettings = [
    { value: 'font', label: 'Font family', default: theme.font, type: 'customselect', options: fonts },
    { value: 'fontsize', label: 'Font size', default: theme.meta.fontsize, type: 'unit' },
    { value: 'fontweight', label: 'Font weight', default: theme.fontweight, type: 'customselect', options: fontweights, is_single: true },
    { value: 'lineheight', label: 'Line height', default: theme.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: theme.meta.padding, type: 'boxcontrol' },
  ];

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
    <>
      <PanelBody title={__('Manage metadata')}>

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

      </PanelBody>

      <PanelBody title={__('Settings')}>

        <ToggleGroupControl
          label={__('Divider style', 'newsletter-glue')}
          value={attributes.divider_style}
          onChange={(newStyle) => setAttributes({ divider_style: newStyle })}
          isBlock
        >
          <ToggleGroupControlOption
            value="line"
            label={__('Line')}
          />
          <ToggleGroupControlOption
            value="dot"
            label={__('Dot')}
          />
        </ToggleGroupControl>

      </PanelBody>

      <ToolsPanel label={__('Colors')} resetAll={resetColors} hasInnerWrapper={true} className="color-block-support-panel">
        <ColorSettingsPane attributes={attributes} setAttributes={setAttributes} colors={colors} />
      </ToolsPanel>

      <ToolsPanel label={__('Typography')} resetAll={resetTypography}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={typographySettings} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}