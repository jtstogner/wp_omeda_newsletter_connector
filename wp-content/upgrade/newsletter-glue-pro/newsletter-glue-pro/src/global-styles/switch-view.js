import { __ } from '@wordpress/i18n';
import React from 'react';

import {
  Component,
} from '@wordpress/element';

import {
  Button,
} from '@wordpress/components';

export default class NGSwitchView extends Component {

  constructor(props) {

    super(props);

  }

  render() {

    const { isMobile } = this.props.getState;

    return (
      <Button variant="secondary"
        onClick={() => {
          this.props.handleChange('switchView');
        }}
      >
        {!isMobile && __('Switch to mobile view', 'newsletter-glue')}
        {isMobile && __('Switch to desktop view', 'newsletter-glue')}
      </Button>
    );

  }

}