/**
 * External dependencies
 */
import { updateCategory } from '@wordpress/blocks';
import { ngIcon } from '../common/icons';

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, PanelRow, TabPanel, ToggleControl } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { dispatch, useSelect } from '@wordpress/data';
import { addFilter } from '@wordpress/hooks';

import { Icon, desktop, mobile } from '@wordpress/icons';

import { BlockCopy } from '../components/block-copy.js';

import { blocksWithoutHideOptions } from '../defaults/blocks-no-visibility.js';

import { ShowConditions } from './show-conditions';

updateCategory( 'newsletterglue-blocks', { icon: <Icon icon={ ngIcon } size={20} /> } );

const addTabs = createHigherOrderComponent((BlockEdit) => {
  // eslint-disable-next-line react/display-name
  return (props) => {
    const { name, attributes } = props;

    let showCondition = false;
    showCondition = name.indexOf('newsletterglue/') !== -1 && !['newsletterglue/list-item'].includes(name) && attributes.viewport;

    if (name === 'newsletterglue/image' && !attributes.url) {
      showCondition = false;
    }

    if (name === 'newsletterglue/sections' && !attributes.layout) {
      showCondition = false;
    }

    if (name === 'newsletterglue/embed' && !attributes.content) {
      showCondition = false;
    }

    const { deviceType } = useSelect(select => {
      const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
      return { deviceType: getDeviceType() }
    }, []);

    if (showCondition) {
      return (
        <>
          <InspectorControls>
            <TabPanel
              className="block-editor-block-inspector__tabs"
              activeClass="is-active"
              onSelect={(tab) => {
                dispatch('core/editor').setDeviceType(tab);
              }}
              initialTabName={deviceType === 'Mobile' ? 'Mobile' : 'Desktop'}
              tabs={[
                {
                  name: 'Desktop',
                  title: 'Desktop',
                  className: 'block-editor-block-inspector__tab-item',
                  icon: desktop
                },
                {
                  name: 'Mobile',
                  title: 'Mobile',
                  className: 'block-editor-block-inspector__tab-item',
                  icon: mobile
                },
              ]}
            >
              {() => { }}
            </TabPanel>
          </InspectorControls>
          <BlockEdit {...props} />
        </>
      );
    }
    return <BlockEdit {...props} />;
  };

}, 'withInspectorControl');

const addCopyFeature = createHigherOrderComponent((BlockEdit) => {
  // eslint-disable-next-line react/display-name
  return (props) => {
    const { name, attributes } = props;

    let showCondition = false;
    showCondition = name.indexOf('newsletterglue/') !== -1 && !['newsletterglue/list-item', 'newsletterglue/social-icon'].includes(name);

    if (name === 'newsletterglue/image' && !attributes.url) {
      showCondition = false;
    }

    if (name === 'newsletterglue/sections' && !attributes.layout) {
      showCondition = false;
    }

    if (name === 'newsletterglue/embed' && !attributes.content) {
      showCondition = false;
    }

    if (showCondition) {
      return (
        <>
          <BlockEdit {...props} />
          <InspectorControls>
            <PanelBody>
              <BlockCopy {...props} />
            </PanelBody>
          </InspectorControls>
        </>
      );
    }

    return <BlockEdit {...props} />;
  };

}, 'withInspectorControl');

const addVisibilitySettings = createHigherOrderComponent((BlockEdit) => {
  // eslint-disable-next-line react/display-name
  return (props) => {
    const { attributes, setAttributes, name } = props;

    const { deviceType } = useSelect(select => {
      const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
      return { deviceType: getDeviceType() }
    }, []);

    let showCondition = false;
    showCondition = name.indexOf('newsletterglue/') !== -1 && !blocksWithoutHideOptions.includes(name) && deviceType !== 'Mobile';

    if (name === 'newsletterglue/image' && !attributes.url) {
      showCondition = false;
    }

    if (name === 'newsletterglue/sections' && !attributes.layout) {
      showCondition = false;
    }

    if (name === 'newsletterglue/embed' && !attributes.content) {
      showCondition = false;
    }

    if (name === 'newsletterglue/social-icon') {
      showCondition = false;
    }

    const emailHelp = name === 'newsletterglue/optin' ? __('Only heading, description and button will be displayed in email newsletter. When clicked, button will take user to the form on the page.') : false;

    if (showCondition) {
      return (
        <>
          <BlockEdit {...props} />
          <InspectorControls>
            <PanelBody
              title={__('Show/hide block', 'newsletter-glue')}
              initialOpen={true}
              className="ngl-panel-body"
            >
              <PanelRow>
                <ToggleControl
                  label={__('Show in blog post', 'newsletter-glue')}
                  checked={attributes.show_in_web}
                  onChange={(value) => {
                    setAttributes({ show_in_web: value });
                  }}
                />
              </PanelRow>
              <PanelRow>
                <ToggleControl
                  label={__('Show in email newsletter ', 'newsletter-glue')}
                  checked={attributes.show_in_email}
                  help={emailHelp}
                  onChange={(value) => {
                    setAttributes({ show_in_email: value });
                  }}
                />
              </PanelRow>
              {['newsletterglue/container', 'newsletterglue/showhide'].includes(name) && (
                <ShowConditions attributes={attributes} setAttributes={setAttributes} />
              )}
            </PanelBody>
          </InspectorControls>
        </>
      );
    }
    return <BlockEdit {...props} />;
  };
}, 'withInspectorControl');

addFilter(
  'editor.BlockEdit',
  'newsletterglue/add-tabs',
  addTabs
);

addFilter(
  'editor.BlockEdit',
  'newsletterglue/add-copy-feature',
  addCopyFeature
);

addFilter(
  'editor.BlockEdit',
  'newsletterglue/add-visibility-settings',
  addVisibilitySettings
);