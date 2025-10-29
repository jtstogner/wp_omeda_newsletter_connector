import { __ } from '@wordpress/i18n';
import React from 'react';

import apiFetch from '@wordpress/api-fetch';

import {
  PanelBody,
} from '@wordpress/components';

import {
  Component,
} from '@wordpress/element';

import Loader from '../common/loader';

export default class TemplateWizard4 extends Component {

  componentDidMount() {

    var props = this.props.state;
    var data = this.props;

    data.changeState({ isSaving: true });

    setTimeout(() => {
      apiFetch({
        path: 'newsletterglue/' + nglue_backend.api_version + '/update_patterns',
        method: 'post',
        headers: {
          'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
        },
        data: props
      }).then(() => {

        window.location.href = props.successRedirect;

      });
    }, 2000);

  }

  render() {

    return <PanelBody className="ngl-setting-up">
      <h2>{__('Customising your patterns now...', 'newsletter-glue')}</h2>
      <p>{__("We'll automatically redirect you back to Patterns when we're done.", 'newsletter-glue')}</p>
      <Loader />
    </PanelBody>

  }

}