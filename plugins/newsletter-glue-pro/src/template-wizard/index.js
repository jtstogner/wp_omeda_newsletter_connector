import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import React from 'react';
import { createRoot } from 'react-dom/client';

import {
  Component,
  Fragment
} from '@wordpress/element';

import TemplateWizard4 from './finish';
import TemplateWizard2 from './logo';
import TemplateWizard1 from './personalise';
import TemplateWizard3 from './social-links';

import { settings } from './settings';

import Loader from '../common/loader';

export default class TemplateWizard extends Component {

  constructor(props) {

    super(props);

    this.changeState = this.changeState.bind(this);
    this.changeOptions = this.changeOptions.bind(this);
    this.startWizard = this.startWizard.bind(this);
    this.navStep = this.navStep.bind(this);

    const data = {
      completedSteps: 0,
      successRedirect: nglue_backend.template_page,
      isAPILoaded: false,
      isSaving: false,
      currentIndex: 1,
      showPatternWelcome: true,
      isOnboarding: false,
    };

    settings.map(function (item) {
      data[item] = '';
    });

    this.state = data;

  }

  componentDidMount() {

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/pattern_settings',
      method: 'get',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      }
    }).then(response => {

      const data = [];

      data.isAPILoaded = true;

      settings.map(function (item) {
        data[item] = response[item];
      });

      this.setState(data);

    });

  }

  changeState(newstate) {
    this.setState(newstate);
  }

  startWizard() {
    this.setState({ isSaving: false, currentIndex: 1, completedSteps: 0 });
  }

  changeOptions() {

    var nextScreen = 1;
    if (this.state.currentIndex == 1) {
      nextScreen = 2;
    }
    if (this.state.currentIndex == 2) {
      nextScreen = 3;
    }
    if (this.state.currentIndex == 3) {
      nextScreen = 4;
    }

    this.setState({
      currentIndex: nextScreen,
      completedSteps: nextScreen
    });

    const data = {};

    settings.map((item) => {
      data[item] = this.state[item];
    });

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/pattern_settings',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

    });

  }

  navStep(step) {

    if (this.state.completedSteps < step || this.state.isSaving) {
      return;
    }

    this.setState({ currentIndex: step, isSaving: false });

  }

  render() {

    var Step = this.state.currentIndex;
    var doneStep = this.state.completedSteps;

    if (!this.state.isAPILoaded) {
      return <Loader />;
    }

    return (
      <Fragment>
        <div className="ngl-wizard-steps">
          <ul className="ngl-steps ngl-steps-3">
            <li className={Step == 1 && "progress" || Step > 1 && "active" || doneStep >= 1 && "active" || undefined} data-step={1} onClick={() => this.navStep(1)}>Personalisation</li>
            <li className={Step == 2 && "progress" || Step >= 2 && "active" || doneStep >= 3 && "active" || undefined} data-step={2} onClick={() => this.navStep(2)}>Brand</li>
            <li className={Step == 3 && "progress" || Step >= 3 && "active" || doneStep >= 3 && "active" || undefined} data-step={3} onClick={() => this.navStep(3)}>Social accounts</li>
          </ul>
          <div style={{ clear: 'both' }}></div>
        </div>
        <div className="ngl-wizard">
          {Step == 1 && <TemplateWizard1 {...this} changeState={this.changeState} changeOptions={this.changeOptions} />}
          {Step == 2 && <TemplateWizard2 {...this} changeState={this.changeState} changeOptions={this.changeOptions} />}
          {Step == 3 && <TemplateWizard3 {...this} changeState={this.changeState} changeOptions={this.changeOptions} />}
          {Step == 4 && <TemplateWizard4 {...this} changeState={this.changeState} changeOptions={this.changeOptions} />}
        </div>
        <div className="ngl-wizard-links">
          {!this.state.isSaving && <a href={nglue_backend.template_page}>{__('Skip onboarding', 'newsletter-glue')}</a>}
        </div>
      </Fragment>
    );

  }

}

var rootElement = document.getElementById('nglue-template-wizard');

if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<TemplateWizard />);
}
