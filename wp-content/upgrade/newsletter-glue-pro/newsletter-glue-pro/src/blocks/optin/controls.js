import {
  BaseControl,
  Button,
  PanelBody,
  PanelRow,
  RangeControl,
  SelectControl,
  TextControl,
  ToggleControl,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
  __experimentalToolsPanel as ToolsPanel
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState } from 'react';

import { seen, unseen } from '@wordpress/icons';

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

import { ColorSettingsPane } from '../../components/colors.js';
import { SettingsPane } from '../../components/settings.js';

import { fonts } from '../../defaults/fonts.js';

import { theme } from '../../defaults/theme.js';

export const Controls = props => {

  const { attributes, setAttributes, showMsg, setShowMsg } = props;

  const resetColors = () => {
    setAttributes({
      background: undefined,
      heading_color: undefined,
      description_color: undefined,
      button_text_color: undefined,
      button_fill: undefined,
      button_outline: undefined,
      label_color: undefined,
      input_color: undefined,
      text_color: undefined,
      checkbox_color: undefined,
      success_color: undefined,
    });
  };

  const resetTypography = () => {
    setAttributes({
      font_heading: theme.font,
      font_desc: theme.font,
      font_input: theme.font,
      font_label: theme.font,
      font_text: theme.font,
      font_checkbox: theme.font,
      font_button: theme.font,
      font_success: theme.font,
      fontsize_heading: theme.optin.fontsize_heading,
      fontsize_desc: theme.optin.fontsize_desc,
      fontsize_label: theme.optin.fontsize_label,
      fontsize_input: theme.optin.fontsize_input,
      fontsize_text: theme.optin.fontsize_text,
      fontsize_checkbox: theme.optin.fontsize_checkbox,
      fontsize_button: theme.optin.fontsize_button,
      fontsize_success: theme.optin.fontsize_success,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: theme.optin.padding,
      margin: theme.optin.margin,
    });
  };

  const colorSettings = [
    { value: 'background', label: 'Background' },
    { value: 'heading_color', label: 'Form heading' },
    { value: 'description_color', label: 'Form description' },
    { value: 'label_color', label: 'Name and email title' },
    { value: 'input_color', label: 'Name and email input fields' },
    { value: 'text_color', label: 'Text beneath button' },
    { value: 'checkbox_color', label: 'Checkbox text' },
    { value: 'button_fill', label: 'Button fill' },
    { value: 'button_outline', label: 'Button outline' },
    { value: 'button_text_color', label: 'Button text' },
    { value: 'success_color', label: 'Success message' },
  ];

  const typographySettings = [
    { type: 'section', label: 'Heading' },
    { value: 'font_heading', label: 'Font family', default: theme.font, type: 'customselect', options: fonts, is_single: true },
    { value: 'fontsize_heading', label: 'Font size', default: theme.optin.fontsize_heading, type: 'unit', is_single: true },

    { type: 'section', label: 'Description' },
    { value: 'font_desc', label: 'Font family', default: theme.font, type: 'customselect', options: fonts, is_single: true },
    { value: 'fontsize_desc', label: 'Font size', default: theme.optin.fontsize_desc, type: 'unit', is_single: true },

    { type: 'section', label: 'Name and email titles' },
    { value: 'font_label', label: 'Font family', default: theme.font, type: 'customselect', options: fonts, is_single: true },
    { value: 'fontsize_label', label: 'Font size', default: theme.optin.fontsize_label, type: 'unit', is_single: true },

    { type: 'section', label: 'Name and email input fields' },
    { value: 'font_input', label: 'Font family', default: theme.font, type: 'customselect', options: fonts, is_single: true },
    { value: 'fontsize_input', label: 'Font size', default: theme.optin.fontsize_input, type: 'unit', is_single: true },

    { type: 'section', label: 'Text beneath button' },
    { value: 'font_text', label: 'Font family', default: theme.font, type: 'customselect', options: fonts, is_single: true },
    { value: 'fontsize_text', label: 'Font size', default: theme.optin.fontsize_text, type: 'unit', is_single: true },

    { type: 'section', label: 'Checkbox text' },
    { value: 'font_checkbox', label: 'Font family', default: theme.font, type: 'customselect', options: fonts, is_single: true },
    { value: 'fontsize_checkbox', label: 'Font size', default: theme.optin.fontsize_checkbox, type: 'unit', is_single: true },

    { type: 'section', label: 'Button text' },
    { value: 'font_button', label: 'Font family', default: theme.font, type: 'customselect', options: fonts, is_single: true },
    { value: 'fontsize_button', label: 'Font size', default: theme.optin.fontsize_button, type: 'unit', is_single: true },

    { type: 'section', label: 'Success message' },
    { value: 'font_success', label: 'Font family', default: theme.font, type: 'customselect', options: fonts, is_single: true },
    { value: 'fontsize_success', label: 'Font size', default: theme.optin.fontsize_success, type: 'unit', is_single: true },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: theme.optin.padding, type: 'boxcontrol' },
    { value: 'margin', label: 'Margin', default: theme.optin.margin, type: 'boxcontrol' },
  ];

  var app = newsletterglue_meta.app;
  var SelectList = '';
  var ExtraList = '';
  var DoubleOptin = '';

  const arrayOfApps = [
    'campaignmonitor',
    'mailerlite',
    'mailerlite_v2',
    'activecampaign',
    'getresponse',
    'convertkit',
    'sailthru',
    'sendgrid',
    'mailjet',
    'moosend',
    'klaviyo',
    'brevo',
    'aweber',
    'mailchimp',
    'constantcontact',
  ];

  if (arrayOfApps.includes(app)) {
    SelectList = <SelectControl
      key={`${app}_select`}
      label={__('Select a list/group')}
      value={attributes.list_id}
      onChange={(val) => {
        setAttributes({ list_id: val });
      }}
      options={newsletterglue_meta.the_lists}
    />;
  }

  if (app == 'sendy') {
    SelectList = <TextControl
      key={`${app}_textinput`}
      label={__('Enter list ID')}
      value={attributes.list_id}
      onChange={(val) => {
        setAttributes({ list_id: val });
      }}
      help={'The list id you want to subscribe a user to. This encrypted & hashed id can be found under View all lists section named ID.'}
    />;
  }

  if (app == 'salesforce') {
    SelectList = <SelectControl
      key={`${app}_select`}
      label={__('Select a list/group')}
      value={attributes.list_id}
      onChange={(val) => {
        setAttributes({ list_id: val });
      }}
      options={newsletterglue_meta.the_lists.filter(item => item.label !== 'All Subscribers' && !item.value.toString().includes('_dex'))}
    />;
  }

  if (app == 'mailchimp') {
    DoubleOptin = <BaseControl className="ngl-gutenberg-help" key='double-optin-key'>
      <ToggleControl
        label={__('Double opt-in')}
        onChange={(val) => {
          setAttributes({ double_optin: val });
        }}
        checked={attributes.double_optin}
        help={__('Automatically email new subscribers to confirm they want to receive emails from you. This creates less spam addresses and higher quality subscribers.')}
      />
    </BaseControl>;
  }

  if (app && newsletterglue_meta.the_lists && !attributes.list_id && newsletterglue_meta.the_lists[0]['value']) {
    setAttributes({ list_id: newsletterglue_meta.the_lists[0]['value'] });
  }

  if (app && newsletterglue_meta.extra_lists && attributes.add_checkbox) {
    ExtraList = <BaseControl key='extra-list-key'>
      <SelectControl
        label={__('Select list for checkbox (optional)')}
        value={attributes.extra_list_id}
        onChange={(val) => {
          setAttributes({ extra_list_id: val });
        }}
        options={newsletterglue_meta.extra_lists}
      />
    </BaseControl>
  } else {
    if (app && app === 'sendy' && attributes.add_checkbox) {
      ExtraList = <BaseControl key='extra-list-key'>
        <TextControl
          label={__('Enter extra list ID for checkbox (optional)')}
          value={attributes.extra_list_id}
          onChange={(val) => {
            setAttributes({ extra_list_id: val });
          }}
        />
      </BaseControl>;
    }
  }

  var showESP = '';
  if (app) {
    showESP = [
      <BaseControl key='select-esp-list'>
        {SelectList}
      </BaseControl>,
      ExtraList,
      DoubleOptin
    ];
  } else {
    showESP = <BaseControl>
      <p>{__('You do not have an active email connection yet.')}</p>
      <a href={newsletterglue_params.connect_url}>{newsletterglue_params.connect_esp}</a>
    </BaseControl>;
  }

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
      <PanelBody title={newsletterglue_meta.app_name} initialOpen={true}>
        {showESP}
      </PanelBody>
      <PanelBody title={__('Form options')} className="ngl-panel-body">
        <PanelRow>
          <ToggleGroupControl
            label={__('Form style', 'newsletter-glue')}
            value={attributes.form_style}
            onChange={(newValue) => setAttributes({ form_style: newValue })}
            isBlock
          >
            <ToggleGroupControlOption
              value="portrait"
              label={__('Portrait')}
            />
            <ToggleGroupControlOption
              value="landscape"
              label={__('Landscape')}
            />
          </ToggleGroupControl>
        </PanelRow>
        <PanelRow>
          <RangeControl
            label={__('Border radius (pixels)', 'newsletter-glue')}
            value={attributes.form_radius}
            onChange={(value) => setAttributes({ form_radius: value })}
            min={0}
            max={100}
            allowReset={true}
            resetFallbackValue={12}
          />
        </PanelRow>
        <PanelRow>
          <RangeControl
            label={__('Spacing (pixels)', 'newsletter-glue')}
            value={attributes.spacing_size}
            onChange={(value) => setAttributes({ spacing_size: value })}
            min={0}
            max={100}
            allowReset={true}
            resetFallbackValue={20}
          />
        </PanelRow>
      </PanelBody>

      <PanelBody title={__('Display options')} className="ngl-panel-body">
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
          <div className={`ng-sortable-item disabled${attributes.add_text ? '' : ' hidden'}`}>
            <div className="ng-sortable-label">Text beneath button</div>
            <Button
              className="ng-sortable-button"
              icon={attributes.add_text ? seen : unseen}
              iconSize={20}
              label={attributes.add_text ? 'Hide text' : 'Show text'}
              showTooltip
              onClick={() => {
                setAttributes({ add_text: !attributes.add_text });
              }}
            />
          </div>
        </div>

      </PanelBody>

      <PanelBody title={__('Success message')} className="ngl-panel-body">
        <PanelRow>
          <ToggleControl
            label={__('Toggle success message', 'newsletter-glue')}
            checked={showMsg}
            onChange={(value) => {
              setShowMsg(value);
            }}
          />
        </PanelRow>
        <PanelRow>
          <TextControl
            label={__('Write success message', 'newsletter-glue')}
            value={attributes.message_text}
            onChange={(value) => {
              setAttributes({ message_text: value });
            }}
          />
        </PanelRow>
      </PanelBody>

      <ToolsPanel label={__('Colors')} resetAll={resetColors} hasInnerWrapper={true} className="color-block-support-panel">
        <ColorSettingsPane attributes={attributes} setAttributes={setAttributes} colors={colorSettings} />
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
