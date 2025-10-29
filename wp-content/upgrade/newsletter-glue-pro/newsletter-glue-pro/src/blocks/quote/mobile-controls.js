import { __experimentalToolsPanel as ToolsPanel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetTypography = () => {
    setAttributes({
      mobile_quote_lineheight: theme.mobile.quote.lineheight,
      mobile_quote_size: theme.mobile.quote.fontsize,
      mobile_quote_citesize: theme.mobile.quote.citesize,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      mobile_quote_padding: theme.mobile.quote.padding,
    });
  };

  const typographySettings = [
    { value: 'mobile_quote_size', label: __('Font size', 'newsletter-glue'), default: theme.mobile.quote.fontsize, type: 'unit' },
    { value: 'mobile_quote_citesize', label: __('Citation font size', 'newsletter-glue'), default: theme.mobile.quote.citesize, type: 'unit' },
    { value: 'mobile_quote_lineheight', label: __('Line height', 'newsletter-glue'), default: theme.mobile.quote.lineheight, type: 'number', step: 0.1, is_single: true },
  ];

  const spacingSettings = [
    { value: 'mobile_quote_padding', label: 'Padding', default: theme.mobile.quote.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <ToolsPanel label={__('Typography')} resetAll={resetTypography}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={typographySettings} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}