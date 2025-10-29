import apiFetch from '@wordpress/api-fetch';
import { BaseControl, Button, Card, CardBody, CardHeader, PanelBody, PanelRow, SelectControl, __experimentalSpacer as Spacer, __experimentalToggleGroupControl as ToggleGroupControl, __experimentalToggleGroupControlOption as ToggleGroupControlOption } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import React, { useEffect } from 'react';
import Select from 'react-select';

export const LpFilterOptions = props => {

  const { attributes, setAttributes } = props;

  // Authors.
  const authors = useSelect((select) => {
    return select('core').getUsers({ who: 'authors', per_page: -1 });
  }, []);

  const authorData = [];
  if (authors) {
    authors.forEach((author) => {
      authorData.push({ label: author.name, value: author.id });
    });
  }

  // Post types.
  const post_types = useSelect((select) => {
    return select('core').getPostTypes({ per_page: -1, public: true });
  }, []);

  const cptData = [];
  if (post_types) {
    post_types.forEach((post_type) => {
      cptData.push({ label: post_type.name, value: post_type.slug });
    });
  }

  // Categories.
  const categories = useSelect((select) => {
    return select('core').getEntityRecords('taxonomy', 'category', { per_page: -1 });
  }, []);

  const categoryData = [];
  if (categories) {
    categories.forEach((category) => {
      categoryData.push({ label: category.name, value: category.id });
    });
  }

  // Tags.
  const tags = useSelect((select) => {
    return select('core').getEntityRecords('taxonomy', 'post_tag', { per_page: -1 });
  }, []);

  const tagData = [];
  if (tags) {
    tags.forEach((tag) => {
      tagData.push({ label: tag.name, value: tag.id });
    });
  }

  const taxes = useSelect((select) => {
    return select('core').getTaxonomies({ per_page: -1, public: true });
  }, []);
  const taxListData = [];
  taxListData.push({ label: 'Select...', value: '' });
  if (taxes) {
    taxes.forEach((item) => {
      taxListData.push({ label: item.labels.singular_name, value: item.slug });
    });
  }

  const setCondition = (position, condition, isDelete = false) => {
    const conditions = [...attributes.taxonomies];

    if (isDelete) {
      conditions.splice(position, 1);
    } else {
      conditions[position] = condition;
    }
    setAttributes({
      taxonomies: conditions
    });
  }

  useEffect(() => {

  }, []);

  useEffect(() => {

    var thetaxes = attributes.taxonomies;

    if (thetaxes.length) {
      thetaxes.forEach((thetax) => {
        if (thetax.key.trim() != "" && attributes[`${thetax.key}_terms`].length == 0) {

          apiFetch({
            path: 'newsletterglue/' + nglue_backend.api_version + '/get_terms?taxonomy=' + thetax.key,
            method: 'get',
            headers: {
              'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
            }
          }).then(response => {

            setAttributes({ [`${thetax.key}_terms`]: response });

          });

        }
      });
    }

  }, [attributes.taxonomies]);

  return (

    <PanelBody
      title={__('Filter options', 'newsletter-glue')}
      initialOpen={true}
      className="ngl-panel-body"
    >

      <PanelRow>
        <ToggleGroupControl
          value={attributes.filter}
          onChange={(newValue) => setAttributes({ filter: newValue })}
          isBlock
        >
          <ToggleGroupControlOption
            value="include"
            label={__('Include')}
          />
          <ToggleGroupControlOption
            value="exclude"
            label={__('Exclude')}
          />
        </ToggleGroupControl>
      </PanelRow>

      <p className="components-base-control__help">
        {attributes.filter === 'exclude' ? <>The queries you select below will be <b>excluded</b> from the latest posts block.</> : <>The queries you select below will be <b>included</b> in the latest posts block.</>}
      </p>

      <PanelRow>
        <BaseControl
          label={__('Post types', 'newsletter-glue')}
          id="ngl-select-cpts"
        >
          <Select
            isMulti
            isClearable={false}
            name="ngl-select-cpts"
            inputId="ngl-select-cpts"
            classNamePrefix="ngl"
            value={attributes.filter_cpts}
            options={cptData}
            onChange={
              (selected) => {
                setAttributes({ filter_cpts: selected });
              }
            }
          />
        </BaseControl>
      </PanelRow>

      <PanelRow>
        <BaseControl
          label={__('Categories', 'newsletter-glue')}
          id="ngl-select-categories"
        >
          <Select
            isMulti
            isClearable={false}
            name="ngl-select-categories"
            inputId="ngl-select-categories"
            classNamePrefix="ngl"
            value={attributes.filter_categories}
            options={categoryData}
            onChange={
              (selected) => {
                setAttributes({ filter_categories: selected });
              }
            }
          />
        </BaseControl>
      </PanelRow>

      <PanelRow>
        <BaseControl
          label={__('Tags', 'newsletter-glue')}
          id="ngl-select-tags"
        >
          <Select
            isMulti
            isClearable={false}
            name="ngl-select-tags"
            inputId="ngl-select-tags"
            classNamePrefix="ngl"
            value={attributes.filter_tags}
            options={tagData}
            onChange={
              (selected) => {
                setAttributes({ filter_tags: selected });
              }
            }
          />
        </BaseControl>
      </PanelRow>

      <PanelRow>
        <BaseControl
          label={__('Authors', 'newsletter-glue')}
          id="ngl-select-authors"
        >
          <Select
            isMulti
            isClearable={false}
            name="ngl-select-authors"
            inputId="ngl-select-authors"
            classNamePrefix="ngl"
            value={attributes.filter_authors}
            options={authorData}
            onChange={
              (selected) => {
                setAttributes({ filter_authors: selected });
              }
            }
          />
        </BaseControl>
      </PanelRow>

      <PanelRow>
        <BaseControl
          label={__('Custom taxonomies', 'newsletter-glue')}
          id="ngl-select-taxonomies"
          className="ngl-conditional-content ngl-cond"
        >

          {attributes.taxonomies.map((condition, i) => {
            let setConditionTitle = 'Set taxonomy';

            if (condition.key && taxes) {
              let title = condition.key;
              taxes.forEach((item) => {
                if (item.slug === title) {
                  title = item.labels.singular_name;
                }
              });
              setConditionTitle = title;
            }

            return (
              <Card key={`ngl-condition-${i}`} className={`ngl-condition-${i}`}>
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
                    isSmall={true}
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
                    onChange={(value) => {
                      setCondition(i, {
                        key: value,
                        term: '',
                      });
                    }}
                    options={taxListData}
                  />

                  {condition.key && attributes[`${condition.key}_terms`] &&
                    <SelectControl
                      value={condition.term}
                      onChange={(value) => {
                        setCondition(i, {
                          key: condition.key,
                          term: value,
                        });
                      }}
                      options={attributes[`${condition.key}_terms`]}
                    />
                  }

                </CardBody>
              </Card>
            )
          })
          }

          <Spacer margin={0} marginBottom={2} />

          <Button
            text='Add new taxonomy'
            isSecondary={true}
            className='btn-add-condition'
            onClick={() => {
              const conditions = attributes.taxonomies;
              const tempCond = {
                key: '',
                term: '',
              };
              setAttributes({
                taxonomies: [...conditions, tempCond]
              });
            }}
          />
        </BaseControl>
      </PanelRow>

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