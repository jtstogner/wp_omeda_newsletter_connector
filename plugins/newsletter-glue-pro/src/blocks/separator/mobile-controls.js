import {
  Flex,
  FlexBlock,
  PanelBody,
  __experimentalToolsPanel as ToolsPanel,
  __experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';
import { units } from '../../defaults/units.js';

export const MobileControls = props => {

  const { attributes, setAttributes } = props;

  const resetSpacing = () => {
    setAttributes({
      mobile_padding: theme.mobile.separator.padding,
    });
  };

  const spacingSettings = [
    { value: 'mobile_padding', label: 'Padding', default: theme.mobile.separator.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <PanelBody title={__('Settings')}>
        <Flex>
          <FlexBlock>
            <UnitControl
              label={__('Width')}
              value={attributes.mobile_width}
              onChange={(nextWidth) =>
                setAttributes({ mobile_width: nextWidth })
              }
              units={units}
            />
          </FlexBlock>
          <FlexBlock>
            <UnitControl
              label={__('Height')}
              value={attributes.mobile_height}
              onChange={(nextHeight) =>
                setAttributes({ mobile_height: nextHeight })
              }
              units={units}
            />
          </FlexBlock>
        </Flex>
      </PanelBody>
      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}