import { dispatch, select } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import React, { useEffect } from 'react';

import { PanelSettings } from '../components/panel-settings.js';

const NGSettingsPlugin = () => {

  const postType = select("core/editor").getCurrentPostType();
  if (!['newsletterglue', 'ngl_template', 'ngl_automation', 'ngl_pattern'].includes(postType)) {
    return null;
  }

  const webViewOptions = [
    { label: 'Blog post', value: 'blog' },
    { label: 'Email view', value: 'email' },
  ];

  const settings = [
    { type: 'text', label: __('Title', 'newsletter-glue'), metakey: 'title', fallback: __('New campaign', 'newsletter-glue') },
    { type: 'radio', label: __('Newsletter view', 'newsletter-glue'), help: __('Which version should people see when click on the newsletter permalink?', 'newsletter-glue'), metakey: '_webview', options: webViewOptions, is_meta: true, fallback: 'blog' }
  ];

  useEffect(() => {
    settings.map(function (item) {
      if (item.is_meta && item.fallback) {
        if (!select('core/editor').getEditedPostAttribute('meta')[item.metakey]) {
          dispatch('core/editor').editPost({ meta: { [item.metakey]: item.fallback } });
        }
      }
      if (!item.is_meta && item.fallback) {
        if (!select('core/editor').getEditedPostAttribute(item.metakey)) {
          dispatch('core/editor').editPost({ [item.metakey]: item.fallback });
        }
      }
    });
  }, []);

  return (
    <>
      <PluginDocumentSettingPanel
        name="ng-settings-panel"
        title={__('Newsletter settings')}
        className="ng-sidebar-panel"
        opened={false}
        initialOpen={true}
      >
        <PanelSettings settings={settings} />
      </PluginDocumentSettingPanel>
    </>
  );
};

registerPlugin('ng-settings-plugin', {
  render: NGSettingsPlugin,
});