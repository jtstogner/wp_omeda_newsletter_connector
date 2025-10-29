import React from 'react';

import {
  Component,
} from '@wordpress/element';

export default class Loader extends Component {

  render() {

    return <span className={`nglue-preloader ${this.props.isAlt ? 'alt' : ''}`}>
      <span />
      <span />
      <span />
    </span>

  }

}