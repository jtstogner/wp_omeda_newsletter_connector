import React from 'react';

import {
  BaseControl,
  Button,
  PanelBody,
  PanelRow,
  RangeControl,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import Select from 'react-select';

export const LpLayoutOptions = props => {

  const { attributes, setAttributes } = props;

  const weekDays = [
    { label: __('Sunday', 'newsletter-glue'), value: 'Sunday' },
    { label: __('Monday', 'newsletter-glue'), value: 'Monday' },
    { label: __('Tuesday', 'newsletter-glue'), value: 'Tuesday' },
    { label: __('Wednesday', 'newsletter-glue'), value: 'Wednesday' },
    { label: __('Thursday', 'newsletter-glue'), value: 'Thursday' },
    { label: __('Friday', 'newsletter-glue'), value: 'Friday' },
    { label: __('Saturday', 'newsletter-glue'), value: 'Saturday' },
  ];

  const times = [
    { label: __('12:00 PM', 'newsletter-glue'), value: '12pm' },
    { label: __('01:00 PM', 'newsletter-glue'), value: '1pm' },
    { label: __('02:00 PM', 'newsletter-glue'), value: '2pm' },
    { label: __('03:00 PM', 'newsletter-glue'), value: '3pm' },
    { label: __('04:00 PM', 'newsletter-glue'), value: '4pm' },
    { label: __('05:00 PM', 'newsletter-glue'), value: '5pm' },
    { label: __('06:00 PM', 'newsletter-glue'), value: '6pm' },
    { label: __('07:00 PM', 'newsletter-glue'), value: '7pm' },
    { label: __('08:00 PM', 'newsletter-glue'), value: '8pm' },
    { label: __('09:00 PM', 'newsletter-glue'), value: '9pm' },
    { label: __('10:00 PM', 'newsletter-glue'), value: '10pm' },
    { label: __('11:00 PM', 'newsletter-glue'), value: '11pm' },
    { label: __('12:00 AM', 'newsletter-glue'), value: '12am' },
    { label: __('01:00 AM', 'newsletter-glue'), value: '1am' },
    { label: __('02:00 AM', 'newsletter-glue'), value: '2am' },
    { label: __('03:00 AM', 'newsletter-glue'), value: '3am' },
    { label: __('04:00 AM', 'newsletter-glue'), value: '4am' },
    { label: __('05:00 AM', 'newsletter-glue'), value: '5am' },
    { label: __('06:00 AM', 'newsletter-glue'), value: '6am' },
    { label: __('07:00 AM', 'newsletter-glue'), value: '7am' },
    { label: __('08:00 AM', 'newsletter-glue'), value: '8am' },
    { label: __('09:00 AM', 'newsletter-glue'), value: '9am' },
    { label: __('10:00 AM', 'newsletter-glue'), value: '10am' },
    { label: __('11:00 AM', 'newsletter-glue'), value: '11am' },
  ];

  return (

    <PanelBody
      title={__('Content options', 'newsletter-glue')}
      initialOpen={true}
      className="ngl-panel-body"
    >

      <PanelRow>
        <ToggleGroupControl
          label={__('Content style')}
          value={attributes.contentstyle}
          onChange={(newValue) => setAttributes({ contentstyle: newValue })}
          isBlock
        >
          <ToggleGroupControlOption
            value="single"
            label={__('Single post')}
          />
          <ToggleGroupControlOption
            value="multi"
            label={__('Multiple posts')}
          />
        </ToggleGroupControl>
      </PanelRow>

      {!attributes.insertRssPosts && (
        <>
          <PanelRow>
            <BaseControl
              label={__('Sort by', 'newsletter-glue')}
              id="ngl-select-sortby"
            >
              <Select
                name="ngl-select-sortby"
                inputId="ngl-select-sortby"
                classNamePrefix="ngl"
                value={attributes.sortby}
                defaultValue={{ label: __('Newest to oldest', 'newsletter-glue'), value: 'newest' }}
                options={[
                  { label: __('Newest to oldest', 'newsletter-glue'), value: 'newest' },
                  { label: __('Oldest to newest', 'newsletter-glue'), value: 'oldest' },
                  { label: __('A-Z', 'newsletter-glue'), value: 'alphabetic' },
                ]}
                onChange={
                  (selected) => {
                    setAttributes({ sortby: selected });
                  }
                }
              />
            </BaseControl>
          </PanelRow>

          <PanelRow>
            <BaseControl
              label={__('Display latest posts from', 'newsletter-glue')}
              id="ngl-select-dates"
            >
              <Select
                name="ngl-select-dates"
                inputId="ngl-select-dates"
                classNamePrefix="ngl"
                value={attributes.dates}
                isClearable
                options={[
                  { label: __('Last 24 hours', 'newsletter-glue'), value: 'last_1' },
                  { label: __('Last 2 days', 'newsletter-glue'), value: 'last_2' },
                  { label: __('Last 3 days', 'newsletter-glue'), value: 'last_3' },
                  { label: __('Last 4 days', 'newsletter-glue'), value: 'last_4' },
                  { label: __('Last 5 days', 'newsletter-glue'), value: 'last_5' },
                  { label: __('Last 6 days', 'newsletter-glue'), value: 'last_6' },
                  { label: __('Last 7 days', 'newsletter-glue'), value: 'last_7' },
                  { label: __('Last 14 days', 'newsletter-glue'), value: 'last_14' },
                  { label: __('Last 30 days', 'newsletter-glue'), value: 'last_30' },
                  { label: __('Last 60 days', 'newsletter-glue'), value: 'last_60' },
                ]}
                onChange={
                  (selected) => {
                    setAttributes({ dates: selected });
                  }
                }
              />
              {attributes.dates && attributes.dates.value === 'this_month' &&
                <p className="components-base-control__help" style={{ marginTop: '6px' }}>
                  Month starts on the first day of the month at midnight.
                </p>
              }
            </BaseControl>
          </PanelRow>
        </>
      )}
      {attributes.dates && attributes.dates.value == 'this_week' &&
        <PanelRow>
          <BaseControl
            label={__('Week starts', 'newsletter-glue')}
            id="ngl-select-week-start"
          >
            <div className="ngl-select-flex">
              <Select
                name="ngl-select-week-start"
                inputId="ngl-select-week-start"
                classNamePrefix="ngl"
                value={attributes.week_starts}
                defaultValue={{ label: __('Monday', 'newsletter-glue'), value: 'Monday' }}
                options={weekDays}
                onChange={
                  (selected) => {
                    setAttributes({ week_starts: selected });
                  }
                }
              />
              <Select
                name="ngl-select-start-time"
                inputId="ngl-select-start-time"
                classNamePrefix="ngl"
                value={attributes.starts_time}
                options={times}
                defaultValue={{ label: __('7:00 PM', 'newsletter-glue'), value: '7pm' }}
                onChange={
                  (selected) => {
                    setAttributes({ starts_time: selected });
                  }
                }
              />
            </div>
          </BaseControl>
        </PanelRow>
      }

      {attributes.dates && attributes.dates.value == 'today' &&
        <PanelRow>
          <BaseControl
            label={__('Day starts', 'newsletter-glue')}
            id="ngl-select-week-start"
          >
            <Select
              name="ngl-select-start-time"
              inputId="ngl-select-start-time"
              classNamePrefix="ngl"
              value={attributes.starts_time}
              options={times}
              defaultValue={{ label: __('7:00 PM', 'newsletter-glue'), value: '7pm' }}
              onChange={
                (selected) => {
                  setAttributes({ starts_time: selected });
                }
              }
            />
          </BaseControl>
        </PanelRow>
      }

      <PanelRow style={{ marginTop: '0px' }}>
        <p className="components-base-control__help" style={{ marginTop: '0px' }}>
          The current time on this site is <b>{nglue_backend.wp_time}</b>. <a href={nglue_backend.wp_general}>Update time</a>.
        </p>
      </PanelRow>

      {attributes.dates && attributes.dates.value == 'two_weeks' &&
        <PanelRow>
          <BaseControl
            label={__('2 weeks starts', 'newsletter-glue')}
            id="ngl-select-week-start"
          >
            <div className="ngl-select-flex">
              <Select
                name="ngl-select-month-start"
                inputId="ngl-select-month-start"
                classNamePrefix="ngl"
                value={attributes.two_weeks_starts}
                defaultValue={{ label: __('Monday', 'newsletter-glue'), value: 'Monday' }}
                options={weekDays}
                onChange={
                  (selected) => {
                    setAttributes({ two_weeks_starts: selected });
                  }
                }
              />
              <Select
                name="ngl-select-start-time"
                inputId="ngl-select-start-time"
                classNamePrefix="ngl"
                value={attributes.starts_time}
                options={times}
                defaultValue={{ label: __('7:00 PM', 'newsletter-glue'), value: '7pm' }}
                onChange={
                  (selected) => {
                    setAttributes({ starts_time: selected });
                  }
                }
              />
            </div>
          </BaseControl>
        </PanelRow>
      }

      {attributes.contentstyle == 'multi' &&
        <PanelRow>
          <RangeControl
            label={__('Number of posts to display', 'newsletter-glue')}
            value={attributes.posts_num}
            onChange={(value) => setAttributes({ posts_num: value })}
            min={0}
            max={20}
            help={__('When set to 0, all eligible posts will be displayed.', 'newsletter-glue')}
          />
        </PanelRow>
      }

      {attributes.contentstyle == 'single' && <>
        <PanelRow>
          <ToggleGroupControl
            label={__('Post length')}
            value={attributes.postlength}
            onChange={(newValue) => setAttributes({ postlength: newValue })}
            isBlock
          >
            <ToggleGroupControlOption
              value="excerpt"
              label={__('Excerpt')}
            />
            <ToggleGroupControlOption
              value="full"
              label={__('Full post')}
            />
          </ToggleGroupControl>
        </PanelRow>
      </>
      }

      {(!attributes.insertRssPosts && (attributes.postlength == 'excerpt' || attributes.contentstyle == 'multi')) &&
        <PanelRow>
          <RangeControl
            label={__('Number of words to display', 'newsletter-glue')}
            value={attributes.words_num}
            onChange={(value) => setAttributes({ words_num: value })}
            min={0}
            max={200}
            help={__('When set to 0, the full post will be displayed.', 'newsletter-glue')}
          />
        </PanelRow>
      }

      <div className="ngl-gb-box">
        <div className="ngl-gb-box-div">
          <Button
            label={__('Update block', 'newsletter-glue')}
            variant="primary"
            onClick={() => {
              setAttributes({ update_posts: Math.random() * 10 });
            }}
          >{__('Update block', 'newsletter-glue')}</Button>
        </div>
      </div>

    </PanelBody>

  );
}