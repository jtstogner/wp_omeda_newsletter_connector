import React from 'react';

import {
  Component,
} from '@wordpress/element';

import {
  BaseControl,
  FlexItem,
  __experimentalHeading as Heading,
  __experimentalHStack as HStack,
  __experimentalNavigatorBackButton as NavigatorBackButton,
  __experimentalNavigatorScreen as NavigatorScreen,
  PanelBody,
  __experimentalVStack as VStack,
} from '@wordpress/components';

export default class NGPaletteScreen extends Component {

  constructor(props) {

    super(props);

  }

  render() {

    const { path, title, description } = this.props;

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
          <p className="edit-site-global-styles-header__description">
            {description}
          </p>
        </VStack>

        <PanelBody className="edit-site-typography-panel">
          <BaseControl>

          </BaseControl>
        </PanelBody>

      </NavigatorScreen>
    );

  }

}