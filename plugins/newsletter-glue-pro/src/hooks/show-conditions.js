import React from 'react';

import {
  BaseControl,
  Button,
  ButtonGroup,
  Card,
  CardBody,
  CardHeader,
  __experimentalRadio as Radio,
  __experimentalRadioGroup as RadioGroup,
  SelectControl,
  __experimentalSpacer as Spacer,
  TextControl
} from '@wordpress/components';

import { useState } from '@wordpress/element';

export function ShowConditions(props) {

  const { attributes, setAttributes } = props;

  const showconditions = newsletterglue_block_show_hide_content.showconditions;
  const operators = JSON.parse(newsletterglue_block_show_hide_content.operators);
  const operatorsForTag = operators.filter(operator => operator.for.split(',').includes('tag'));
  const operatorsForField = operators.filter(operator => operator.for.split(',').includes('field'));

  const [loading, setLoading] = useState(false);
  const [tags, setTags] = useState([]);
  const [fields, setFields] = useState([]);

  if (tags.length === 0 && newsletterglue_meta?.custom_tags?.length) {
    setTags(newsletterglue_meta.custom_tags);
  }

  if (fields.length === 0 && newsletterglue_meta?.custom_fields?.length) {
    setFields(newsletterglue_meta.custom_fields);
  }

  let app = newsletterglue_meta?.app;
  let conditionalContent = () => { };

  const setCondition = (position, condition, isDelete = false) => {
    const conditions = [...attributes[`${app}_conditions`]];

    if (isDelete) {
      conditions.splice(position, 1);
    } else {
      conditions[position] = condition;
    }
    setAttributes({
      [`${app}_conditions`]: conditions
    });
  }

  if (attributes.show_in_email && showconditions) {

    conditionalContent = () => {
      return (
        <BaseControl
          label='Conditional content'
          className={`ngl-conditional-content ${loading ? 'is-loading' : ''}`}
          help='Note: Preview in browser will always display this content. Send a test campaign to test conditional content.'
        >
          <BaseControl label='Show email content based on these conditions:'>
            {attributes[`${app}_conditions`].map((condition, i) => {

              let setConditionTitle = 'Set condition';

              if (condition.key == 'tag') {
                if (condition.value) {
                  setConditionTitle = 'Tag: ' + condition.value;
                }
              } else if (condition.key == 'manual') {
                if (condition.key_manual) {
                  setConditionTitle = condition.key_manual;
                }
              } else if (condition.key) {
                let title = condition.key;
                if (app == 'aweber') {
                  if (title.includes('subscriber.')) {
                    title = title.replace('subscriber.', '')
                  }

                  if (title.includes('custom_field')) {
                    title = title.replace('custom_field["', '').replace('"]', '');
                  }
                }
                setConditionTitle = title;
              }

              return (
                <Card key={`nglcard-${i}`} className={`ngl-condition-${i}`}>
                  <CardHeader
                    onClick={(e) => {
                      const element = e.target;
                      if (!element.classList.contains('dashicon')) {
                        element.closest('.components-card').classList.toggle('is-active');
                      }
                    }}
                  >
                    <span>{setConditionTitle}</span>
                    <Button
                      isSmall
                      className='btn-remove'
                      icon='no'
                      onClick={() => {
                        setCondition(i, {}, true);
                      }}
                    />
                  </CardHeader>
                  <CardBody>
                    <SelectControl
                      value={condition.key}
                      options={fields}
                      onChange={(value) => {
                        setCondition(i, {
                          key: value,
                          key_manual: '',
                          operator: '',
                          value: '',
                          relationship: condition.relationship
                        });
                      }}
                    />

                    {condition.key == 'manual' && (
                      <TextControl
                        placeholder="Enter your field key"
                        value={condition.key_manual}
                        onChange={(value) => {
                          setCondition(i, {
                            key: condition.key,
                            key_manual: value,
                            operator: '',
                            value: '',
                            relationship: condition.relationship
                          });
                        }}
                      />
                    )}

                    {condition.key && ((condition.key == 'manual' && condition.key_manual) || (condition.key != 'manual')) && (
                      <SelectControl
                        value={condition.operator}
                        options={condition.key == 'tag' ? operatorsForTag : condition.key == 'manual' ? operators : operatorsForField}
                        onChange={(value) => {
                          setCondition(i, {
                            key: condition.key,
                            key_manual: condition.key_manual,
                            operator: value,
                            value: '',
                            relationship: condition.relationship
                          });
                        }}
                      />
                    )}

                    {condition.key &&
                      condition.key != 'tag' &&
                      condition.operator &&
                      condition.operator != 'and' &&
                      condition.operator != 'or' &&
                      ((condition.key == 'manual') || (condition.operator != 'ex' && condition.operator != 'nex')) && (
                        <TextControl
                          placeholder="Enter your value"
                          help={condition.key == 'manual' ? "For multiple values use exactly the same pattern. List: value1, value2, value3" : ""}
                          value={condition.value}
                          onChange={(value) => {
                            setCondition(i, {
                              key: condition.key,
                              key_manual: condition.key_manual,
                              operator: condition.operator,
                              value,
                              relationship: condition.relationship
                            });
                          }}
                        />
                      )}

                    {condition.key &&
                      condition.key != 'tag' &&
                      condition.operator &&
                      (condition.operator == 'and' || condition.operator == 'or') && (
                        <SelectControl
                          value={condition.value}
                          options={fields}
                          onChange={(value) => {
                            setCondition(i, {
                              key: condition.key,
                              key_manual: condition.key_manual,
                              operator: condition.operator,
                              value,
                              relationship: condition.relationship
                            });
                          }}
                        />
                      )}

                    {condition.key &&
                      condition.operator &&
                      condition.key == 'tag' && (
                        <SelectControl
                          multiple
                          value={condition.value}
                          style={{ height: 'auto' }}
                          options={tags}
                          onChange={(value) => {
                            setCondition(i, {
                              key: condition.key,
                              key_manual: condition.key_manual,
                              operator: condition.operator,
                              value,
                              relationship: condition.relationship
                            });
                          }}
                        />
                      )}

                    <BaseControl>
                      <RadioGroup
                        checked={condition.relationship}
                        onChange={(value) => {
                          setCondition(i, {
                            key: condition.key,
                            key_manual: condition.key_manual,
                            operator: condition.operator,
                            value: condition.value,
                            relationship: value
                          });
                        }}
                      >
                        <Radio value='AND' />
                        {!['campaignmonitor', 'mailchimp', 'moosend', 'sendgrid'].includes(app) && (
                          <Radio value='OR' />
                        )}
                      </RadioGroup>
                    </BaseControl>

                  </CardBody>
                </Card>
              );
            })}
          </BaseControl>
          <BaseControl>
            <ButtonGroup className='ngl-gutenberg--fullwidth'>
              <Button
                text='Add new condition'
                variant='secondary'
                className='btn-add-condition'
                onClick={() => {
                  const conditions = attributes[`${app}_conditions`];
                  const tempCond = {
                    key: '',
                    key_manual: '',
                    operator: '',
                    value: '',
                    relationship: 'AND'
                  };
                  setAttributes({
                    [`${app}_conditions`]: [...conditions, tempCond]
                  });
                }}
              />
              <Button
                text='Refresh'
                variant='secondary'
                showTooltip
                label='Fetch tags/custom fields from API'
                className='btn-refresh'
                style={{ justifyContent: 'flex-end' }}
                onClick={async () => {
                  setLoading(true);
                  await fetch(newsletterglue_params.ajaxurl, {
                    method: 'POST',
                    body: new URLSearchParams({
                      action: 'newsletterglue_block_show_hide_refresh',
                      security: newsletterglue_params.ajaxnonce,
                    }),
                  })
                    .then(response => response.json())
                    .then(response => {
                      setLoading(false);
                      if (response.success) {
                        if (response.data?.custom_tag_list?.length) {
                          setTags(response.data.custom_tag_list);
                        }
                        if (response.data?.custom_field_list?.length) {
                          setFields(response.data.custom_field_list);
                        }
                      } else {
                        alert(response.message);
                      }
                    });
                }}
              />
            </ButtonGroup>
          </BaseControl>
        </BaseControl>
      );
    };
  }

  return (
    <>
      <Spacer marginBottom={4} />
      {conditionalContent()}
    </>
  )
}