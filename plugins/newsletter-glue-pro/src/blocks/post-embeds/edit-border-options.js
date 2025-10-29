import { BaseControl, PanelBody, PanelRow, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';
import Select from 'react-select';

export const LpBorderOptions = props => {

  const { attributes, setAttributes } = props;

  return (

    <PanelBody
      title={__('Border options', 'newsletter-glue')}
      initialOpen={true}
      className="ngl-panel-body"
    >

      <PanelRow>
        <RangeControl
          label={__('Border radius (pixels)', 'newsletter-glue')}
          value={attributes.border_radius}
          onChange={(value) => setAttributes({ border_radius: value })}
          min={0}
          max={50}
          allowReset={true}
          resetFallbackValue={0}
        />
      </PanelRow>

      <PanelRow>
        <RangeControl
          label={__('Border thickness (pixels)', 'newsletter-glue')}
          value={attributes.border_size}
          onChange={(value) => setAttributes({ border_size: value })}
          min={0}
          max={20}
          allowReset={true}
          resetFallbackValue={0}
        />
      </PanelRow>

      {attributes.border_size > 0 &&
        <PanelRow>
          <BaseControl
            label={__('Border style', 'newsletter-glue')}
            id="ngl-select-border"
          >
            <Select
              name="ngl-select-border"
              inputId="ngl-select-border"
              classNamePrefix="ngl"
              value={attributes.border_style}
              defaultValue={{ label: __('Solid', 'newsletter-glue'), value: 'solid' }}
              options={[
                { label: __('Solid', 'newsletter-glue'), value: 'solid' },
                { label: __('Dashed', 'newsletter-glue'), value: 'dashed' },
                { label: __('Dotted', 'newsletter-glue'), value: 'dotted' },
              ]}
              onChange={
                (selected) => {
                  setAttributes({ border_style: selected });
                }
              }
            />
          </BaseControl>
        </PanelRow>
      }

      {attributes.show_image &&
        <PanelRow>
          <RangeControl
            label={__('Image border radius (pixels)', 'newsletter-glue')}
            value={attributes.image_radius}
            onChange={(value) => setAttributes({ image_radius: value })}
            min={0}
            max={50}
            allowReset={true}
            resetFallbackValue={0}
          />
        </PanelRow>
      }

    </PanelBody>

  );
}