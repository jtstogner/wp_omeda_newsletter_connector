import React from 'react';

import {
  Component
} from '@wordpress/element';

import NGButtonScreen from './button-screen.js';
import NGColorScreen from './color-screen.js';
import NGPaletteScreen from './palette-screen.js';
import NGTypographyScreen from './typography-screen.js';

export default class NGScreens extends Component {

  constructor(props) {

    super(props);

  }

  render() {

    return (
      <>
        <NGTypographyScreen
          path="/typography/h1"
          title="Heading 1"
          id="h1"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGTypographyScreen
          path="/typography/h2"
          title="Heading 2"
          id="h2"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGTypographyScreen
          path="/typography/h3"
          title="Heading 3"
          id="h3"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGTypographyScreen
          path="/typography/h4"
          title="Heading 4, 5, 6"
          id="h4"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGTypographyScreen
          path="/typography/paragraph"
          title="Paragraph"
          id="p"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGTypographyScreen
          path="/typography/links"
          title="Links"
          id="a"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
          description="Set the default color used for links."
        />

        <NGButtonScreen
          path="/button"
          title="Button"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGColorScreen
          path="/colors/background"
          title="Background"
          id="email_bg"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGColorScreen
          path="/colors/content"
          title="Content area"
          id="container_bg"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGColorScreen
          path="/colors/button/background"
          title="Button background"
          id="btn_bg"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGColorScreen
          path="/colors/button/text"
          title="Button text"
          id="btn_colour"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGColorScreen
          path="/colors/button/border"
          title="Button border"
          id="btn_border"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

        <NGPaletteScreen
          path="/colors/palette"
          title="Palette"
          getState={this.props.getState}
          handleChange={this.props.handleChange}
        />

      </>
    );

  }

}