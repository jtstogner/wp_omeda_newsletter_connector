import { Button, RadioControl, TextControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

import { chevronDown, chevronUp, dragHandle, Icon, seen, unseen } from '@wordpress/icons';

export function SortableItem(props) {
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
  } = useSortable({ id: props.id });

  const { id, attrs, setAttributes } = props;

  const { show_label, show_author, label_type, show_heading, show_excerpt, show_cta, cta_type, cta_link } = attrs;

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
  };

  const [LabelOpen, setLabelOpen] = useState(false);
  const [CTAOpen, setCTAOpen] = useState(false);

  const handle = <div {...listeners} {...attributes} className="ng-sortable-handle"><Icon icon={dragHandle} /></div>;

  return (
    <div ref={setNodeRef} style={style} className="ng-sortable-area">

      {id == 1 && (
        <>
          <div className={`ng-sortable-item${LabelOpen ? ' opened' : ''}${show_label ? '' : ' hidden'}`}>
            {handle}
            <div className="ng-sortable-label is-underlined" onClick={() => setLabelOpen(!LabelOpen)}>Label</div>
            {show_label && (
              <Button
                className="ng-sortable-button"
                icon={LabelOpen && show_label ? chevronUp : chevronDown}
                iconSize={22}
                label={LabelOpen && show_label ? 'Close settings' : 'Open settings'}
                showTooltip
                onClick={() => {
                  setLabelOpen(!LabelOpen);
                }}
              />
            )}
            <Button
              className="ng-sortable-button"
              icon={show_label ? seen : unseen}
              iconSize={20}
              label={show_label ? 'Hide label' : 'Show label'}
              showTooltip
              onClick={() => {
                setAttributes({ show_label: !show_label });
              }}
            />
          </div>
          {show_label && (
            <div className={`ng-sortable-control${LabelOpen && show_label ? ' opened' : ''}`}>
              <RadioControl
                className="ngl-radio-as-child"
                selected={label_type}
                options={[
                  { label: 'Category', value: 'category' },
                  { label: 'Tag', value: 'tag' },
                  { label: 'Domain name', value: 'domain' },
                ]}
                onChange={(value) => setAttributes({ label_type: value })}
              />
            </div>
          )}
        </>
      )}

      {id == 2 && (
        <div className={`ng-sortable-item${show_author ? '' : ' hidden'}`}>
          {handle}
          <div className="ng-sortable-label">Author</div>
          <Button
            className="ng-sortable-button"
            icon={show_author ? seen : unseen}
            iconSize={20}
            label={show_author ? 'Hide excerpt' : 'Show excerpt'}
            showTooltip
            onClick={() => {
              setAttributes({ show_author: !show_author });
            }}
          />
        </div>
      )}

      {id == 3 && (
        <div className={`ng-sortable-item${show_heading ? '' : ' hidden'}`}>
          {handle}
          <div className="ng-sortable-label">Heading</div>
          <Button
            className="ng-sortable-button"
            icon={show_heading ? seen : unseen}
            iconSize={20}
            label={show_heading ? 'Hide heading' : 'Show heading'}
            showTooltip
            onClick={() => {
              setAttributes({ show_heading: !show_heading });
            }}
          />
        </div>
      )}

      {id == 4 && (
        <div className={`ng-sortable-item${show_excerpt ? '' : ' hidden'}`}>
          {handle}
          <div className="ng-sortable-label">Excerpt</div>
          <Button
            className="ng-sortable-button"
            icon={show_excerpt ? seen : unseen}
            iconSize={20}
            label={show_excerpt ? 'Hide excerpt' : 'Show excerpt'}
            showTooltip
            onClick={() => {
              setAttributes({ show_excerpt: !show_excerpt });
            }}
          />
        </div>
      )}

      {id == 5 && (
        <>
          <div className={`ng-sortable-item${CTAOpen ? ' opened' : ''}${show_cta ? '' : ' hidden'}`}>
            {handle}
            <div className="ng-sortable-label is-underlined" onClick={() => setCTAOpen(!CTAOpen)}>Call to action</div>
            {show_cta && (
              <Button
                className="ng-sortable-button"
                icon={CTAOpen && show_cta ? chevronUp : chevronDown}
                iconSize={22}
                label={CTAOpen && show_cta ? 'Close settings' : 'Open settings'}
                showTooltip
                onClick={() => {
                  setCTAOpen(!CTAOpen);
                }}
              />
            )}
            <Button
              className="ng-sortable-button"
              icon={show_cta ? seen : unseen}
              iconSize={20}
              label={show_cta ? 'Hide CTA' : 'Show CTA'}
              showTooltip
              onClick={() => {
                setAttributes({ show_cta: !show_cta });
              }}
            />
          </div>
          {show_cta && (
            <div className={`ng-sortable-control${CTAOpen && show_cta ? ' opened' : ''}`}>

              <RadioControl
                className="ngl-radio-as-child"
                selected={cta_type}
                options={[
                  { label: 'Link', value: 'link' },
                  { label: 'Button', value: 'button' },
                ]}
                onChange={(value) => setAttributes({ cta_type: value })}
              />

              <TextControl
                label={__('Default text', 'newsletter-glue')}
                value={cta_link}
                className="ngl-input-as-child"
                onChange={(value) => setAttributes({ cta_link: value })}
              />
            </div>
          )}
        </>
      )}

    </div>
  );
}