import React from 'react';

import {
  Component,
} from '@wordpress/element';

import {
  BaseControl,
  ColorIndicator,
  Flex,
  FlexItem,
  __experimentalHeading as Heading,
  __experimentalHStack as HStack,
  __experimentalItem as Item,
  __experimentalItemGroup as ItemGroup,
  __experimentalNavigatorBackButton as NavigatorBackButton,
  __experimentalNavigatorButton as NavigatorButton,
  __experimentalNavigatorScreen as NavigatorScreen,
  PanelBody,
  RangeControl,
  __experimentalVStack as VStack,
} from '@wordpress/components';

export default class NGButtonScreen extends Component {

  constructor(props) {

    super(props);

  }

  render() {

    const { path, title } = this.props;

    const { isMobile, theme_m, theme_r } = this.props.getState;

    let theme = isMobile ? theme_m : theme_r;

    return (
      <NavigatorScreen path={path} className="edit-site-global-styles-sidebar__navigator-screen">
        <VStack>
          <HStack justify="flex-start">
            <div>
              <NavigatorBackButton
                icon={<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" className="edit-site-global-styles-icon-with-current-color" aria-hidden="true" focusable="false"><path d="M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"></path></svg>}
              ></NavigatorBackButton>
            </div>
            <FlexItem><Heading level={5}>{title}</Heading></FlexItem>
          </HStack>
        </VStack>

        <PanelBody className="edit-site-typography-panel">

          <BaseControl>
            <RangeControl
              label="Size"
              value={parseInt(theme['btn_width'])}
              onChange={(newValue) => {
                if (newValue) {
                  this.props.handleChange('setAttr', 'btn_width', newValue);
                } else {
                  this.props.handleChange('setAttr', 'btn_width', 150);
                }
              }}
              min={50}
              max={560}
              allowReset={true}
              resetFallbackValue={150}
            />
          </BaseControl>

          <BaseControl>
            <RangeControl
              label="Border radius"
              value={parseInt(theme['btn_radius'])}
              onChange={(newValue) => {
                if (newValue) {
                  this.props.handleChange('globalAttr', 'btn_radius', newValue);
                } else {
                  this.props.handleChange('globalAttr', 'btn_radius', 0);
                }
              }}
              min={0}
              max={50}
              allowReset={true}
              resetFallbackValue={0}
            />
          </BaseControl>

        </PanelBody>

        <div className="edit-site-global-styles-screen-colors">
          <VStack>
            <Heading level={2} className="edit-site-global-styles-subtitle">Customize colors</Heading>
            <ItemGroup className="nglue-group">
              <Item>
                <NavigatorButton
                  path="/colors/button/background"
                >
                  <HStack justify="flex-start">
                    <Flex className="edit-site-global-styles__color-indicator-wrapper">
                      <ColorIndicator colorValue={theme.btn_bg} />
                    </Flex>
                    <FlexItem>Background</FlexItem>
                  </HStack>
                </NavigatorButton>
              </Item>
              <Item>
                <NavigatorButton
                  path="/colors/button/text"
                >
                  <HStack justify="flex-start">
                    <Flex className="edit-site-global-styles__color-indicator-wrapper">
                      <ColorIndicator colorValue={theme.btn_colour} />
                    </Flex>
                    <FlexItem>Text</FlexItem>
                  </HStack>
                </NavigatorButton>
              </Item>
              <Item>
                <NavigatorButton
                  path="/colors/button/border"
                >
                  <HStack justify="flex-start">
                    <Flex className="edit-site-global-styles__color-indicator-wrapper">
                      <ColorIndicator colorValue={theme.btn_border} />
                    </Flex>
                    <FlexItem>Border</FlexItem>
                  </HStack>
                </NavigatorButton>
              </Item>
            </ItemGroup>
          </VStack>
        </div>

      </NavigatorScreen>
    );

  }

}