import {
  Flex,
  FlexBlock,
  PanelBody,
  __experimentalToolsPanel as ToolsPanel,
  __experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React from 'react';

import { ColorSettingsPane } from '../../components/colors.js';
import { SettingsPane } from '../../components/settings.js';

import { theme } from '../../defaults/theme.js';
import { units } from '../../defaults/units.js';

export const Controls = props => {

  const { attributes, setAttributes } = props;

  const resetColors = () => {
    setAttributes({
      background: undefined,
      color: undefined,
    });
  };

  const resetSpacing = () => {
    setAttributes({
      padding: theme.separator.padding,
    });
  };

  const colors = [
    { value: 'color', label: 'Color' },
    { value: 'background', label: 'Background' },
  ];

  const spacingSettings = [
    { value: 'padding', label: 'Padding', default: theme.separator.padding, type: 'boxcontrol' },
  ];

  return (
    <>
      <PanelBody title={__('Settings')}>
        <Flex>
          <FlexBlock>
            <UnitControl
              label={__('Width')}
              value={attributes.width}
              onChange={(nextWidth) =>
                setAttributes({ width: nextWidth })
              }
              units={units}
            />
          </FlexBlock>
          <FlexBlock>
            <UnitControl
              label={__('Height')}
              value={attributes.height}
              onChange={(nextHeight) =>
                setAttributes({ height: nextHeight })
              }
              units={units}
            />
          </FlexBlock>
        </Flex>
      </PanelBody>

      <ToolsPanel label={__('Separator')} resetAll={resetColors} hasInnerWrapper={true} className="color-block-support-panel">
        <ColorSettingsPane attributes={attributes} setAttributes={setAttributes} colors={colors} />
      </ToolsPanel>

      <ToolsPanel label={__('Spacing')} resetAll={resetSpacing}>
        <SettingsPane attributes={attributes} setAttributes={setAttributes} settings={spacingSettings} />
      </ToolsPanel>
    </>
  );

}